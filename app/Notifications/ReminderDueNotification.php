<?php

namespace App\Notifications;

use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

class ReminderDueNotification extends Notification
{
    /** @param  'pre15'|'pre10'|'pre5'|'pre2'|'due'|'post3'  $phase */
    public function __construct(
        public Reminder $reminder,
        public string $phase = 'due',
    ) {
        $allowed = ['pre15', 'pre10', 'pre5', 'pre2', 'due', 'post3'];
        $this->phase = in_array($phase, $allowed, true) ? $phase : 'due';
    }

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

        $tipoEtiqueta = null;
        if ($r->tipo_accion) {
            $tipoEtiqueta = Reminder::TIPO_ACCION_OPCIONES[$r->tipo_accion] ?? ucfirst((string) $r->tipo_accion);
        }

        return [
            'reminder_id' => $r->id,
            'titulo' => $r->title ?: null,
            'tipo_accion' => $tipoEtiqueta,
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

        $rid = isset($data['reminder_id']) ? (int) $data['reminder_id'] : 0;
        if ($rid < 1 && isset($data['reminderId'])) {
            $rid = (int) $data['reminderId'];
        }
        if ($rid < 1) {
            $det = $data['reminder_detalle'] ?? [];
            if (is_array($det) && isset($det['reminder_id'])) {
                $rid = (int) $det['reminder_id'];
            }
        }
        if ($rid < 1) {
            $guessed = self::guessReminderIdForLegacyPayload($data, $userId);
            if ($guessed !== null) {
                $rid = $guessed;
                $data['reminder_id'] = $rid;
            }
        }

        $hasDetail = ! empty($data['reminder_detalle']) && is_array($data['reminder_detalle']);
        if ($rid >= 1 && ! $hasDetail) {
            $r = Reminder::where('user_id', $userId)->find($rid);
            if ($r) {
                $data['reminder_detalle'] = self::reminderSnapshot($r);
                $hasDetail = true;
            }
        }

        if ($rid >= 1 && $hasDetail && is_array($data['reminder_detalle'])) {
            $data['reminder_detalle']['reminder_id'] = $rid;
        }

        return $data;
    }

    /**
     * Notificaciones antiguas sin reminder_id: intentar localizar el registro por título + fecha.
     */
    protected static function guessReminderIdForLegacyPayload(array $data, int $userId): ?int
    {
        $detail = is_array($data['reminder_detalle'] ?? null) ? $data['reminder_detalle'] : [];
        $title = trim((string) ($detail['titulo'] ?? $data['titulo'] ?? ''));
        if ($title === '') {
            return null;
        }

        $fi = trim((string) ($detail['fecha_inicio'] ?? $data['fecha_prevista'] ?? ''));
        $tz = config('app.timezone');
        $candidateStart = null;
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})$/', $fi, $m)) {
            $candidateStart = Carbon::create((int) $m[3], (int) $m[2], (int) $m[1], (int) $m[4], (int) $m[5], 0, $tz);
        } elseif ($fi !== '') {
            try {
                $candidateStart = Carbon::parse($fi, $tz);
            } catch (\Throwable) {
                $candidateStart = null;
            }
        }

        $q = Reminder::query()
            ->where('user_id', $userId)
            ->where('title', $title)
            ->where('is_done', false);

        if ($candidateStart) {
            $from = $candidateStart->copy()->subMinutes(3);
            $to = $candidateStart->copy()->addMinutes(3);
            $q->where(function ($qq) use ($from, $to) {
                $qq->whereBetween('start_at', [$from, $to])
                    ->orWhereBetween('scheduled_for', [$from, $to]);
            });
        }

        $r = $q->orderByDesc('id')->first();

        return $r?->id;
    }

    /**
     * Texto corto para lista / notificación del navegador.
     *
     * @param  array<string, mixed>  $data
     */
    public static function reminderSummaryLine(array $data): string
    {
        $phase = (string) ($data['alert_phase'] ?? '');
        $prefix = match ($phase) {
            'pre15' => 'En 15 min: ',
            'pre10' => 'En 10 min: ',
            'pre5' => 'En 5 min: ',
            'pre2' => 'En 2 min: ',
            'post3' => '+3 min (seguimiento): ',
            default => '',
        };

        $det = $data['reminder_detalle'] ?? [];
        if (is_array($det)) {
            $parts = array_filter([
                $det['titulo'] ?? null,
                $det['tipo_accion'] ?? null,
                $det['nombre_cliente'] ?? null,
                $det['empresa'] ?? null,
                $det['fecha_inicio'] ?? null,
            ]);
            if ($parts !== []) {
                return $prefix.implode(' · ', $parts);
            }
        }

        return $prefix.(string) ($data['mensaje'] ?? '');
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

        $titulo = match ($this->phase) {
            'pre15' => 'Recordatorio en 15 minutos',
            'pre10' => 'Recordatorio en 10 minutos',
            'pre5' => 'Recordatorio en 5 minutos',
            'pre2' => 'Recordatorio en 2 minutos',
            'post3' => 'Recordatorio: 3 min después de la hora',
            default => 'Recordatorio',
        };

        return [
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => 'recordatorio',
            'alert_phase' => $this->phase,
            'reminder_id' => $this->reminder->id,
            'fecha_prevista' => $fecha,
            'reminder_detalle' => self::reminderSnapshot($this->reminder),
        ];
    }
}
