<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Traits\SendsWebPush;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable, SendsWebPush;

    public function __construct(
        public Task $task
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database', 'broadcast'];

        if ($this->shouldSendWebPush($notifiable)) {
            $channels[] = 'webpush';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $priorityLabels = [
            'haute' => 'Haute',
            'moyenne' => 'Moyenne',
            'basse' => 'Basse',
        ];

        $mail = (new MailMessage)
            ->subject('Nouvelle tache qui vous a ete assignee - ManageX')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Une nouvelle tache vous a ete assignee par l\'administration.')
            ->line('**Tache :** '.$this->task->titre);

        if ($this->task->description) {
            $mail->line('**Description :** '.\Str::limit($this->task->description, 200));
        }

        $mail->line('**Priorite :** '.($priorityLabels[$this->task->priorite] ?? $this->task->priorite));

        if ($this->task->date_fin) {
            $mail->line('**Date limite :** '.$this->task->date_fin->format('d/m/Y'));
        }

        return $mail
            ->action('Voir la tache', route('employee.tasks.show', $this->task))
            ->line('Merci de traiter cette tache dans les delais impartis.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'task_assigned',
            'task_id' => $this->task->id,
            'task_title' => $this->task->titre,
            'task_description' => \Str::limit($this->task->description, 100),
            'priority' => $this->task->priorite,
            'due_date' => $this->task->date_fin?->format('d/m/Y'),
            'message' => "Nouvelle tache assignee : {$this->task->titre}",
            'url' => route('employee.tasks.show', $this->task),
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
