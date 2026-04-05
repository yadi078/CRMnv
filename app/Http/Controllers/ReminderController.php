<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Services\ReminderDueNotificationReadSync;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReminderController extends Controller
{
    public function edit(Request $request, Reminder $reminder)
    {
        $this->authorizeReminder($request, $reminder);

        return view('reminders.edit', [
            'reminder' => $reminder,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'tipo_accion' => ['required', 'string', 'in:llamada,reunión,cierre'],
            'date' => ['required', 'date'],
            'time' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:1000'],
            'extension' => ['nullable', 'string', 'max:20'],
            'nombre_cliente' => ['nullable', 'string', 'max:255'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'correo_electronico' => ['nullable', 'email', 'max:255'],
            'numero_telefonico' => ['nullable', 'string', 'max:50'],
            'area' => ['nullable', 'string', 'max:255'],
            'puesto_trabajo' => ['nullable', 'string', 'max:255'],
            'reminder_context' => ['sometimes'],
        ]);
        unset($data['reminder_context']);

        $startAt = $this->reminderStartAtFromRequest($data);
        if ($startAt === null) {
            throw ValidationException::withMessages([
                'time' => ['La fecha u hora no es válida.'],
            ]);
        }

        $request->user()->reminders()->create(array_merge([
            'title' => $data['title'],
            'tipo_accion' => $data['tipo_accion'],
            'description' => $data['description'] ?? null,
            'extension' => $data['extension'] ?? null,
            'nombre_cliente' => $data['nombre_cliente'] ?? null,
            'empresa' => $data['empresa'] ?? null,
            'correo_electronico' => $data['correo_electronico'] ?? null,
            'numero_telefonico' => $data['numero_telefonico'] ?? null,
            'area' => $data['area'] ?? null,
            'puesto_trabajo' => $data['puesto_trabajo'] ?? null,
            'start_at' => $startAt,
            'end_at' => null,
            'all_day' => false,
            'repeat' => null,
            'deadline_at' => null,
            'scheduled_for' => $startAt,
        ], $this->disabledAlarmDefaults()));

        return back()->with('success', 'Recordatorio agregado.');
    }

    public function update(Request $request, Reminder $reminder)
    {
        $this->authorizeReminder($request, $reminder);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'tipo_accion' => ['required', 'string', 'in:llamada,reunión,cierre'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'description' => ['nullable', 'string', 'max:1000'],
            'extension' => ['nullable', 'string', 'max:20'],
            'nombre_cliente' => ['nullable', 'string', 'max:255'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'correo_electronico' => ['nullable', 'email', 'max:255'],
            'numero_telefonico' => ['nullable', 'string', 'max:50'],
            'area' => ['nullable', 'string', 'max:255'],
            'puesto_trabajo' => ['nullable', 'string', 'max:255'],
        ]);

        $oldStartStr = $reminder->start_at?->format('Y-m-d H:i:s');
        $newStartStr = Carbon::parse($data['start_at'])->format('Y-m-d H:i:s');
        $resetNotifyState = $oldStartStr !== $newStartStr;

        $payload = array_merge([
            'title' => $data['title'],
            'tipo_accion' => $data['tipo_accion'],
            'description' => $data['description'] ?? $reminder->description,
            'extension' => $data['extension'] ?? $reminder->extension,
            'nombre_cliente' => $data['nombre_cliente'] ?? $reminder->nombre_cliente,
            'empresa' => $data['empresa'] ?? $reminder->empresa,
            'correo_electronico' => $data['correo_electronico'] ?? $reminder->correo_electronico,
            'numero_telefonico' => $data['numero_telefonico'] ?? $reminder->numero_telefonico,
            'area' => $data['area'] ?? $reminder->area,
            'puesto_trabajo' => $data['puesto_trabajo'] ?? $reminder->puesto_trabajo,
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'] ?? $reminder->end_at,
            'all_day' => false,
            'repeat' => null,
            'deadline_at' => null,
            'scheduled_for' => $data['start_at'],
        ], $this->disabledAlarmDefaults());

        if ($resetNotifyState) {
            $payload['notification_sent_at'] = null;
            $payload['pre_notification_sent_at'] = null;
            $payload['last_recurring_notify_at'] = null;
            $payload['alarm_last_ring_at'] = null;
            $payload['alarm_rings_count'] = 0;
            $payload['alarm_window_started_at'] = null;
            $payload['alarm_confirmed_at'] = null;
        }

        $reminder->update($payload);

        if ($resetNotifyState) {
            // Quitar avisos anteriores para que pueda volver a generarse uno en la nueva hora (alreadySentPhase mira la tabla notifications).
            app(ReminderDueNotificationReadSync::class)->deleteAllForReminder($request->user(), $reminder->id);
        }

        return back()->with('success', 'Recordatorio actualizado.');
    }

    public function toggle(Request $request, Reminder $reminder)
    {
        $this->authorizeReminder($request, $reminder);

        $nextDone = ! $reminder->is_done;
        $update = ['is_done' => $nextDone];
        if ($nextDone) {
            $update['alarm_confirmed_at'] = now();
        } else {
            $update['alarm_confirmed_at'] = null;
        }

        $reminder->update($update);

        return back();
    }

    public function destroy(Request $request, Reminder $reminder)
    {
        $this->authorizeReminder($request, $reminder);

        app(ReminderDueNotificationReadSync::class)->deleteAllForReminder($request->user(), $reminder->id);
        $reminder->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Recordatorio eliminado.');
    }

    public function confirmAlarm(Request $request, Reminder $reminder)
    {
        $this->authorizeReminder($request, $reminder);

        $reminder->update(['alarm_confirmed_at' => now()]);
        app(ReminderDueNotificationReadSync::class)->markAllUnreadForReminder($request->user(), $reminder->id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Alarma confirmada.');
    }

    public function snooze(Request $request, Reminder $reminder)
    {
        $this->authorizeReminder($request, $reminder);

        $base = $reminder->start_at ?? $reminder->scheduled_for;
        if (! $base) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Este recordatorio no tiene fecha programada.'], 422);
            }

            return back()->with('error', 'Este recordatorio no tiene fecha programada.');
        }

        $tz = config('app.timezone');
        $newStart = $base->copy()->timezone($tz)->addMinutes(5);

        $reminder->update([
            'start_at' => $newStart,
            'scheduled_for' => $newStart,
            'notification_sent_at' => null,
            'pre_notification_sent_at' => null,
            'last_recurring_notify_at' => null,
            'alarm_last_ring_at' => null,
            'alarm_rings_count' => 0,
            'alarm_window_started_at' => null,
        ]);
        // Borrar avisos anteriores del recordatorio; si solo se marcan como leídos, alreadySentPhase() sigue viendo el 'due' y no vuelve a notificar.
        app(ReminderDueNotificationReadSync::class)->deleteAllForReminder($request->user(), $reminder->id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'new_start' => $newStart->toIso8601String(),
            ]);
        }

        return back()->with('success', 'Recordatorio aplazado 5 minutos.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function disabledAlarmDefaults(): array
    {
        return [
            'alarm_repeat_enabled' => false,
            'alarm_repeat_interval_minutes' => null,
            'alarm_repeat_type' => null,
            'alarm_repeat_value' => null,
            'alarm_last_ring_at' => null,
            'alarm_rings_count' => 0,
            'alarm_window_started_at' => null,
            'alarm_confirmed_at' => null,
        ];
    }

    protected function authorizeReminder(Request $request, Reminder $reminder): void
    {
        $ownerId = (int) $reminder->user_id;
        $currentId = (int) $request->user()->getAuthIdentifier();
        abort_unless($ownerId === $currentId, 403);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function reminderStartAtFromRequest(array $data): ?string
    {
        $date = trim((string) ($data['date'] ?? ''));
        if ($date === '') {
            return null;
        }

        $tz = config('app.timezone');
        $rawTime = trim((string) ($data['time'] ?? ''));
        $timeStr = $rawTime !== ''
            ? $rawTime
            : trim((string) config('crm.default_reminder_time', '09:00'));

        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $timeStr, $m)) {
            $h = (int) $m[1];
            $mi = (int) $m[2];
            $s = isset($m[3]) ? (int) $m[3] : 0;
            if ($h < 0 || $h > 23 || $mi < 0 || $mi > 59 || $s < 0 || $s > 59) {
                return null;
            }
            $normalizedTime = sprintf('%02d:%02d:%02d', $h, $mi, $s);
        } else {
            try {
                $normalizedTime = Carbon::parse($timeStr, $tz)->format('H:i:s');
            } catch (\Throwable) {
                $normalizedTime = '09:00:00';
            }
        }

        try {
            return Carbon::parse($date.' '.$normalizedTime, $tz)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }
}
