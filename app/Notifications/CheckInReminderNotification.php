<?php

namespace App\Notifications;

use App\Notifications\Traits\SendsOneSignal;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class CheckInReminderNotification extends Notification implements ShouldQueue
{
    use Queueable, SendsOneSignal;

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

        // Send via OneSignal (works even when browser is closed)
        $this->sendViaOneSignal($notifiable, [
            'type' => 'check_in_reminder',
            'title' => $messages['title'],
            'message' => $messages['body'],
            'url' => route('employee.presences.index'),
        ]);

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
        $isCheckout = in_array($this->type, ['checkout_reminder', 'auto_checkout']);
        $actionLabel = $isCheckout ? 'Voir mes présences' : 'Pointer maintenant';
        $topic = $isCheckout ? 'check-out-reminder' : 'check-in-reminder';

        return (new WebPushMessage)
            ->title($messages['title'])
            ->body($messages['body'])
            ->icon('/icons/icon-192x192.png')
            ->badge('/icons/icon-72x72.png')
            ->action($actionLabel, 'check_in')
            ->options([
                'TTL' => 300,
                'urgency' => $isAlarm ? 'high' : 'normal',
                'topic' => $topic,
            ])
            ->data([
                'url' => route('employee.presences.index'),
                'type' => $isCheckout ? 'check_out_reminder' : 'check_in_reminder',
                'reminder_type' => $this->type,
                'play_sound' => ! $isCheckout || $this->type === 'checkout_reminder',
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
            'checkout_reminder' => [
                'title' => "🏠 N'oubliez pas de pointer votre départ !",
                'body' => "Il est bientôt {$this->workStartTime}. Pensez à pointer votre départ avant de partir.",
            ],
            'auto_checkout' => [
                'title' => '✅ Départ enregistré automatiquement',
                'body' => "Votre départ a été automatiquement enregistré à {$this->workStartTime}.",
            ],
            default => [
                'title' => "⏰ Il est {$this->workStartTime} !",
                'body' => 'Marquez votre présence SVP. Cliquez ici pour pointer.',
            ],
        };
    }
}
