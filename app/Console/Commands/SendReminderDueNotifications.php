<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Notifications\ReminderDueNotification;
use Illuminate\Console\Command;

class SendReminderDueNotifications extends Command
{
    protected $signature = 'reminders:send-due';

    protected $description = 'Envía notificaciones por recordatorios cuya fecha/hora ya llegó';

    public function handle(): int
    {
        $now = now();
        $limit = $now->copy()->addMinutes(10);

        // Notificar cuando falten 10 min o menos (o ya pasó): start_at <= now + 10 min
        $due = Reminder::whereNull('notification_sent_at')
            ->where('is_done', false)
            ->where(function ($q) use ($limit) {
                $q->where(function ($q2) use ($limit) {
                    $q2->whereNotNull('start_at')->where('start_at', '<=', $limit);
                })->orWhere(function ($q2) use ($limit) {
                    $q2->whereNull('start_at')->whereNotNull('scheduled_for')->where('scheduled_for', '<=', $limit);
                });
            })
            ->get();

        foreach ($due as $reminder) {
            $reminder->user->notify(new ReminderDueNotification($reminder));
            $reminder->update(['notification_sent_at' => $now]);
        }

        if ($due->count() > 0) {
            $this->info('Enviadas ' . $due->count() . ' notificaciones de recordatorio.');
        }

        return self::SUCCESS;
    }
}
