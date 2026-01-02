<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule: Process overdue tasks daily at midnight
// runInBackground() allows it to run without blocking other scheduled tasks
Schedule::command('tasks:process-overdue')
    ->daily()
    ->at('00:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->runInBackground();

// Schedule: Process overdue character quests daily at midnight
Schedule::command('character-quests:process-overdue')
    ->daily()
    ->at('00:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->runInBackground();
