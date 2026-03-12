<?php

namespace App\Notifications;

use App\Models\Reminder;
use Illuminate\Notifications\Notification;

class ReminderDueNotification extends Notification
{
    public function __construct(
        public Reminder $reminder
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $fecha = $this->reminder->start_at
            ? $this->reminder->all_day
                ? $this->reminder->start_at->format('d M Y') . ' (Todo el día)'
                : $this->reminder->start_at->format('d M Y H:i')
            : 'Sin fecha';

        $mensaje = $this->reminder->description
            ? $this->reminder->title . ' — ' . $this->reminder->description
            : $this->reminder->title;

        return [
            'titulo' => 'Recordatorio',
            'mensaje' => $mensaje,
            'tipo' => 'recordatorio',
            'reminder_id' => $this->reminder->id,
            'fecha_prevista' => $fecha,
        ];
    }
}
