<?php

namespace App\Policies;

use App\Models\Messaging\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConversationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view the conversation
     */
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->hasParticipant($user->id);
    }

    /**
     * Determine if user can view any conversations (list)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if user can create a conversation
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if user can update the conversation
     */
    public function update(User $user, Conversation $conversation): bool
    {
        // Admins can update any conversation
        if ($user->isAdmin()) {
            return true;
        }

        // Only conversation admins can update
        return $conversation->isAdmin($user->id);
    }

    /**
     * Determine if user can delete/leave the conversation
     */
    public function delete(User $user, Conversation $conversation): bool
    {
        // For direct messages, anyone can archive
        if ($conversation->type === 'direct') {
            return $conversation->hasParticipant($user->id);
        }

        // For groups, only conversation admin or system admin
        return $conversation->isAdmin($user->id) || $user->isAdmin();
    }

    /**
     * Determine if user can send messages in the conversation
     */
    public function sendMessage(User $user, Conversation $conversation): bool
    {
        // Must be a participant
        if (! $conversation->hasParticipant($user->id)) {
            return false;
        }

        // Announcement channels: only admins can post
        if ($conversation->type === 'announcement') {
            return $user->isAdmin() || $conversation->isAdmin($user->id);
        }

        // Check if participant is muted
        $participant = $conversation->participants()->where('user_id', $user->id)->first();
        if ($participant && $participant->isMuted()) {
            return false;
        }

        return true;
    }

    /**
     * Determine if user can manage participants
     */
    public function manageParticipants(User $user, Conversation $conversation): bool
    {
        // Not for direct messages
        if ($conversation->type === 'direct') {
            return false;
        }

        // System admins can always manage
        if ($user->isAdmin()) {
            return true;
        }

        // Conversation admins/moderators can manage
        $participant = $conversation->activeParticipants()
            ->where('user_id', $user->id)
            ->first();

        return $participant && in_array($participant->role, ['admin', 'moderator']);
    }

    /**
     * Determine if user can pin the conversation
     */
    public function pin(User $user, Conversation $conversation): bool
    {
        return $conversation->hasParticipant($user->id);
    }

    /**
     * Determine if user can archive the conversation
     */
    public function archive(User $user, Conversation $conversation): bool
    {
        return $conversation->hasParticipant($user->id);
    }
}
