<?php

namespace App\Services;

use App\Models\Reminder;
use App\Notifications\ReminderDueNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Reglas de aviso (con hora o "todo el día" con hora concreta en start_at):
 * - Primera notificación: en cuanto entramos en los 10 minutos anteriores a la hora programada (una sola vez).
 * - Segunda notificación: a la hora programada o después (una sola vez), si aún no se envió.
 */
class ReminderDueNotifier
{
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

        return $this->processTimed($reminder, $now, $start);
    }

    /**
     * Aviso 10 minutos antes (una vez) y aviso en la hora programada (una vez).
     */
    protected function processTimed(Reminder $reminder, Carbon $now, Carbon $start): bool
    {
        $windowStart = $start->copy()->subMinutes(self::PRE_EVENT_MINUTES);

        if ($now->gte($start) && ! $reminder->notification_sent_at) {
            $reminder->user->notify(new ReminderDueNotification($reminder, 'due'));
            $reminder->update([
                'notification_sent_at' => $now,
            ]);

            return true;
        }

        if ($now->gte($windowStart) && $now->lt($start) && ! $reminder->pre_notification_sent_at) {
            $reminder->user->notify(new ReminderDueNotification($reminder, 'pre'));
            $reminder->update([
                'pre_notification_sent_at' => $now,
            ]);

            return true;
        }

        return false;
    }
}
