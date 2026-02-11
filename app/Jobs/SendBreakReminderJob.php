<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\User;
use App\Notifications\CheckInReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBreakReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $reminderType = 'break_start' // 'break_start', 'break_end'
    ) {}

    public function handle(): void
    {
        $breakStartTime = Setting::getBreakStartTime();
        $breakEndTime = Setting::getBreakEndTime();
        $now = now();

        Log::info("[BreakReminder] Running {$this->reminderType} job at {$now->format('H:i')}");

        // Get all active employees who are checked in today
        $employees = User::where('role', 'employee')
            ->whereIn('status', ['active', 'on_leave'])
            ->get();

        $notifiedCount = 0;

        foreach ($employees as $employee) {
            // Skip if not a working day
            if (! $employee->isWorkingDay()) {
                continue;
            }

            // Only notify employees who have checked in today
            $todayPresence = $employee->presences()
                ->whereDate('date', today())
                ->first();

            if (! $todayPresence || ! $todayPresence->check_in) {
                continue;
            }

            // Skip if already checked out
            if ($todayPresence->check_out) {
                continue;
            }

            $time = $this->reminderType === 'break_start' ? $breakStartTime : $breakEndTime;

            $employee->notify(
                new CheckInReminderNotification($this->reminderType, $time)
            );
            $notifiedCount++;
        }

        Log::info("[BreakReminder] {$this->reminderType}: Notified {$notifiedCount} employees");
    }
}
