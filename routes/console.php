<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:notify-saved-searches')->dailyAt('08:00');
Schedule::command('app:process-lead-sla')->everyMinute()->withoutOverlapping();
