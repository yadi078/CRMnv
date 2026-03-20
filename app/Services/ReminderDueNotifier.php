<?php

namespace App\Services;

use App\Models\Reminder;
use App\Notifications\ReminderDueNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Reglas de aviso:
 * - Sin "todo el día": una sola notificación cuando falten 10 minutos o menos (o ya pasó la hora y aún no se avisó).
 * - Con "todo el día": en la fecha del recordatorio, antes de la hora fijada, una notificación cada 10 minutos
 *   (incluye el intervalo de los últimos 10 min antes de la hora).
 */
class ReminderDueNotifier
{
    public const RECURRING_INTERVAL_MINUTES = 10;

    public const PRE_EVENT_MINUTES = 10;

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

        if ($reminder->all_day) {
            return $this->processAllDay($reminder, $now, $start);
        }

        return $this->processTimed($reminder, $now, $start);
    }

    /**
     * Una sola notificación: desde 10 minutos antes de la hora (o si ya pasó y no se notificó).
     */
    protected function processTimed(Reminder $reminder, Carbon $now, Carbon $start): bool
    {
        if ($reminder->notification_sent_at) {
            return false;
        }

        $windowStart = $start->copy()->subMinutes(self::PRE_EVENT_MINUTES);
        if ($now->lt($windowStart)) {
            return false;
        }

        $reminder->user->notify(new ReminderDueNotification($reminder));
        $reminder->update([
            'notification_sent_at' => $now,
        ]);

        return true;
    }

    /**
     * Mismo día calendario que start_at, antes de la hora: notificación cada 10 minutos.
     * Al llegar o pasar la hora, se cierra el ciclo (sin más avisos recurrentes).
     */
    protected function processAllDay(Reminder $reminder, Carbon $now, Carbon $start): bool
    {
        if ($now->gte($start)) {
            if (! $reminder->notification_sent_at) {
                $reminder->update(['notification_sent_at' => $now]);
            }

            return false;
        }

        if (! $now->isSameDay($start)) {
            return false;
        }

        $last = $reminder->last_recurring_notify_at;
        if ($last) {
            $last = $last->copy()->timezone(config('app.timezone'));
            if ($last->diffInMinutes($now) < self::RECURRING_INTERVAL_MINUTES) {
                return false;
            }
        }

        $reminder->user->notify(new ReminderDueNotification($reminder));
        $reminder->update([
            'last_recurring_notify_at' => $now,
        ]);

        return true;
    }
}
