<?php

namespace App\Console\Commands;

use App\Models\InternEvaluation;
use App\Models\User;
use App\Notifications\MissingEvaluationAlert;
use Illuminate\Console\Command;

class SendMissingEvaluationAlerts extends Command
{
    protected $signature = 'evaluations:check-missing';

    protected $description = 'Send alerts for missing evaluations from last week (run on Mondays at 8:00 AM)';

    public function handle(): int
    {
        $this->info('Checking for missing evaluations from last week...');

        $lastWeekStart = now()->subWeek()->startOfWeek();
        $weekLabel = 'Semaine du '.$lastWeekStart->format('d/m/Y');

        $this->line("Checking: {$weekLabel}");
        $this->newLine();

        // Get tutors who have interns
        $tutors = User::whereHas('supervisees', function ($q) {
            $q->interns();
        })->get();

        $tutorsAlerted = 0;
        $adminsAlerted = 0;
        $totalMissingInterns = 0;

        // Collect all missing data for admin summary
        $allMissingData = collect();

        foreach ($tutors as $tutor) {
            $interns = $tutor->supervisees()->interns()->get();

            // Find interns without submitted evaluation for last week
            $missingInterns = $interns->filter(function ($intern) use ($lastWeekStart) {
                return ! InternEvaluation::where('intern_id', $intern->id)
                    ->where('week_start', $lastWeekStart)
                    ->where('status', 'submitted')
                    ->exists();
            });

            if ($missingInterns->isNotEmpty()) {
                // Alert the tutor
                $tutor->notify(new MissingEvaluationAlert($missingInterns, $tutor, $weekLabel));
                $tutorsAlerted++;
                $totalMissingInterns += $missingInterns->count();

                $this->line("  ⚠️  {$tutor->name}: {$missingInterns->count()} intern(s) not evaluated");

                // Collect for admin summary
                foreach ($missingInterns as $intern) {
                    $allMissingData->push([
                        'intern' => $intern,
                        'tutor' => $tutor,
                    ]);
                }
            } else {
                $this->line("  ✓ {$tutor->name}: All evaluations submitted");
            }
        }

        // If there are missing evaluations, alert admins
        if ($allMissingData->isNotEmpty()) {
            $this->newLine();
            $this->info('Alerting HR Admins...');

            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                // Group by tutor for better admin view
                foreach ($allMissingData->groupBy('tutor.id') as $tutorId => $items) {
                    $tutor = $items->first()['tutor'];
                    $missingInterns = $items->pluck('intern');

                    $admin->notify(new MissingEvaluationAlert($missingInterns, $tutor, $weekLabel));
                }
                $adminsAlerted++;
            }

            $this->line("  ✓ Notified {$adminsAlerted} admin(s)");
        }

        $this->newLine();
        $this->info('📊 Summary:');
        $this->line("  - Tutors alerted: {$tutorsAlerted}");
        $this->line("  - Admins alerted: {$adminsAlerted}");
        $this->line("  - Missing evaluations: {$totalMissingInterns}");

        if ($totalMissingInterns === 0) {
            $this->newLine();
            $this->info('🎉 All evaluations were submitted for last week!');
        }

        return Command::SUCCESS;
    }
}
