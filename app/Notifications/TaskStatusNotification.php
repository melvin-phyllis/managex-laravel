<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Task $task;

    protected string $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, string $status)
    {
        $this->task = $task;
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
        $statusLabel = match ($this->status) {
            'approved' => 'approuvée',
            'rejected' => 'rejetée',
            'completed' => 'marquée comme terminée',
            default => 'mise à jour',
        };

        return (new MailMessage)
            ->subject('Mise à jour de votre tâche - ManageX')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Votre tâche "'.$this->task->titre.'" a été '.$statusLabel.'.')
            ->action('Voir mes tâches', route('employee.tasks.index'))
            ->line('Merci d\'utiliser ManageX !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusLabel = match ($this->status) {
            'approved' => 'approuvée',
            'rejected' => 'rejetée',
            'completed' => 'terminée',
            default => 'mise à jour',
        };

        return [
            'type' => 'task_status',
            'task_id' => $this->task->id,
            'task_titre' => $this->task->titre,
            'status' => $this->status,
            'message' => 'Votre tâche "'.$this->task->titre.'" a été '.$statusLabel.'.',
            'url' => route('employee.tasks.index'),
        ];
    }
}
