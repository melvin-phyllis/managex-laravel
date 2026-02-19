<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use App\Notifications\Traits\SendsOneSignal;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCompletedAdminNotification extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush, SendsOneSignal;

    protected Task $task;

    protected User $employee;

    public function __construct(Task $task, User $employee)
    {
        $this->task = $task;
        $this->employee = $employee;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database', 'mail'];

        if ($this->shouldSendWebPush($notifiable)) {
            $channels[] = 'webpush';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('✅ Tâche terminée par '.$this->employee->name.' - ManageX')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('L\'employé **'.$this->employee->name.'** a terminé la tâche suivante :')
            ->line('**Tâche :** '.$this->task->titre)
            ->line('**Progression :** 100%');

        if ($this->task->date_fin) {
            $mail->line('**Date limite :** '.$this->task->date_fin->format('d/m/Y'));
        }

        if ($this->task->priorite) {
            $mail->line('**Priorité :** '.ucfirst($this->task->priorite));
        }

        return $mail
            ->line('Cette tâche est en attente de votre **validation**.')
            ->action('Valider la tâche', route('admin.tasks.show', $this->task))
            ->line('Merci de vérifier et valider le travail effectué.');
    }

    public function toArray(object $notifiable): array
    {
        $this->sendViaOneSignal($notifiable);

        return [
            'type' => 'task_completed',
            'task_id' => $this->task->id,
            'task_titre' => $this->task->titre,
            'employee_name' => $this->employee->name,
            'message' => $this->employee->name.' a terminé la tâche "'.$this->task->titre.'". En attente de validation.',
            'url' => route('admin.tasks.show', $this->task),
        ];
    }
}
