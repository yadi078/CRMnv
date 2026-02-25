<?php

namespace App\Console\Commands;

use App\Models\FollowUp;
use App\Models\User;
use App\Notifications\FollowUpReminderNotification;
use Illuminate\Console\Command;

/**
 * Crea notificaciones de recordatorio (próximas 2 h) y vencidos para seguimientos.
 * Notifica al usuario asignado/creador y a los administradores.
 */
class SendFollowUpReminders extends Command
{
    protected $signature = 'follow-ups:send-reminders';

    protected $description = 'Envía notificaciones de recordatorio y seguimientos vencidos a usuarios y admins';

    public function handle(): int
    {
        $now = now();
        $inTwoHours = $now->copy()->addHours(2);

        // Recordatorios: fecha_alarma en las próximas 2 horas, no completado, no notificado
        $upcoming = FollowUp::with(['company', 'contact', 'asignado', 'creator'])
            ->where('completado', false)
            ->whereNull('notification_sent_at')
            ->whereBetween('fecha_alarma', [$now, $inTwoHours])
            ->get();

        foreach ($upcoming as $followUp) {
            $this->sendToResponsibleAndAdmins($followUp, FollowUpReminderNotification::TYPE_REMINDER);
            $followUp->update(['notification_sent_at' => $now]);
        }

        // Vencidos: fecha_alarma ya pasó, no completado, no notificado
        $overdue = FollowUp::with(['company', 'contact', 'asignado', 'creator'])
            ->where('completado', false)
            ->whereNull('notification_sent_at')
            ->where('fecha_alarma', '<', $now)
            ->get();

        foreach ($overdue as $followUp) {
            $this->sendToResponsibleAndAdmins($followUp, FollowUpReminderNotification::TYPE_OVERDUE);
            $followUp->update(['notification_sent_at' => $now]);
        }

        $total = $upcoming->count() + $overdue->count();
        if ($total > 0) {
            $this->info("Enviadas {$total} notificaciones de seguimiento.");
        }

        return self::SUCCESS;
    }

    private function sendToResponsibleAndAdmins(FollowUp $followUp, string $type): void
    {
        $notification = new FollowUpReminderNotification($followUp, $type);

        $user = $followUp->asignado ?? $followUp->creator;
        if ($user) {
            $user->notify($notification);
        }

        $admins = User::role(['admin', 'administrador'])->get();
        foreach ($admins as $admin) {
            if ($admin->id !== $user?->id) {
                $admin->notify($notification);
            }
        }
    }
}
