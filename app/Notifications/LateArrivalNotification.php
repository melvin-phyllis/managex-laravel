<?php

namespace App\Notifications;

use App\Models\Presence;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class LateArrivalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $employee,
        public Presence $presence,
        public int $minutesLate
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'late_arrival',
            'employee_id' => $this->employee->id,
            'employee_name' => $this->employee->name,
            'presence_id' => $this->presence->id,
            'check_in_time' => $this->presence->check_in->format('H:i'),
            'minutes_late' => $this->minutesLate,
            'message' => "{$this->employee->name} est arrivé(e) en retard de {$this->minutesLate} min",
            'url' => route('admin.presences.index'),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

