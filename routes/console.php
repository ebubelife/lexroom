<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Decrement room timers every second
Schedule::command('rooms:decrement-timers')->everySecond();

// Check for expired paused sessions every 15 minutes
Schedule::command('check:paused-sessions')->everyFifteenMinutes();
