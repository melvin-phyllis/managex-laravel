<?php

namespace App\Jobs;

use App\Models\Presence;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\CheckInReminderNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCheckOutRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $reminderType = 'reminder' // 'reminder', 'auto_checkout'
    ) {}

    public function handle(): void
    {
        $workEndTime = Setting::getWorkEndTime();
        $now = now();

        Log::info("[CheckOutReminder] Running {$this->reminderType} job at {$now->format('H:i')}");

        // Get all active employees
        $employees = User::where('role', 'employee')
            ->whereIn('status', ['active', 'on_leave'])
            ->get();

        $processedCount = 0;

        foreach ($employees as $employee) {
            // Skip if not a working day
            if (! $employee->isWorkingDay()) {
                continue;
            }

            $todayPresence = $employee->presences()
                ->whereDate('date', today())
                ->first();

            // Skip if no presence today or no check-in
            if (! $todayPresence || ! $todayPresence->check_in) {
                continue;
            }

            // Skip if already checked out
            if ($todayPresence->check_out) {
                continue;
            }

            if ($this->reminderType === 'reminder') {
                // Send reminder notification
                $employee->notify(
                    new CheckInReminderNotification('checkout_reminder', $workEndTime)
                );
                $processedCount++;
            } elseif ($this->reminderType === 'auto_checkout') {
                // Auto check-out at official end time
                $this->autoCheckOut($todayPresence, $employee, $workEndTime);
                $processedCount++;
            }
        }

        Log::info("[CheckOutReminder] {$this->reminderType}: Processed {$processedCount} employees");

        // Schedule auto-checkout 60 min after end time (if this is the reminder)
        if ($this->reminderType === 'reminder') {
            // 70 min from now (10 min before end + 60 min after end = 70 min)
            self::dispatch('auto_checkout')->delay(now()->addMinutes(70));
        }
    }

    private function autoCheckOut(Presence $presence, User $employee, string $workEndTime): void
    {
        $scheduledEnd = Carbon::createFromFormat('H:i', $workEndTime)
            ->setDate(now()->year, now()->month, now()->day);

        $presence->update([
            'check_out' => $scheduledEnd,
            'is_auto_checkout' => true,
            'scheduled_end' => $workEndTime,
        ]);

        // Calculate overtime if applicable
        $overtimeMinutes = $presence->calculateOvertimeMinutes();
        if ($overtimeMinutes > 0) {
            $presence->update(['overtime_minutes' => $overtimeMinutes]);
        }

        // Send confirmation notification
        $employee->notify(
            new CheckInReminderNotification('auto_checkout', $workEndTime)
        );

        Log::info("[CheckOutReminder] Auto check-out for {$employee->name} at {$workEndTime}");
    }
}
