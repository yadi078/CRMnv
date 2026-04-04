<?php

namespace App\Console\Commands;

use App\Services\ReminderAlarmRepeater;
use App\Services\ReminderDueNotifier;
use Illuminate\Console\Command;

class SendReminderDueNotifications extends Command
{
    protected $signature = 'reminders:send-due';

    protected $description = 'Envía avisos de recordatorios (5 min, 2 min antes y a la hora) para todos los usuarios';

    public function handle(ReminderDueNotifier $notifier, ReminderAlarmRepeater $alarmRepeater): int
    {
        $sent = $notifier->dispatchDue(null);
        $repeats = $alarmRepeater->dispatchRepeats(null);
        if ($this->output->isVerbose()) {
            $this->info("Notificaciones de recordatorio enviadas en esta ejecución: {$sent}");
            $this->info("Repeticiones de alarma enviadas: {$repeats}");
        }

        return self::SUCCESS;
    }
}
