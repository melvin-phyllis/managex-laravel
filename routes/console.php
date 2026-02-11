<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ==========================================
// Intern Evaluation Scheduler
// ==========================================

// Friday 9:00 AM - Send weekly evaluation reminders to tutors
Schedule::command('evaluations:send-reminders')
    ->weeklyOn(5, '09:00')
    ->timezone('Europe/Paris')
    ->withoutOverlapping()
    ->onOneServer();

// Monday 8:00 AM - Check for missing evaluations and send alerts
Schedule::command('evaluations:check-missing')
    ->weeklyOn(1, '08:00')
    ->timezone('Europe/Paris')
    ->withoutOverlapping()
    ->onOneServer();

// ==========================================
// Queue Worker (push notifications + other queued jobs)
// ==========================================

Schedule::command('queue:work --stop-when-empty --max-time=55 --tries=1')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// ==========================================
// Late Hours Recovery Scheduler
// ==========================================

// Daily at 6:00 AM - Check for expired late hours and apply penalties
Schedule::command('presence:check-expired-late')
    ->dailyAt('06:00')
    ->timezone('Europe/Paris')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/late-hours-check.log'));

// ==========================================
// Check-in Reminders (Early Arrival System)
// ==========================================

// At work start time - Send push reminder to employees who haven't checked in
$workStartTime = '08:00';
try {
    $workStartTime = \App\Models\Setting::getWorkStartTime();
} catch (\Exception $e) {
    // Fallback to default if DB not available (e.g., during migration)
}

Schedule::job(new \App\Jobs\SendCheckInRemindersJob('reminder'))
    ->dailyAt($workStartTime)
    ->timezone('Europe/Paris')
    ->onOneServer();

// ==========================================
// Check-out Reminders (Auto Check-Out System)
// ==========================================

// 10 min before work end time - Remind employees to check out
$workEndTime = '17:00';
try {
    $workEndTime = \App\Models\Setting::getWorkEndTime();
} catch (\Exception $e) {
    // Fallback to default if DB not available
}

// Calculate 10 minutes before end time
$endTimeParts = explode(':', $workEndTime);
$checkoutReminderTime = \Carbon\Carbon::createFromTime((int) $endTimeParts[0], (int) $endTimeParts[1])
    ->subMinutes(10)
    ->format('H:i');

Schedule::job(new \App\Jobs\SendCheckOutRemindersJob('reminder'))
    ->dailyAt($checkoutReminderTime)
    ->timezone('Europe/Paris')
    ->onOneServer();

// ==========================================
// Break Reminders
// ==========================================

// At break start time - Remind employees to take their break
$breakStartTime = '12:00';
try {
    $breakStartTime = \App\Models\Setting::getBreakStartTime();
} catch (\Exception $e) {
    // Fallback to default if DB not available
}

Schedule::job(new \App\Jobs\SendBreakReminderJob('break_start'))
    ->dailyAt($breakStartTime)
    ->timezone('Europe/Paris')
    ->onOneServer();

// At break end time - Remind employees break is over
$breakEndTime = '13:00';
try {
    $breakEndTime = \App\Models\Setting::getBreakEndTime();
} catch (\Exception $e) {
    // Fallback to default if DB not available
}

Schedule::job(new \App\Jobs\SendBreakReminderJob('break_end'))
    ->dailyAt($breakEndTime)
    ->timezone('Europe/Paris')
    ->onOneServer();
