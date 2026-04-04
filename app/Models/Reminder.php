<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    public const TIPO_ACCION_OPCIONES = [
        'llamada' => 'Llamada',
        'reunión' => 'Reunión',
        'cierre' => 'Cierre',
    ];

    public const ALARM_REPEAT_UNTIL_CONFIRMED = 'until_confirmed';

    public const ALARM_REPEAT_TIMES = 'times';

    public const ALARM_REPEAT_DURATION = 'duration';

    protected $fillable = [
        'user_id',
        'title',
        'tipo_accion',
        'description',
        'extension',
        'nombre_cliente',
        'empresa',
        'correo_electronico',
        'numero_telefonico',
        'area',
        'puesto_trabajo',
        'scheduled_for',
        'start_at',
        'end_at',
        'all_day',
        'repeat',
        'deadline_at',
        'is_done',
        'notification_sent_at',
        'pre_notification_sent_at',
        'last_recurring_notify_at',
        'alarm_repeat_enabled',
        'alarm_repeat_interval_minutes',
        'alarm_repeat_type',
        'alarm_repeat_value',
        'alarm_confirmed_at',
        'alarm_last_ring_at',
        'alarm_rings_count',
        'alarm_window_started_at',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'all_day' => 'boolean',
        'deadline_at' => 'datetime',
        'is_done' => 'boolean',
        'notification_sent_at' => 'datetime',
        'pre_notification_sent_at' => 'datetime',
        'last_recurring_notify_at' => 'datetime',
        'alarm_repeat_enabled' => 'boolean',
        'alarm_confirmed_at' => 'datetime',
        'alarm_last_ring_at' => 'datetime',
        'alarm_window_started_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hora efectiva para avisos (misma lógica que ReminderDueNotifier): todo el día usa hora configurada.
     */
    public function effectiveNotificationStart(): ?Carbon
    {
        $start = $this->start_at ?? $this->scheduled_for;
        if (! $start) {
            return null;
        }

        $tz = config('app.timezone');
        $start = $start->copy()->timezone($tz);

        if ($this->all_day) {
            $timeStr = (string) config('crm.reminder_all_day_notify_time', '09:00');

            return Carbon::parse($start->format('Y-m-d').' '.$timeStr, $tz);
        }

        return $start;
    }

    /**
     * El usuario debe confirmar desde el modal para detener ciclos de alarma repetida.
     */
    public function needsAlarmConfirmation(): bool
    {
        return $this->alarm_repeat_enabled
            && $this->alarm_confirmed_at === null;
    }
}

