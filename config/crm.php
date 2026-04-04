<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hora local del cumpleaños (mismo día que APP_TIMEZONE)
    |--------------------------------------------------------------------------
    |
    | El comando birthdays:notify corre una vez al día a esta hora en la zona
    | configurada en app.timezone (APP_TIMEZONE). Solo notifica contactos cuyo
    | mes y día de fecha_cumpleaños coinciden con ese día calendario local.
    |
    */

    'birthday_notify_time' => env('BIRTHDAY_NOTIFY_TIME', '08:00'),

    /*
    |--------------------------------------------------------------------------
    | Recordatorios personales (modelo Reminder)
    |--------------------------------------------------------------------------
    */

    'reminder_all_day_notify_time' => env('REMINDER_ALL_DAY_NOTIFY_TIME', '09:00'),

    'default_reminder_time' => env('DEFAULT_REMINDER_TIME', '09:00'),

];
