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
// Late Hours Recovery Scheduler
// ==========================================

// Daily at 6:00 AM - Check for expired late hours and apply penalties
Schedule::command('presence:check-expired-late')
    ->dailyAt('06:00')
    ->timezone('Europe/Paris')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/late-hours-check.log'));
