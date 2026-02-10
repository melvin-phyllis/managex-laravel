<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class CheckInReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $type = 'reminder', // 'reminder', 'warning', 'pre_checkin_confirm'
        public ?string $workStartTime = null
    ) {
        $this->workStartTime = $workStartTime ?? '08:00';
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (method_exists($notifiable, 'pushSubscriptions')
            && $notifiable->pushSubscriptions()->exists()) {
            $channels[] = 'webpush';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        $messages = $this->getMessages();

        return [
            'type' => 'check_in_reminder',
            'reminder_type' => $this->type,
            'title' => $messages['title'],
            'message' => $messages['body'],
            'url' => route('employee.presences.index'),
            'requires_action' => true,
        ];
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $messages = $this->getMessages();
        $isAlarm = in_array($this->type, ['pre_checkin_alarm', 'warning']);

        return (new WebPushMessage)
            ->title($messages['title'])
            ->body($messages['body'])
            ->icon('/icons/icon-192x192.png')
            ->badge('/icons/icon-72x72.png')
            ->action('Pointer maintenant', 'check_in')
            ->options([
                'TTL' => 300,
                'urgency' => $isAlarm ? 'high' : 'normal',
                'topic' => 'check-in-reminder',
            ])
            ->data([
                'url' => route('employee.presences.index'),
                'type' => 'check_in_reminder',
                'reminder_type' => $this->type,
                'play_sound' => true,
                'sound_type' => $isAlarm ? 'urgent' : 'notification',
            ]);
    }

    private function getMessages(): array
    {
        return match ($this->type) {
            'pre_checkin_alarm' => [
                'title' => '⏰ Confirmez votre présence !',
                'body' => "Il est {$this->workStartTime}. Vous avez fait un pré-pointage, confirmez votre présence maintenant !",
            ],
            'pre_checkin_confirm' => [
                'title' => '✅ Présence confirmée !',
                'body' => "Il est {$this->workStartTime}. Votre arrivée anticipée a été validée. Bonne journée !",
            ],
            'warning' => [
                'title' => '⚠️ Attention - Retard imminent !',
                'body' => "Vous n'avez toujours pas pointé. Vous serez bientôt marqué en retard !",
            ],
            'second_reminder' => [
                'title' => "🔔 N'oubliez pas de pointer !",
                'body' => "Il est {$this->workStartTime} passé. Marquez votre présence rapidement.",
            ],
            default => [
                'title' => "⏰ Il est {$this->workStartTime} !",
                'body' => 'Marquez votre présence SVP. Cliquez ici pour pointer.',
            ],
        };
    }
}
