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
 * - Alertas previas: 15, 10 y 5 minutos antes (una vez cada fase).
 * - Alarma principal: en la hora programada o después (una sola vez).
 */
class ReminderDueNotifier
{
    /** @var array<int> */
    public const PRE_EVENT_MINUTES = [15, 10, 5];

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

    protected function processTimed(Reminder $reminder, Carbon $now, Carbon $start): bool
    {
        // 1) Aviso en hora (fase due).
        if ($now->gte($start) && ! $this->alreadySentPhase($reminder, 'due')) {
            $reminder->update([
                'notification_sent_at' => $now,
            ]);
            $reminder->user?->notify(new ReminderDueNotification($reminder, 'due'));

            return true;
        }

        // 2) Avisos previos (fases pre15/pre10/pre5).
        foreach (self::PRE_EVENT_MINUTES as $minutes) {
            $phase = 'pre' . $minutes;
            $windowStart = $start->copy()->subMinutes($minutes);
            if ($now->gte($windowStart) && $now->lt($start) && ! $this->alreadySentPhase($reminder, $phase)) {
                $reminder->update([
                    'pre_notification_sent_at' => $now,
                ]);
                $reminder->user?->notify(new ReminderDueNotification($reminder, $phase));

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
