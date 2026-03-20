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

    /**
     * Campos listos para mostrar en el modal / detalle (solo valores no vacíos en la vista).
     *
     * @return array<string, string|null>
     */
    public static function reminderSnapshot(Reminder $r): array
    {
        $start = $r->start_at ?? $r->scheduled_for;
        $fechaInicio = null;
        if ($start) {
            $fechaInicio = $r->all_day
                ? $start->format('d/m/Y') . ' (Todo el día)'
                : $start->format('d/m/Y H:i');
        }

        return [
            'titulo' => $r->title ?: null,
            'descripcion' => $r->description ?: null,
            'nombre_cliente' => $r->nombre_cliente ?: null,
            'empresa' => $r->empresa ?: null,
            'correo_electronico' => $r->correo_electronico ?: null,
            'numero_telefonico' => $r->numero_telefonico ?: null,
            'extension' => $r->extension ?: null,
            'area' => $r->area ?: null,
            'puesto_trabajo' => $r->puesto_trabajo ?: null,
            'fecha_inicio' => $fechaInicio,
            'fecha_limite' => $r->deadline_at ? $r->deadline_at->format('d/m/Y') : null,
            'repeticion' => $r->repeat ?: null,
        ];
    }

    /**
     * Completa reminder_detalle en memoria para notificaciones guardadas antes del snapshot.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function enrichStoredData(array $data, int $userId): array
    {
        if (($data['tipo'] ?? '') !== 'recordatorio') {
            return $data;
        }
        if (! empty($data['reminder_detalle']) && is_array($data['reminder_detalle'])) {
            return $data;
        }
        $rid = $data['reminder_id'] ?? null;
        if (! $rid) {
            return $data;
        }
        $r = Reminder::where('user_id', $userId)->find($rid);
        if ($r) {
            $data['reminder_detalle'] = self::reminderSnapshot($r);
        }

        return $data;
    }

    /**
     * Texto corto para lista / notificación del navegador.
     *
     * @param  array<string, mixed>  $data
     */
    public static function reminderSummaryLine(array $data): string
    {
        $det = $data['reminder_detalle'] ?? [];
        if (is_array($det)) {
            $parts = array_filter([
                $det['titulo'] ?? null,
                $det['nombre_cliente'] ?? null,
                $det['empresa'] ?? null,
                $det['fecha_inicio'] ?? null,
            ]);
            if ($parts !== []) {
                return implode(' · ', $parts);
            }
        }

        return (string) ($data['mensaje'] ?? '');
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
            'reminder_detalle' => self::reminderSnapshot($this->reminder),
        ];
    }
}
