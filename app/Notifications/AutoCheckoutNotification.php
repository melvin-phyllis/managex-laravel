<?php

namespace App\Notifications;

use App\Models\Presence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AutoCheckoutNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Presence $presence
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $checkIn = $this->presence->check_in?->format('H:i') ?? '--:--';
        $checkOut = $this->presence->check_out?->format('H:i') ?? '--:--';

        return [
            'type' => 'auto_checkout',
            'message' => "⚠️ Vous avez été automatiquement pointé à {$checkOut}. Pensez à pointer votre départ. Arrivée : {$checkIn}.",
            'url' => route('employee.presences.index'),
            'presence_id' => $this->presence->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ];
    }

    public function toBroadcast(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
