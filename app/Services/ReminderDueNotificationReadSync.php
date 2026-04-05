<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\ReminderDueNotification;
use Illuminate\Support\Facades\DB;

/**
 * Marca como leídas todas las notificaciones no leídas de recordatorio (ReminderDue)
 * asociadas a un mismo reminder_id, para que dejen de aparecer en polling y no suene la alarma.
 */
class ReminderDueNotificationReadSync
{
    public function markAllUnreadForReminder(User $user, int $reminderId): int
    {
        if ($reminderId < 1) {
            return 0;
        }

        $q = $user->notifications()
            ->whereNull('read_at')
            ->where('type', ReminderDueNotification::class);

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            return (int) $q->whereRaw(
                'CAST(JSON_UNQUOTE(JSON_EXTRACT(data, \'$.reminder_id\')) AS UNSIGNED) = ?',
                [$reminderId]
            )->update(['read_at' => now()]);
        }

        if ($driver === 'sqlite') {
            return (int) $q->whereRaw(
                'CAST(json_extract(data, \'$.reminder_id\') AS INTEGER) = ?',
                [$reminderId]
            )->update(['read_at' => now()]);
        }

        if ($driver === 'pgsql') {
            return (int) $q->whereRaw(
                'CAST(NULLIF(data::json->>\'reminder_id\', \'\') AS INTEGER) = ?',
                [$reminderId]
            )->update(['read_at' => now()]);
        }

        $ids = $q->get()->filter(function ($n) use ($reminderId) {
            $raw = $n->data;
            $d = is_array($raw) ? $raw : (is_string($raw) ? (json_decode($raw, true) ?: []) : []);

            return (int) ($d['reminder_id'] ?? 0) === $reminderId;
        })->pluck('id')->all();

        if ($ids === []) {
            return 0;
        }

        return (int) $user->notifications()
            ->whereIn('id', $ids)
            ->update(['read_at' => now()]);
    }

    /**
     * Elimina todas las notificaciones de recordatorio asociadas a un reminder_id.
     */
    public function deleteAllForReminder(User $user, int $reminderId): int
    {
        if ($reminderId < 1) {
            return 0;
        }

        $q = $user->notifications()
            ->where('type', ReminderDueNotification::class);

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            return (int) $q->whereRaw(
                'CAST(JSON_UNQUOTE(JSON_EXTRACT(data, \'$.reminder_id\')) AS UNSIGNED) = ?',
                [$reminderId]
            )->delete();
        }

        if ($driver === 'sqlite') {
            return (int) $q->whereRaw(
                'CAST(json_extract(data, \'$.reminder_id\') AS INTEGER) = ?',
                [$reminderId]
            )->delete();
        }

        if ($driver === 'pgsql') {
            return (int) $q->whereRaw(
                'CAST(NULLIF(data::json->>\'reminder_id\', \'\') AS INTEGER) = ?',
                [$reminderId]
            )->delete();
        }

        $ids = $q->get()->filter(function ($n) use ($reminderId) {
            $raw = $n->data;
            $d = is_array($raw) ? $raw : (is_string($raw) ? (json_decode($raw, true) ?: []) : []);

            return (int) ($d['reminder_id'] ?? 0) === $reminderId;
        })->pluck('id')->all();

        if ($ids === []) {
            return 0;
        }

        return (int) $user->notifications()->whereIn('id', $ids)->delete();
    }
}
