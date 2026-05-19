<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('notify:tomorrows-deliveries')->dailyAt('11:30')->timezone('Asia/Kolkata');
Schedule::command('notify:tomorrows-not-packed')->dailyAt('18:00')->timezone('Asia/Kolkata');

Schedule::command('notify:customer-tomorrow-delivery')->dailyAt('11:30')->timezone('Asia/Kolkata');
Schedule::command('notify:customer-today-return')->dailyAt('10:00')->timezone('Asia/Kolkata');
