<?php

namespace App\Services;

use App\Models\Reminder;
use App\Models\User;
use App\Notifications\ReminderDueNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Reglas de aviso:
 * - Alertas previas: 10 y 5 minutos antes (una vez cada fase), en ventanas disjuntas.
 * - Alarma principal: en la hora programada o después (una sola vez).
 * - No se envía una alerta “previa” si el recordatorio se creó después de ese instante
 *   (evita que suene “al guardar” cuando la hora queda a pocos minutos).
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
        $start = $reminder->start_at ?? $reminder->scheduled_for;
        if (! $start) {
            return false;
        }

        $start = $start->copy()->timezone(config('app.timezone'));

        $effective = $this->effectiveNotificationStart($reminder, $start);

        return $this->processTimed($reminder, $now, $effective);
    }

    /**
     * Hora efectiva para avisos: "todo el día" usa una hora configurada, no 00:00.
     */
    protected function effectiveNotificationStart(Reminder $reminder, Carbon $start): Carbon
    {
        $tz = config('app.timezone');
        $start = $start->copy()->timezone($tz);

        if ($reminder->all_day) {
            $timeStr = (string) config('crm.reminder_all_day_notify_time', '09:00');

            return Carbon::parse($start->format('Y-m-d').' '.$timeStr, $tz);
        }

        return $start;
    }

    protected function processTimed(Reminder $reminder, Carbon $now, Carbon $start): bool
    {
        $tz = config('app.timezone');
        $created = $reminder->created_at?->copy()->timezone($tz) ?? $now;

        // 1) Aviso en hora (fase due).
        if ($now->gte($start) && ! $this->alreadySentPhase($reminder, 'due')) {
            $reminder->update([
                'notification_sent_at' => $now,
            ]);
            $reminder->user?->notify(new ReminderDueNotification($reminder, 'due'));

            return true;
        }

        $t5 = $start->copy()->subMinutes(5);
        $t10 = $start->copy()->subMinutes(10);

        // 2) 5 minutos antes: [T-5, T)
        if ($now->gte($t5) && $now->lt($start) && ! $this->alreadySentPhase($reminder, 'pre5')) {
            if ($created->lte($t5)) {
                $reminder->update([
                    'pre_notification_sent_at' => $now,
                ]);
                $reminder->user?->notify(new ReminderDueNotification($reminder, 'pre5'));

                return true;
            }
        }

        // 3) 10 minutos antes: [T-10, T-5)
        if ($now->gte($t10) && $now->lt($t5) && ! $this->alreadySentPhase($reminder, 'pre10')) {
            if ($created->lte($t10)) {
                $reminder->update([
                    'pre_notification_sent_at' => $now,
                ]);
                $reminder->user?->notify(new ReminderDueNotification($reminder, 'pre10'));

                return true;
            }
        }

        return false;
    }

    protected function alreadySentPhase(Reminder $reminder, string $phase): bool
    {
        return DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $reminder->user_id)
            ->where('type', ReminderDueNotification::class)
            ->where('data->tipo', 'recordatorio')
            ->where('data->reminder_id', $reminder->id)
            ->where('data->alert_phase', $phase)
            ->exists();
    }
}
