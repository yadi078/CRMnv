<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    /**
     * Formulario para editar fecha y datos del recordatorio (p. ej. desde «Reprogramar» en la alerta).
     */
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
            'date' => ['nullable', 'date'],
            'time' => ['nullable', 'string'],
            'all_day' => ['sometimes', 'boolean'],
            'repeat' => ['nullable', 'string', 'max:20'],
            'deadline_date' => ['nullable', 'date'],
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

        $allDay = $request->boolean('all_day');
        $startAt = null;
        if (! empty($data['date'])) {
            if ($allDay) {
                $time = config('crm.reminder_all_day_notify_time', '09:00');
            } else {
                $time = ! empty($data['time'])
                    ? $data['time']
                    : config('crm.default_reminder_time', '09:00');
            }
            $time = is_string($time) ? trim($time) : '09:00';
            $startAt = strlen($time) === 5
                ? $data['date'].' '.$time.':00'
                : $data['date'].' '.$time;
        }

        $deadlineAt = null;
        if (!empty($data['deadline_date'])) {
            $deadlineAt = $data['deadline_date'] . ' 00:00:00';
        }

        $request->user()->reminders()->create([
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
            'all_day' => $allDay,
            'repeat' => $data['repeat'] ?? null,
            'deadline_at' => $deadlineAt,
            'scheduled_for' => $startAt,
        ]);

        return back()->with('success', 'Recordatorio agregado.');
    }

    public function update(Request $request, Reminder $reminder)
    {
        $this->authorizeReminder($request, $reminder);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'tipo_accion' => ['required', 'string', 'in:llamada,reunión,cierre'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'all_day' => ['sometimes', 'boolean'],
            'repeat' => ['nullable', 'string', 'max:20'],
            'deadline_at' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'extension' => ['nullable', 'string', 'max:20'],
            'nombre_cliente' => ['nullable', 'string', 'max:255'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'correo_electronico' => ['nullable', 'email', 'max:255'],
            'numero_telefonico' => ['nullable', 'string', 'max:50'],
            'area' => ['nullable', 'string', 'max:255'],
            'puesto_trabajo' => ['nullable', 'string', 'max:255'],
        ]);

        $deadlineAt = !empty($data['deadline_at']) ? $data['deadline_at'] . ' 00:00:00' : $reminder->deadline_at;

        if ($request->boolean('all_day') && ! empty($data['start_at'])) {
            $d = Carbon::parse($data['start_at']);
            $t = trim((string) config('crm.reminder_all_day_notify_time', '09:00'));
            $data['start_at'] = strlen($t) === 5
                ? $d->format('Y-m-d').' '.$t.':00'
                : $d->format('Y-m-d').' '.$t;
        }

        $oldStartStr = $reminder->start_at?->format('Y-m-d H:i:s');
        if (array_key_exists('start_at', $data)) {
            $newStartStr = ! empty($data['start_at'])
                ? Carbon::parse($data['start_at'])->format('Y-m-d H:i:s')
                : null;
        } else {
            $newStartStr = $oldStartStr;
        }

        $newAllDay = $request->boolean('all_day', $reminder->all_day);
        $resetNotifyState = ($oldStartStr !== $newStartStr)
            || ((bool) $reminder->all_day !== (bool) $newAllDay);

        $payload = [
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
            'start_at' => $data['start_at'] ?? $reminder->start_at,
            'end_at' => $data['end_at'] ?? $reminder->end_at,
            'all_day' => $newAllDay,
            'repeat' => $data['repeat'] ?? $reminder->repeat,
            'deadline_at' => $deadlineAt,
            'scheduled_for' => $data['start_at'] ?? $reminder->scheduled_for,
        ];

        if ($resetNotifyState) {
            $payload['notification_sent_at'] = null;
            $payload['pre_notification_sent_at'] = null;
            $payload['last_recurring_notify_at'] = null;
        }

        $reminder->update($payload);

        return back()->with('success', 'Recordatorio actualizado.');
    }

    public function toggle(Request $request, Reminder $reminder)
    {
        $this->authorizeReminder($request, $reminder);

        $reminder->update([
            'is_done' => ! $reminder->is_done,
        ]);

        return back();
    }

    public function destroy(Request $request, Reminder $reminder)
    {
        $this->authorizeReminder($request, $reminder);

        $reminder->delete();

        return back()->with('success', 'Recordatorio eliminado.');
    }

    /**
     * Aplaza el inicio del recordatorio 5 minutos y reinicia el estado de avisos.
     */
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
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'new_start' => $newStart->toIso8601String(),
            ]);
        }

        return back()->with('success', 'Recordatorio aplazado 5 minutos.');
    }

    protected function authorizeReminder(Request $request, Reminder $reminder): void
    {
        abort_unless($reminder->user_id === $request->user()->id, 403);
    }
}

