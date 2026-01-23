<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Leave $leave;
    protected string $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Leave $leave, string $status)
    {
        $this->leave = $leave;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->status === 'approved' ? 'approuvée' : 'rejetée';
        $statusColor = $this->status === 'approved' ? 'success' : 'error';

        $mail = (new MailMessage)
            ->subject('Demande de congé ' . $statusLabel . ' - ManageX')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre demande de congé du ' . $this->leave->date_debut->format('d/m/Y') . ' au ' . $this->leave->date_fin->format('d/m/Y') . ' a été ' . $statusLabel . '.');

        if ($this->leave->commentaire_admin) {
            $mail->line('Commentaire de l\'administrateur : ' . $this->leave->commentaire_admin);
        }

        return $mail
            ->action('Voir mes congés', route('employee.leaves.index'))
            ->line('Merci d\'utiliser ManageX !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusLabel = $this->status === 'approved' ? 'approuvée' : 'rejetée';

        return [
            'type' => 'leave_status',
            'leave_id' => $this->leave->id,
            'status' => $this->status,
            'date_debut' => $this->leave->date_debut->format('d/m/Y'),
            'date_fin' => $this->leave->date_fin->format('d/m/Y'),
            'commentaire' => $this->leave->commentaire_admin,
            'message' => 'Votre demande de congé a été ' . $statusLabel . '.',
            'url' => route('employee.leaves.index'),
        ];
    }
}
