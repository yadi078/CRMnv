<?php

namespace App\Services;

use App\Models\Reminder;
use App\Notifications\ReminderDueNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Envía notificaciones repetidas después de la hora del recordatorio (fase "due"),
 * según intervalo y tipo configurados en el modelo Reminder.
 *
 * No interfiere con pre5/pre2/due/post3 de ReminderDueNotifier: solo añade fase alarm_repeat.
 */
class ReminderAlarmRepeater
{
    /**
     * @return int Notificaciones alarm_repeat enviadas en esta ejecución
     */
    public function dispatchRepeats(?int $onlyUserId = null, ?Carbon $now = null): int
    {
        $now = $now ?? now();
        $tz = config('app.timezone');
        $now = $now->copy()->timezone($tz);

        $query = Reminder::query()
            ->where('is_done', false)
            ->where('alarm_repeat_enabled', true)
            ->whereNull('alarm_confirmed_at')
            ->whereNotNull('alarm_repeat_interval_minutes')
            ->whereNotNull('alarm_repeat_type')
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
                Log::warning('ReminderAlarmRepeater: '.$e->getMessage(), ['reminder_id' => $reminder->id]);
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

        if ($now->lt($effective)) {
            return false;
        }

        // Primera alarma (due) debe haber ocurrido: notification_sent_at o ya pasó la hora efectiva con anillo inicial
        if (! $reminder->notification_sent_at) {
            return false;
        }

        $this->backfillAlarmAnchorIfMissing($reminder, $effective);

        if (! $reminder->alarm_last_ring_at) {
            return false;
        }

        if ($this->shouldStopByPolicy($reminder, $now)) {
            return false;
        }

        $interval = max(1, (int) $reminder->alarm_repeat_interval_minutes);
        $nextRing = $reminder->alarm_last_ring_at->copy()->timezone(config('app.timezone'))->addMinutes($interval);

        if ($now->lt($nextRing)) {
            return false;
        }

        $reminder->user?->notify(new ReminderDueNotification($reminder, 'alarm_repeat'));

        $reminder->update([
            'alarm_last_ring_at' => $nextRing,
            'alarm_rings_count' => $reminder->alarm_rings_count + 1,
        ]);

        return true;
    }

    /**
     * Registros antiguos o migración: anclar último toque al inicio efectivo del evento.
     */
    protected function backfillAlarmAnchorIfMissing(Reminder $reminder, Carbon $effective): void
    {
        if ($reminder->alarm_last_ring_at !== null) {
            return;
        }

        $anchor = $reminder->notification_sent_at
            ? $reminder->notification_sent_at->copy()->timezone(config('app.timezone'))
            : $effective->copy();

        $payload = ['alarm_last_ring_at' => $anchor];
        if ($reminder->alarm_repeat_type === Reminder::ALARM_REPEAT_DURATION
            && $reminder->alarm_window_started_at === null) {
            $payload['alarm_window_started_at'] = $anchor->copy();
        }
        $reminder->update($payload);
        $reminder->refresh();
    }

    protected function shouldStopByPolicy(Reminder $reminder, Carbon $now): bool
    {
        $type = $reminder->alarm_repeat_type;

        if ($type === Reminder::ALARM_REPEAT_TIMES) {
            $max = max(1, (int) ($reminder->alarm_repeat_value ?? 1));

            return $reminder->alarm_rings_count >= $max;
        }

        if ($type === Reminder::ALARM_REPEAT_DURATION) {
            $minutes = max(1, (int) ($reminder->alarm_repeat_value ?? 1));
            $windowStart = $reminder->alarm_window_started_at ?? $reminder->alarm_last_ring_at;
            if (! $windowStart) {
                return false;
            }
            $end = $windowStart->copy()->timezone(config('app.timezone'))->addMinutes($minutes);

            return $now->gte($end);
        }

        return false;
    }
}
