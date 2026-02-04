<?php

namespace App\Policies;

use App\Models\Messaging\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view the message
     */
    public function view(User $user, Message $message): bool
    {
        return $message->conversation->hasParticipant($user->id);
    }

    /**
     * Determine if user can create a message (in any conversation)
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if user can update (edit) the message
     */
    public function update(User $user, Message $message): bool
    {
        // Only the sender can edit
        if ($message->sender_id !== $user->id) {
            return false;
        }

        // Can only edit within 60 minutes
        if ($message->created_at->diffInMinutes(now()) > 60) {
            return false;
        }

        // Can't edit system messages
        if ($message->type === 'system') {
            return false;
        }

        return true;
    }

    /**
     * Determine if user can delete the message
     */
    public function delete(User $user, Message $message): bool
    {
        // Sender can delete their own messages
        if ($message->sender_id === $user->id) {
            return true;
        }

        // System admin can delete any message
        if ($user->isAdmin()) {
            return true;
        }

        // Conversation admin/moderator can delete
        $participant = $message->conversation->activeParticipants()
            ->where('user_id', $user->id)
            ->first();

        return $participant && in_array($participant->role, ['admin', 'moderator']);
    }

    /**
     * Determine if user can react to the message
     */
    public function react(User $user, Message $message): bool
    {
        return $message->conversation->hasParticipant($user->id);
    }

    /**
     * Determine if user can reply to the message
     */
    public function reply(User $user, Message $message): bool
    {
        $conversation = $message->conversation;

        // Must be a participant
        if (! $conversation->hasParticipant($user->id)) {
            return false;
        }

        // Check announcement restrictions
        if ($conversation->type === 'announcement') {
            return $user->isAdmin() || $conversation->isAdmin($user->id);
        }

        return true;
    }
}
