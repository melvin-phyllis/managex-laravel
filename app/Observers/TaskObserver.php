<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        // Notify assigned user when task is created
        if ($task->user_id) {
            $assignee = User::find($task->user_id);
            if ($assignee) {
                $assignee->notify(new TaskAssignedNotification($task));
            }
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // Check if user_id changed (task reassigned)
        if ($task->isDirty('user_id') && $task->user_id) {
            $newAssignee = User::find($task->user_id);
            if ($newAssignee) {
                $newAssignee->notify(new TaskAssignedNotification($task));
            }
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        // Optional: notify assignee that task was deleted
    }
}
