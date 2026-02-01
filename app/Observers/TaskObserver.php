<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Services\CacheService;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        // Invalider le cache des statistiques
        CacheService::clearTaskCache();
        if ($task->user_id) {
            CacheService::clearUserCache($task->user_id);
        }

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
        // Invalider le cache des statistiques
        CacheService::clearTaskCache();
        if ($task->user_id) {
            CacheService::clearUserCache($task->user_id);
        }
        // Si l'utilisateur a changé, invalider aussi le cache de l'ancien assigné
        if ($task->isDirty('user_id') && $task->getOriginal('user_id')) {
            CacheService::clearUserCache($task->getOriginal('user_id'));
        }

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
        CacheService::clearTaskCache();
        if ($task->user_id) {
            CacheService::clearUserCache($task->user_id);
        }
    }
}
