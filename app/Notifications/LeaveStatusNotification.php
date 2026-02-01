<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class LeaveStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Leave $leave,
        public string $status
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusLabels = [
            'approved' => 'approuvée',
            'rejected' => 'refusée',
        ];

        $icons = [
            'approved' => '✅',
            'rejected' => '❌',
        ];

        return [
            'type' => 'leave_status',
            'leave_id' => $this->leave->id,
            'status' => $this->status,
            'start_date' => $this->leave->date_debut->format('d/m/Y'),
            'end_date' => $this->leave->date_fin->format('d/m/Y'),
            'rejection_reason' => $this->leave->commentaire_admin,
            'message' => "{$icons[$this->status]} Votre demande de congé a été {$statusLabels[$this->status]}",
            'url' => route('employee.leaves.show', $this->leave),
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

