<?php

namespace App\Console\Commands;

use App\Services\ReminderDueNotifier;
use Illuminate\Console\Command;

class SendReminderDueNotifications extends Command
{
    protected $signature = 'reminders:send-due';

    protected $description = 'Envía notificaciones de recordatorios (todo el día: cada 10 min; con hora: una vez 10 min antes)';

    public function handle(ReminderDueNotifier $notifier): int
    {
        $sent = $notifier->dispatchDue();

        if ($sent > 0) {
            $this->info('Enviadas ' . $sent . ' notificaciones de recordatorio.');
        }

        return self::SUCCESS;
    }
}
