<?php

namespace App\Events;

use App\Models\Messaging\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message->load('sender');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to all participants except the sender
        foreach ($this->message->conversation->activeParticipants as $participant) {
            if ($participant->user_id !== $this->message->sender_id) {
                $channels[] = new PrivateChannel('user.'.$participant->user_id);
            }
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-message';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'sender_id' => $this->message->sender_id,
                'user_id' => $this->message->sender_id, // Alias pour compatibilité
                'sender' => $this->message->sender ? [
                    'id' => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                    'avatar' => $this->message->sender->avatar ?? null,
                ] : null,
                'user' => $this->message->sender ? [
                    'id' => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                    'avatar' => $this->message->sender->avatar ?? null,
                ] : null,
                'content' => $this->message->content,
                'created_at' => $this->message->created_at->toIso8601String(),
            ],
            'conversation_id' => $this->message->conversation_id,
        ];
    }
}
