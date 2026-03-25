<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\User;
use App\Notifications\ContactBirthdayNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Envía a los administradores una notificación el mismo día calendario del cumpleaños
 * (mes y día de fecha_cumpleaños = mes y día de "hoy" en la zona horaria de la app).
 */
class ContactBirthdayNotifier
{
    /**
     * @return int Número de notificaciones nuevas guardadas (admin × contacto)
     */
    public function notifyAdminsForToday(?Carbon $today = null): int
    {
        $tz = config('app.timezone');
        $today = ($today ?? Carbon::now($tz))->copy()->startOfDay();

        $month = (int) $today->month;
        $day = (int) $today->day;

        $contacts = Contact::query()
            ->whereNotNull('fecha_cumpleanos')
            ->whereMonth('fecha_cumpleanos', $month)
            ->whereDay('fecha_cumpleanos', $day)
            ->with('company')
            ->get();

        $admins = User::administradoresParaNotificaciones();

        if ($admins->isEmpty()) {
            return 0;
        }

        if ($contacts->isEmpty()) {
            return 0;
        }

        $sent = 0;
        foreach ($contacts as $contact) {
            foreach ($admins as $admin) {
                try {
                    if ($this->alreadyNotifiedToday($admin, $contact, $today)) {
                        continue;
                    }
                    $admin->notify(new ContactBirthdayNotification($contact, $today));
                    $sent++;
                } catch (\Throwable $e) {
                    Log::warning('ContactBirthdayNotifier: '.$e->getMessage(), [
                        'contact_id' => $contact->id,
                        'admin_id' => $admin->id,
                    ]);
                }
            }
        }

        return $sent;
    }

    protected function alreadyNotifiedToday(User $admin, Contact $contact, Carbon $today): bool
    {
        $dateStr = $today->toDateString();

        try {
            return $admin->notifications()
                ->where('type', ContactBirthdayNotification::class)
                ->whereDate('created_at', $dateStr)
                ->where('data->contact_id', $contact->id)
                ->exists();
        } catch (\Throwable) {
            return $admin->notifications()
                ->where('type', ContactBirthdayNotification::class)
                ->whereDate('created_at', $dateStr)
                ->get()
                ->contains(function ($n) use ($contact) {
                    $d = is_array($n->data) ? $n->data : [];

                    return (int) ($d['contact_id'] ?? 0) === (int) $contact->id;
                });
        }
    }
}
