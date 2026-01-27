<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class LeaveRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Leave $leave
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'leave_request',
            'leave_id' => $this->leave->id,
            'leave_type' => $this->leave->type_label,
            'employee_name' => $this->leave->user?->name ?? 'Employé',
            'start_date' => $this->leave->date_debut->format('d/m/Y'),
            'end_date' => $this->leave->date_fin->format('d/m/Y'),
            'days' => $this->leave->duree,
            'message' => "Nouvelle demande de congé de " . ($this->leave->user?->name ?? 'un employé'),
            'url' => route('admin.leaves.show', $this->leave),
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
