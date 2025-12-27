<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Close inactive screen usage logs (runs every 5 minutes)
Schedule::command('screens:close-inactive-logs')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Automated database and file backups (hospital-grade disaster recovery)
// Daily backup at 2:00 AM (adjust timezone as needed)
Schedule::command('backup:run')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->emailOutputOnFailure(env('BACKUP_NOTIFICATION_EMAIL'));

// Cleanup old backups (runs after backup to maintain retention policy)
Schedule::command('backup:clean')
    ->dailyAt('02:30')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->emailOutputOnFailure(env('BACKUP_NOTIFICATION_EMAIL'));

// Monitor backup health (runs daily to ensure backups are healthy)
Schedule::command('backup:monitor')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->emailOutputOnFailure(env('BACKUP_NOTIFICATION_EMAIL'));

// Check for alerts (churn risk, payment risk, system health)
Schedule::command('alerts:check')
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->onOneServer();

// Run automation (trial reminders, inactivity nudges)
Schedule::command('automation:run')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->onOneServer();
