<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\InternEvaluation;
use App\Notifications\WeeklyEvaluationReminder;
use Illuminate\Console\Command;

class SendWeeklyEvaluationReminders extends Command
{
    protected $signature = 'evaluations:send-reminders';

    protected $description = 'Send weekly evaluation reminders to tutors (run on Fridays at 9:00 AM)';

    public function handle(): int
    {
        $this->info('Sending weekly evaluation reminders to tutors...');

        $currentWeekStart = now()->startOfWeek();

        // Get tutors who have interns to supervise
        $tutors = User::whereHas('supervisees', function ($q) {
            $q->interns();
        })->get();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($tutors as $tutor) {
            $interns = $tutor->supervisees()->interns()->get();

            // Filter interns who don't have an evaluation for this week yet
            $internsToEvaluate = $interns->filter(function ($intern) use ($currentWeekStart) {
                return !InternEvaluation::where('intern_id', $intern->id)
                    ->where('week_start', $currentWeekStart)
                    ->where('status', 'submitted')
                    ->exists();
            });

            if ($internsToEvaluate->isNotEmpty()) {
                $tutor->notify(new WeeklyEvaluationReminder($internsToEvaluate));
                $sentCount++;

                $this->line("  ✓ Sent reminder to {$tutor->name} for {$internsToEvaluate->count()} intern(s)");
            } else {
                $skippedCount++;
                $this->line("  - Skipped {$tutor->name} (all evaluations already submitted)");
            }
        }

        $this->newLine();
        $this->info("✅ Reminders sent: {$sentCount}");
        $this->info("⏭️  Skipped (already submitted): {$skippedCount}");

        return Command::SUCCESS;
    }
}
