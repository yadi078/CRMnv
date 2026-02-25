<?php

namespace App\Notifications;

use App\Models\FollowUp;
use Illuminate\Notifications\Notification;

/**
 * Notificación de seguimiento: recordatorio (próxima llamada/reunión/cierre)
 * o seguimiento vencido.
 */
class FollowUpReminderNotification extends Notification
{
    public const TYPE_REMINDER = 'reminder';
    public const TYPE_OVERDUE = 'overdue';

    public function __construct(
        public FollowUp $followUp,
        public string $type = self::TYPE_REMINDER
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
        $companyName = $this->followUp->company?->nombre_comercial ?? 'Sin empresa';
        $contactName = $this->followUp->contact?->nombre_completo ?? null;
        $tipo = ucfirst($this->followUp->tipo_accion);
        $fecha = $this->followUp->fecha_alarma->format('d/m/Y H:i');

        if ($this->type === self::TYPE_OVERDUE) {
            $titulo = "Seguimiento vencido: {$tipo}";
            $mensaje = "{$tipo} a {$companyName}" . ($contactName ? " ({$contactName})" : '') . " programada el {$fecha}.";
        } else {
            $titulo = "Recordatorio: {$tipo} programada";
            $mensaje = "{$tipo} a {$companyName}" . ($contactName ? " ({$contactName})" : '') . " hoy a las " . $this->followUp->fecha_alarma->format('H:i') . ".";
        }

        return [
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => 'seguimiento',
            'type' => $this->type === self::TYPE_OVERDUE ? 'follow_up_overdue' : 'follow_up_reminder',
            'message' => $mensaje,
            'follow_up_id' => $this->followUp->id,
            'company_name' => $companyName,
            'tipo_accion' => $this->followUp->tipo_accion,
            'fecha_alarma' => $this->followUp->fecha_alarma->toIso8601String(),
        ];
    }
}
