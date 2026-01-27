<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendNotificationReminders extends Command
{
    protected $signature = 'notifications:send-reminders';
    
    protected $description = 'Send reminder notifications for upcoming tasks and deadlines';

    public function handle(): int
    {
        $this->info('Sending notification reminders...');
        
        // Get tasks due in 24 hours
        $this->sendTaskReminders24h();
        
        // Get overdue tasks
        $this->sendOverdueReminders();
        
        $this->info('✅ Reminders sent successfully!');
        
        return Command::SUCCESS;
    }

    private function sendTaskReminders24h(): void
    {
        $tomorrow = Carbon::tomorrow();
        
        $tasks = Task::whereDate('date_fin', $tomorrow)
            ->whereNotIn('statut', ['completed', 'rejected'])
            ->whereNotNull('user_id')
            ->with('user')
            ->get();

        $count = 0;
        foreach ($tasks as $task) {
            if ($task->user) {
                $task->user->notify(new TaskReminderNotification($task, '24h'));
                $count++;
            }
        }
        
        $this->line("  - Sent {$count} 24h reminders");
    }

    private function sendOverdueReminders(): void
    {
        $tasks = Task::where('date_fin', '<', Carbon::now())
            ->whereNotIn('statut', ['completed', 'rejected'])
            ->whereNotNull('user_id')
            ->with('user')
            ->get();

        $count = 0;
        foreach ($tasks as $task) {
            if ($task->user) {
                $task->user->notify(new TaskReminderNotification($task, 'overdue'));
                $count++;
            }
        }
        
        $this->line("  - Sent {$count} overdue reminders");
    }
}
