<?php

namespace App\Services;

use App\Models\Reminder;
use App\Models\User;
use App\Notifications\ReminderDueNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Un solo aviso por recordatorio: solo durante el minuto programado [hora, hora+1min).
 * Fuera de esa ventana no se envía (ni antes, ni después; si no hubo polling en ese minuto, no hay aviso).
 */
class ReminderDueNotifier
{

    /**
     * Procesa recordatorios pendientes y envía notificaciones según reglas.
     *
     * @return int Número de notificaciones enviadas en esta ejecución
     */
    public function dispatchDue(?int $onlyUserId = null, ?Carbon $now = null): int
    {
        $now = $now ?? now();
        $tz = config('app.timezone');
        $now = $now->copy()->timezone($tz);

        $query = Reminder::query()
            ->where('is_done', false)
            ->where(function ($q) {
                $q->whereNotNull('start_at')->orWhereNotNull('scheduled_for');
            });

        if ($onlyUserId !== null) {
            $query->where('user_id', $onlyUserId);
        }

        $sent = 0;
        foreach ($query->get() as $reminder) {
            try {
                if ($this->processOne($reminder, $now)) {
                    $sent++;
                }
            } catch (\Throwable $e) {
                Log::warning('ReminderDueNotifier: ' . $e->getMessage(), ['reminder_id' => $reminder->id]);
            }
        }

        return $sent;
    }

    protected function processOne(Reminder $reminder, Carbon $now): bool
    {
        $effective = $reminder->effectiveNotificationStart();
        if (! $effective) {
            return false;
        }

        return $this->processTimed($reminder, $now, $effective);
    }

    protected function processTimed(Reminder $reminder, Carbon $now, Carbon $start): bool
    {
        $windowEnd = $start->copy()->addMinute();
        // Solo dentro del minuto calendario programado [start, start+1min): ni antes, ni después.
        if ($now->lt($start) || $now->gte($windowEnd)) {
            return false;
        }

        if ($this->alreadySentPhase($reminder, 'due')) {
            return false;
        }

        $reminder->update(['notification_sent_at' => $now]);
        $reminder->user?->notify(new ReminderDueNotification($reminder, 'due'));

        return true;
    }

    protected function alreadySentPhase(Reminder $reminder, string $phase): bool
    {
        $q = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $reminder->user_id)
            ->where('type', ReminderDueNotification::class);

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            return $q->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(data, \'$.tipo\')) = ?', ['recordatorio'])
                ->whereRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data, \'$.reminder_id\')) AS UNSIGNED) = ?', [(int) $reminder->id])
                ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(data, \'$.alert_phase\')) = ?', [$phase])
                ->exists();
        }

        if ($driver === 'sqlite') {
            return $q->whereRaw('json_extract(data, \'$.tipo\') = ?', ['recordatorio'])
                ->whereRaw('CAST(json_extract(data, \'$.reminder_id\') AS INTEGER) = ?', [(int) $reminder->id])
                ->whereRaw('json_extract(data, \'$.alert_phase\') = ?', [$phase])
                ->exists();
        }

        if ($driver === 'pgsql') {
            return $q->whereRaw('(data::json->>\'tipo\') = ?', ['recordatorio'])
                ->whereRaw('(NULLIF(TRIM(data::json->>\'reminder_id\'), \'\'))::bigint = ?', [(int) $reminder->id])
                ->whereRaw('(data::json->>\'alert_phase\') = ?', [$phase])
                ->exists();
        }

        return DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $reminder->user_id)
            ->where('type', ReminderDueNotification::class)
            ->get(['data'])
            ->contains(function ($row) use ($reminder, $phase) {
                $d = json_decode($row->data, true) ?: [];

                return ($d['tipo'] ?? '') === 'recordatorio'
                    && (int) ($d['reminder_id'] ?? 0) === (int) $reminder->id
                    && ($d['alert_phase'] ?? '') === $phase;
            });
    }
}
