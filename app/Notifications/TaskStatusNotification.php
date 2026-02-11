<?php

namespace App\Notifications;

use App\Models\Task;
use App\Notifications\Traits\SendsOneSignal;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStatusNotification extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush, SendsOneSignal;

    protected Task $task;

    protected string $status;

    public function __construct(Task $task, string $status)
    {
        $this->task = $task;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->shouldSendWebPush($notifiable)) {
            $channels[] = 'webpush';
        }

        $channels[] = 'mail';

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $config = $this->getStatusConfig();

        $mail = (new MailMessage)
            ->subject($config['subject'])
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($config['intro'])
            ->line('**Tache :** '.$this->task->titre);

        if ($this->task->date_fin) {
            $mail->line('**Date limite :** '.$this->task->date_fin->format('d/m/Y'));
        }

        if ($config['extra']) {
            $mail->line($config['extra']);
        }

        return $mail
            ->action('Voir la tache', route('employee.tasks.show', $this->task))
            ->line($config['closing']);
    }

    public function toArray(object $notifiable): array
    {
        $this->sendViaOneSignal($notifiable);

        $config = $this->getStatusConfig();

        return [
            'type' => 'task_status',
            'task_id' => $this->task->id,
            'task_titre' => $this->task->titre,
            'status' => $this->status,
            'message' => $config['notification'],
            'url' => route('employee.tasks.show', $this->task),
        ];
    }

    protected function getStatusConfig(): array
    {
        return match ($this->status) {
            'approved' => [
                'subject' => 'Votre tache a ete approuvee - ManageX',
                'intro' => 'Votre tache **"'.$this->task->titre.'"** a ete **approuvee** par l\'administration.',
                'extra' => 'Vous pouvez maintenant la marquer comme terminee une fois le travail effectue.',
                'closing' => 'Bon travail !',
                'notification' => 'Votre tache "'.$this->task->titre.'" a ete approuvee.',
            ],
            'rejected' => [
                'subject' => 'Votre tache a ete rejetee - ManageX',
                'intro' => 'Votre tache **"'.$this->task->titre.'"** a ete **rejetee** par l\'administration.',
                'extra' => $this->task->commentaire_admin
                    ? '**Motif :** '.$this->task->commentaire_admin
                    : 'Veuillez consulter les details et apporter les corrections necessaires.',
                'closing' => 'N\'hesitez pas a contacter l\'administration pour plus d\'informations.',
                'notification' => 'Votre tache "'.$this->task->titre.'" a ete rejetee.',
            ],
            'completed' => [
                'subject' => 'Tache terminee avec succes - ManageX',
                'intro' => 'La tache **"'.$this->task->titre.'"** a ete **validee et terminee**.',
                'extra' => null,
                'closing' => 'Merci pour votre travail !',
                'notification' => 'La tache "'.$this->task->titre.'" est terminee.',
            ],
            'assigned' => [
                'subject' => 'Nouvelle tache assignee - ManageX',
                'intro' => 'Une nouvelle tache vous a ete assignee : **"'.$this->task->titre.'"**.',
                'extra' => $this->task->priorite
                    ? '**Priorite :** '.ucfirst($this->task->priorite)
                    : null,
                'closing' => 'Merci de traiter cette tache dans les delais impartis.',
                'notification' => 'Nouvelle tache assignee : "'.$this->task->titre.'".',
            ],
            'in_progress' => [
                'subject' => 'Tache en cours de traitement - ManageX',
                'intro' => 'Votre tache **"'.$this->task->titre.'"** est maintenant **en cours de traitement**.',
                'extra' => null,
                'closing' => 'Continuez votre bon travail !',
                'notification' => 'La tache "'.$this->task->titre.'" est en cours.',
            ],
            default => [
                'subject' => 'Mise a jour de votre tache - ManageX',
                'intro' => 'Le statut de votre tache **"'.$this->task->titre.'"** a ete mis a jour.',
                'extra' => '**Nouveau statut :** '.ucfirst(str_replace('_', ' ', $this->status)),
                'closing' => 'Merci d\'utiliser ManageX !',
                'notification' => 'La tache "'.$this->task->titre.'" a ete mise a jour.',
            ],
        };
    }
}
