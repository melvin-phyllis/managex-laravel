<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $reminderType = '24h'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $messages = [
            '24h' => "Rappel : La tâche \"{$this->task->titre}\" est due demain",
            'overdue' => "⚠️ La tâche \"{$this->task->titre}\" est en retard !",
        ];

        return [
            'type' => 'task_reminder',
            'task_id' => $this->task->id,
            'task_title' => $this->task->titre,
            'priority' => $this->task->priorite,
            'due_date' => $this->task->date_fin?->format('d/m/Y'),
            'reminder_type' => $this->reminderType,
            'message' => $messages[$this->reminderType] ?? $messages['24h'],
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
