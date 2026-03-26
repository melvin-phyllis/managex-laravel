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
    ->timezone('Africa/Abidjan')
    ->withoutOverlapping()
    ->onOneServer();

// Monday 8:00 AM - Check for missing evaluations and send alerts
Schedule::command('evaluations:check-missing')
    ->weeklyOn(1, '08:00')
    ->timezone('Africa/Abidjan')
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
    ->weekdays()
    ->dailyAt('06:00')
    ->timezone('Africa/Abidjan')
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
    ->weekdays()
    ->dailyAt($workStartTime)
    ->timezone('Africa/Abidjan')
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
    ->weekdays()
    ->dailyAt($checkoutReminderTime)
    ->timezone('Africa/Abidjan')
    ->onOneServer();

// ==========================================
// Auto Check-Out (Forgot to clock out)
// ==========================================

// 30 min after work end time - Auto check-out employees who forgot
$autoCheckoutTime = \Carbon\Carbon::createFromTime((int) $endTimeParts[0], (int) $endTimeParts[1])
    ->addMinutes(30)
    ->format('H:i');

Schedule::command('presence:auto-checkout')
    ->weekdays()
    ->dailyAt($autoCheckoutTime)
    ->timezone('Africa/Abidjan')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/auto-checkout.log'));

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
    ->weekdays()
    ->dailyAt($breakStartTime)
    ->timezone('Africa/Abidjan')
    ->onOneServer();

// At break end time - Remind employees break is over
$breakEndTime = '13:00';
try {
    $breakEndTime = \App\Models\Setting::getBreakEndTime();
} catch (\Exception $e) {
    // Fallback to default if DB not available
}

Schedule::job(new \App\Jobs\SendBreakReminderJob('break_end'))
    ->weekdays()
    ->dailyAt($breakEndTime)
    ->timezone('Africa/Abidjan')
    ->onOneServer();

// ==========================================
// Task Reminders (24h before & Overdue)
// ==========================================

// Daily at 08:00 AM - Send task reminders
Schedule::command('notifications:send-reminders')
    ->weekdays()
    ->dailyAt('08:00')
    ->timezone('Africa/Abidjan')
    ->onOneServer();

// ==========================================
// Weekly Report (Rapport Hebdomadaire)
// ==========================================

// Friday at 19:00 - Send weekly presence report to admins
Schedule::command('report:weekly')
    ->weeklyOn(5, '19:00')
    ->timezone('Africa/Abidjan')
    ->withoutOverlapping()
    ->onOneServer();

// ==========================================
// Recruitment inbox sync
// ==========================================
Schedule::command('recruitment:sync-stage-requests --days=7')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/recruitment-sync.log'));
