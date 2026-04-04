<?php

namespace App\Services;

use App\Models\Reminder;
use App\Models\User;
use App\Notifications\ReminderDueNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Reglas de aviso (una notificación por fase):
 * - 5 minutos antes: durante el minuto calendario que empieza en T−5.
 * - 2 minutos antes: durante el minuto calendario que empieza en T−2.
 * - Hora exacta: cuando llega o pasa start_at efectivo.
 * - +3 minutos después: durante el minuto calendario que empieza en T+3 (recordatorio de seguimiento).
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
        // 1) Hora exacta.
        if ($now->gte($start) && ! $this->alreadySentPhase($reminder, 'due')) {
            $reminder->refresh();

            $payload = ['notification_sent_at' => $now];
            if ($reminder->alarm_repeat_enabled) {
                $payload['alarm_last_ring_at'] = $start->copy();
                if ($reminder->alarm_repeat_type === Reminder::ALARM_REPEAT_DURATION
                    && $reminder->alarm_window_started_at === null) {
                    $payload['alarm_window_started_at'] = $start->copy();
                }
            }
            $reminder->update($payload);
            $reminder->user?->notify(new ReminderDueNotification($reminder, 'due'));

            return true;
        }

        // 2) Antes de la hora: T−2 y T−5 (ventanas de un minuto).
        if ($now->lt($start)) {
            $pre2MinuteStart = $start->copy()->subMinutes(2)->startOfMinute();
            if ($now->gte($pre2MinuteStart)
                && $now->lt($pre2MinuteStart->copy()->addMinute())
                && ! $this->alreadySentPhase($reminder, 'pre2')) {
                $reminder->update([
                    'pre_notification_sent_at' => $now,
                ]);
                $reminder->user?->notify(new ReminderDueNotification($reminder, 'pre2'));

                return true;
            }

            $pre5MinuteStart = $start->copy()->subMinutes(5)->startOfMinute();
            if ($now->gte($pre5MinuteStart)
                && $now->lt($pre5MinuteStart->copy()->addMinute())
                && ! $this->alreadySentPhase($reminder, 'pre5')) {
                $reminder->update([
                    'pre_notification_sent_at' => $now,
                ]);
                $reminder->user?->notify(new ReminderDueNotification($reminder, 'pre5'));

                return true;
            }

            return false;
        }

        // 3) Después de la hora: T+3 (un disparo en el minuto [T+3, T+4)).
        $post3MinuteStart = $start->copy()->addMinutes(3)->startOfMinute();
        if ($now->gte($post3MinuteStart)
            && $now->lt($post3MinuteStart->copy()->addMinute())
            && ! $this->alreadySentPhase($reminder, 'post3')) {
            $reminder->update([
                'pre_notification_sent_at' => $now,
            ]);
            $reminder->user?->notify(new ReminderDueNotification($reminder, 'post3'));

            return true;
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
