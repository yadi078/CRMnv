<?php

namespace App\Console\Commands;

use App\Services\ReminderDueNotifier;
use Illuminate\Console\Command;

class SendReminderDueNotifications extends Command
{
    protected $signature = 'reminders:send-due';

    protected $description = 'Envía avisos de recordatorios: 10 min antes y en la hora programada';

    public function handle(ReminderDueNotifier $notifier): int
    {
        $sent = $notifier->dispatchDue();

        if ($sent > 0) {
            $this->info('Enviadas ' . $sent . ' notificaciones de recordatorio.');
        }

        return self::SUCCESS;
    }
}
