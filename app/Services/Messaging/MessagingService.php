<?php

namespace App\Services\Messaging;

use App\Models\Department;
use App\Models\Messaging\Conversation;
use App\Models\Messaging\ConversationParticipant;
use App\Models\Messaging\Message;
use App\Models\User;
use Illuminate\Support\Str;

class MessagingService
{
    /**
     * Create or get default channels
     */
    public function createDefaultChannels(): array
    {
        $channels = [];

        // General channel
        $channels['general'] = $this->findOrCreateChannel('general', 'Discussions générales', 'channel');
        
        // Announcements channel
        $channels['annonces'] = $this->findOrCreateChannel('annonces', 'Communications officielles RH', 'announcement');

        return $channels;
    }

    /**
     * Create department channels for all departments
     */
    public function createDepartmentChannels(): array
    {
        $channels = [];
        $departments = Department::all();

        foreach ($departments as $department) {
            $slug = Str::slug($department->name);
            $channel = $this->findOrCreateChannel(
                "dept-{$slug}",
                "Canal du département {$department->name}",
                'channel'
            );
            
            // Add all department members to the channel
            $this->syncDepartmentMembers($channel, $department);
            
            $channels[$department->id] = $channel;
        }

        return $channels;
    }

    /**
     * Find or create a channel
     */
    public function findOrCreateChannel(string $name, string $description, string $type = 'channel'): Conversation
    {
        return Conversation::firstOrCreate(
            ['name' => $name, 'type' => $type],
            [
                'description' => $description,
                'created_by' => User::where('role', 'admin')->first()?->id,
            ]
        );
    }

    /**
     * Sync department members to a channel
     */
    public function syncDepartmentMembers(Conversation $channel, Department $department): void
    {
        // Get all users in the department
        $departmentUserIds = User::where('department_id', $department->id)->pluck('id');

        // Get current participants
        $currentParticipantIds = $channel->activeParticipants()->pluck('user_id');

        // Add new members
        foreach ($departmentUserIds as $userId) {
            if (!$currentParticipantIds->contains($userId)) {
                $channel->participants()->create([
                    'user_id' => $userId,
                    'role' => 'member',
                    'joined_at' => now(),
                ]);
            }
        }
    }

    /**
     * Add user to all relevant channels when they join
     */
    public function onUserCreated(User $user): void
    {
        // Add to general channel
        $generalChannel = Conversation::where('name', 'general')->where('type', 'channel')->first();
        if ($generalChannel) {
            $this->addUserToChannel($user, $generalChannel);
        }

        // Add to announcements channel
        $announcementsChannel = Conversation::where('name', 'annonces')->where('type', 'announcement')->first();
        if ($announcementsChannel) {
            $this->addUserToChannel($user, $announcementsChannel);
        }

        // Add to department channel if they have a department
        if ($user->department_id) {
            $department = Department::find($user->department_id);
            if ($department) {
                $slug = Str::slug($department->name);
                $deptChannel = Conversation::where('name', "dept-{$slug}")->where('type', 'channel')->first();
                if ($deptChannel) {
                    $this->addUserToChannel($user, $deptChannel);
                }
            }
        }

        // Send welcome message in general
        if ($generalChannel) {
            Message::create([
                'conversation_id' => $generalChannel->id,
                'sender_id' => null,
                'type' => 'system',
                'content' => "🎉 Bienvenue à {$user->name} qui rejoint l'équipe !",
            ]);
        }
    }

    /**
     * Handle user leaving the company
     */
    public function onUserDeleted(User $user): void
    {
        // Remove from all conversations
        ConversationParticipant::where('user_id', $user->id)->update([
            'left_at' => now(),
        ]);
    }

    /**
     * Handle user department change
     */
    public function onUserDepartmentChanged(User $user, ?int $oldDepartmentId, ?int $newDepartmentId): void
    {
        // Remove from old department channel
        if ($oldDepartmentId) {
            $oldDepartment = Department::find($oldDepartmentId);
            if ($oldDepartment) {
                $slug = Str::slug($oldDepartment->name);
                $oldChannel = Conversation::where('name', "dept-{$slug}")->where('type', 'channel')->first();
                if ($oldChannel) {
                    $oldChannel->participants()->where('user_id', $user->id)->update(['left_at' => now()]);
                }
            }
        }

        // Add to new department channel
        if ($newDepartmentId) {
            $newDepartment = Department::find($newDepartmentId);
            if ($newDepartment) {
                $slug = Str::slug($newDepartment->name);
                $newChannel = Conversation::where('name', "dept-{$slug}")->where('type', 'channel')->first();
                if ($newChannel) {
                    $this->addUserToChannel($user, $newChannel);
                    
                    // Welcome message in new department
                    Message::create([
                        'conversation_id' => $newChannel->id,
                        'sender_id' => null,
                        'type' => 'system',
                        'content' => "👋 {$user->name} a rejoint le département !",
                    ]);
                }
            }
        }
    }

    /**
     * Send birthday message
     */
    public function sendBirthdayMessage(User $user): void
    {
        $generalChannel = Conversation::where('name', 'general')->where('type', 'channel')->first();
        
        if ($generalChannel) {
            Message::create([
                'conversation_id' => $generalChannel->id,
                'sender_id' => null,
                'type' => 'system',
                'content' => "🎂 Joyeux anniversaire à {$user->name} ! 🎉",
            ]);
        }
    }

    /**
     * Send work anniversary message
     */
    public function sendWorkAnniversaryMessage(User $user, int $years): void
    {
        $generalChannel = Conversation::where('name', 'general')->where('type', 'channel')->first();
        
        if ($generalChannel) {
            $yearsText = $years === 1 ? '1 an' : "{$years} ans";
            Message::create([
                'conversation_id' => $generalChannel->id,
                'sender_id' => null,
                'type' => 'system',
                'content' => "🎊 Félicitations à {$user->name} pour ses {$yearsText} dans l'entreprise ! 🌟",
            ]);
        }
    }

    /**
     * Add user to a channel
     */
    private function addUserToChannel(User $user, Conversation $channel): void
    {
        // Check if already a participant
        $existing = $channel->participants()->where('user_id', $user->id)->first();
        
        if ($existing) {
            // Reactivate if left
            if ($existing->left_at) {
                $existing->update(['left_at' => null, 'joined_at' => now()]);
            }
        } else {
            $channel->participants()->create([
                'user_id' => $user->id,
                'role' => 'member',
                'joined_at' => now(),
            ]);
        }
    }
}
