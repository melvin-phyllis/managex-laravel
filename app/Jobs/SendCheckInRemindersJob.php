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

class SendCheckInRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $reminderType = 'reminder' // 'reminder', 'second_reminder', 'warning'
    ) {}

    public function handle(): void
    {
        $workStartTime = Setting::getWorkStartTime();
        $now = now();

        Log::info("[CheckInReminder] Running {$this->reminderType} job at {$now->format('H:i')}");

        // Get all active employees
        $employees = User::where('role', 'employee')
            ->where('is_active', true)
            ->get();

        $notifiedCount = 0;

        foreach ($employees as $employee) {
            // Skip if not a working day for this employee
            if (! $employee->isWorkingDay()) {
                continue;
            }

            $todayPresence = $employee->presences()
                ->whereDate('date', today())
                ->first();

            // Already checked in (not pre-check-in) → skip
            if ($todayPresence && $todayPresence->check_in) {
                continue;
            }

            // Pre-check-in exists but no actual check-in
            if ($todayPresence && $todayPresence->pre_check_in && ! $todayPresence->check_in) {
                if ($this->reminderType === 'reminder') {
                    // At work start time: send alarm notification (push + sound)
                    // Do NOT auto-confirm yet — let the user confirm manually
                    if (method_exists($employee, 'pushSubscriptions')
                        && $employee->pushSubscriptions()->exists()) {
                        $employee->notify(
                            new CheckInReminderNotification('pre_checkin_alarm', $workStartTime)
                        );
                        $notifiedCount++;
                    }

                    continue;
                } elseif ($this->reminderType === 'second_reminder') {
                    // 5 minutes after start: auto-confirm + send confirmation
                    $this->confirmPreCheckIn($todayPresence, $employee, $workStartTime);
                    $notifiedCount++;

                    continue;
                }
            }

            // No presence at all → send reminder
            if (! $todayPresence) {
                if (method_exists($employee, 'pushSubscriptions')
                    && $employee->pushSubscriptions()->exists()) {
                    $employee->notify(
                        new CheckInReminderNotification($this->reminderType, $workStartTime)
                    );
                    $notifiedCount++;
                }
            }
        }

        Log::info("[CheckInReminder] {$this->reminderType}: Notified {$notifiedCount} employees");

        // Schedule follow-up reminders
        if ($this->reminderType === 'reminder') {
            // Schedule second reminder in 5 minutes (auto-confirms pre-check-ins)
            self::dispatch('second_reminder')->delay(now()->addMinutes(5));

            // Schedule warning at tolerance limit
            $lateTolerance = Setting::getLateTolerance();
            if ($lateTolerance > 5) {
                self::dispatch('warning')->delay(now()->addMinutes($lateTolerance - 2));
            }
        }
    }

    private function confirmPreCheckIn(Presence $presence, User $employee, string $workStartTime): void
    {
        $scheduledStart = Carbon::createFromFormat('H:i', $workStartTime)
            ->setDate(now()->year, now()->month, now()->day);

        $presence->update([
            'check_in' => $scheduledStart,
            'is_early_arrival' => true,
            'is_late' => false,
            'late_minutes' => null,
        ]);

        // Send confirmation notification
        if (method_exists($employee, 'pushSubscriptions')
            && $employee->pushSubscriptions()->exists()) {
            $employee->notify(
                new CheckInReminderNotification('pre_checkin_confirm', $workStartTime)
            );
        }

        Log::info("[CheckInReminder] Auto-confirmed pre-check-in for {$employee->name}");
    }
}
