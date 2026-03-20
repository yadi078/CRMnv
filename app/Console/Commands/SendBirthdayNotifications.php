<?php

namespace App\Console\Commands;

use App\Services\ContactBirthdayNotifier;
use Illuminate\Console\Command;

class SendBirthdayNotifications extends Command
{
    protected $signature = 'birthdays:notify';

    protected $description = 'Notifica a los administradores los contactos que cumplen años hoy (mes/día)';

    public function handle(ContactBirthdayNotifier $notifier): int
    {
        $sent = $notifier->notifyAdminsForToday();

        if ($sent > 0) {
            $this->info('Enviadas '.$sent.' notificación(es) de cumpleaños (sin duplicar el mismo contacto el mismo día).');
        } else {
            $this->comment('Sin cumpleaños hoy o ya notificados.');
        }

        return self::SUCCESS;
    }
}
