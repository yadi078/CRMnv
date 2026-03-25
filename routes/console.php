<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recordatorios y vencidos de seguimientos: cada 30 minutos
Schedule::command('follow-ups:send-reminders')->everyThirtyMinutes();

// Recordatorios personales: cada minuto
Schedule::command('reminders:send-due')->everyMinute();

// Cumpleaños: mismo día calendario en app.timezone (config/crm.php → BIRTHDAY_NOTIFY_TIME)
Schedule::command('birthdays:notify')
    ->dailyAt(config('crm.birthday_notify_time'))
    ->timezone(config('app.timezone'));
