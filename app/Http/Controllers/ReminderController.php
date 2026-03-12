<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
            'time' => ['nullable', 'string'],
            'all_day' => ['sometimes', 'boolean'],
            'repeat' => ['nullable', 'string', 'max:20'],
            'deadline_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $startAt = null;
        if (!empty($data['date'])) {
            $time = !empty($data['time']) ? $data['time'] : '00:00';
            $startAt = $data['date'] . ' ' . $time . ':00';
        }

        $deadlineAt = null;
        if (!empty($data['deadline_date'])) {
            $deadlineAt = $data['deadline_date'] . ' 00:00:00';
        }

        $request->user()->reminders()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_at' => $startAt,
            'end_at' => null,
            'all_day' => $request->boolean('all_day'),
            'repeat' => $data['repeat'] ?? null,
            'deadline_at' => $deadlineAt,
            // Mantener compatibilidad con scheduled_for usando la hora de inicio
            'scheduled_for' => $startAt,
        ]);

        return back()->with('success', 'Recordatorio agregado.');
    }

    public function update(Request $request, Reminder $reminder)
    {
        $this->authorizeReminder($request, $reminder);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'all_day' => ['sometimes', 'boolean'],
            'repeat' => ['nullable', 'string', 'max:20'],
            'deadline_at' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $deadlineAt = !empty($data['deadline_at']) ? $data['deadline_at'] . ' 00:00:00' : $reminder->deadline_at;

        $reminder->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? $reminder->description,
            'start_at' => $data['start_at'] ?? $reminder->start_at,
            'end_at' => $data['end_at'] ?? $reminder->end_at,
            'all_day' => $request->boolean('all_day', $reminder->all_day),
            'repeat' => $data['repeat'] ?? $reminder->repeat,
            'deadline_at' => $deadlineAt,
            'scheduled_for' => $data['start_at'] ?? $reminder->scheduled_for,
        ]);

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

    protected function authorizeReminder(Request $request, Reminder $reminder): void
    {
        abort_unless($reminder->user_id === $request->user()->id, 403);
    }
}

