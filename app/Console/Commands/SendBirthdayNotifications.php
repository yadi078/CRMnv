<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\User;
use App\Notifications\ContactBirthdayNotification;
use Illuminate\Console\Command;

class SendBirthdayNotifications extends Command
{
    protected $signature = 'birthdays:notify';

    protected $description = 'Notifica a los administradores los contactos que cumplen años hoy';

    public function handle(): int
    {
        $today = now();

        $contacts = Contact::whereNotNull('fecha_cumpleanos')
            ->whereMonth('fecha_cumpleanos', $today->month)
            ->whereDay('fecha_cumpleanos', $today->day)
            ->with('company')
            ->get();

        $admins = User::role('admin')->get();

        if ($admins->isEmpty()) {
            $this->warn('No hay administradores para notificar.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($contacts as $contact) {
            foreach ($admins as $admin) {
                $admin->notify(new ContactBirthdayNotification($contact));
                $count++;
            }
        }

        if ($count > 0) {
            $this->info('Enviadas ' . $count . ' notificación(es) de cumpleaños a ' . $admins->count() . ' administrador(es).');
        }

        return self::SUCCESS;
    }
}
