<?php

namespace App\Notifications;

use App\Models\Messaging\Message;
use App\Notifications\Traits\SendsOneSignal;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush, SendsOneSignal;

    public function __construct(
        public Message $message
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->shouldSendWebPush($notifiable)) {
            $channels[] = 'webpush';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        $this->sendViaOneSignal($notifiable);

        $conversation = $this->message->conversation;
        $sender = $this->message->sender;

        // Get conversation name for display
        if ($conversation->type === 'direct') {
            $title = $sender?->name ?? 'Utilisateur';
        } else {
            $title = $conversation->name ?? 'Groupe';
        }

        $senderName = $sender?->name ?? 'Quelqu\'un';
        $isAdmin = $notifiable->role === 'admin';
        $routeName = $isAdmin ? 'admin.messaging.show' : 'employee.messaging.show';

        return [
            'type' => 'new_message',
            'message_id' => $this->message->id,
            'conversation_id' => $conversation->id,
            'conversation_type' => $conversation->type,
            'conversation_name' => $title,
            'sender_id' => $sender?->id,
            'sender_name' => $senderName,
            'sender_avatar' => $sender?->avatar,
            'content_preview' => \Str::limit($this->message->content, 100),
            'has_attachments' => $this->message->attachments()->exists(),
            'message' => "💬 Nouveau message de {$senderName}",
            'url' => route($routeName, $conversation->id),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
