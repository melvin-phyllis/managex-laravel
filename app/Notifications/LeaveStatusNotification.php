<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Leave $leave,
        public string $status
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->status === 'approved') {
            $mail = (new MailMessage)
                ->subject('Votre demande de conge a ete approuvee - ManageX')
                ->greeting('Bonjour '.$notifiable->name.',')
                ->line('Bonne nouvelle ! Votre demande de conge a ete **approuvee**.')
                ->line('**Type :** '.($this->leave->type_label ?? $this->leave->type))
                ->line('**Du :** '.$this->leave->date_debut->format('d/m/Y').' **au** '.$this->leave->date_fin->format('d/m/Y'))
                ->line('**Duree :** '.$this->leave->nombre_jours.' jour(s)')
                ->action('Voir ma demande', route('employee.leaves.show', $this->leave))
                ->line('Bon repos !');
        } else {
            $mail = (new MailMessage)
                ->subject('Votre demande de conge a ete refusee - ManageX')
                ->greeting('Bonjour '.$notifiable->name.',')
                ->line('Votre demande de conge a malheureusement ete **refusee**.')
                ->line('**Type :** '.($this->leave->type_label ?? $this->leave->type))
                ->line('**Du :** '.$this->leave->date_debut->format('d/m/Y').' **au** '.$this->leave->date_fin->format('d/m/Y'));

            if ($this->leave->commentaire_admin) {
                $mail->line('**Motif du refus :** '.$this->leave->commentaire_admin);
            }

            $mail->action('Voir ma demande', route('employee.leaves.show', $this->leave))
                ->line('N\'hesitez pas a contacter l\'administration pour plus d\'informations.');
        }

        return $mail;
    }

    public function toDatabase(object $notifiable): array
    {
        $statusLabels = [
            'approved' => 'approuvee',
            'rejected' => 'refusee',
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
            'message' => "{$icons[$this->status]} Votre demande de conge a ete {$statusLabels[$this->status]}",
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
