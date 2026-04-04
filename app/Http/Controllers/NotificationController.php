<?php

namespace App\Http\Controllers;

use App\Notifications\ReminderDueNotification;
use App\Services\ContactBirthdayNotifier;
use App\Services\ReminderDueNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Notificaciones (estilo Gmail).
 * Filtros: todas, leídas, no leídas, destacadas, sin destacar.
 * Acciones: marcar leída, destacar, eliminar, ordenar.
 */
class NotificationController extends Controller
{
    /**
     * Solo para polling: devuelve el número de no leídas (máx. 99+ en display).
     * Incluye reminder_id en cada alerta para Aplazar / Reprogramar en el modal.
     */
    public function unreadCount(Request $request)
    {
        try {
            $user = auth()->user();
            // Respaldo web: si no corre cron/scheduler, generar aquí los recordatorios debidos
            // para el usuario autenticado (15/10/5 min y hora).
            app(ReminderDueNotifier::class)->dispatchDue($user->id);
            $count = $user->unreadNotificationsCount();
            $display = $count > 99 ? '99+' : (string) $count;

            $dueReminderAlerts = $user->unreadNotifications()
                ->where('type', ReminderDueNotification::class)
                ->latest()
                ->limit(8)
                ->get()
                ->map(function ($notification) use ($user) {
                    $raw = $notification->data;
                    $data = is_array($raw) ? $raw : (is_string($raw) ? (json_decode($raw, true) ?: []) : []);
                    $data = ReminderDueNotification::enrichStoredData($data, $user->id);

                    $phase = (string) ($data['alert_phase'] ?? 'due');
                    if (! in_array($phase, ['pre15', 'pre10', 'pre5', 'pre2', 'due', 'post3'], true)) {
                        return null;
                    }

                    $detail = isset($data['reminder_detalle']) && is_array($data['reminder_detalle'])
                        ? $data['reminder_detalle']
                        : [];
                    $title = trim((string) ($detail['titulo'] ?? $data['titulo'] ?? 'Recordatorio'));
                    $fechaInicio = trim((string) ($detail['fecha_inicio'] ?? $data['fecha_prevista'] ?? ''));
                    $description = trim((string) ($detail['descripcion'] ?? $data['mensaje'] ?? ''));

                    $reminderId = isset($data['reminder_id']) ? (int) $data['reminder_id'] : null;

                    return [
                        'id' => (string) $notification->id,
                        'reminder_id' => $reminderId,
                        'title' => $title !== '' ? $title : 'Recordatorio',
                        'time' => $fechaInicio,
                        'description' => $description,
                        'detail' => [
                            'nombre_cliente' => trim((string) ($detail['nombre_cliente'] ?? '')),
                            'empresa' => trim((string) ($detail['empresa'] ?? '')),
                            'correo_electronico' => trim((string) ($detail['correo_electronico'] ?? '')),
                            'numero_telefonico' => trim((string) ($detail['numero_telefonico'] ?? '')),
                            'extension' => trim((string) ($detail['extension'] ?? '')),
                            'area' => trim((string) ($detail['area'] ?? '')),
                            'puesto_trabajo' => trim((string) ($detail['puesto_trabajo'] ?? '')),
                            'fecha_inicio' => $fechaInicio,
                            'fecha_limite' => trim((string) ($detail['fecha_limite'] ?? '')),
                            'repeticion' => trim((string) ($detail['repeticion'] ?? '')),
                            'tipo_accion' => trim((string) ($detail['tipo_accion'] ?? '')),
                            'reminder_id' => $reminderId,
                        ],
                    ];
                })
                ->filter()
                ->values();

            return response()->json([
                'unread_count' => $count,
                'display' => $display,
                'due_reminder_alerts' => $dueReminderAlerts,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['unread_count' => 0, 'display' => '0', 'due_reminder_alerts' => []], 200);
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Al abrir Notificaciones: mismo criterio que el scheduler (por si no corre el cron)
        app(ReminderDueNotifier::class)->dispatchDue($user->id);

        // Cumpleaños: respaldo web si el servidor no ejecuta `schedule:run`.
        // Se puede ejecutar en cada carga; el servicio evita duplicados por admin/contacto/día.
        if ($user->esAdmin()) {
            app(ContactBirthdayNotifier::class)->notifyAdminsForToday();
        }

        $query = $user->notifications();

        // Filtro
        $filter = $request->get('filtro', 'todas');
        if ($filter === 'no_leidas') {
            $query->whereNull('read_at');
        } elseif ($filter === 'leidas') {
            $query->whereNotNull('read_at');
        } elseif ($filter === 'destacadas') {
            $query->where('starred', true);
        } elseif ($filter === 'sin_destacar') {
            $query->where(function ($q) {
                $q->where('starred', false)->orWhereNull('starred');
            });
        }

        // Orden
        $sort = $request->get('orden', 'recientes');
        if ($sort === 'alfabetico') {
            $driver = DB::connection()->getDriverName();
            $tituloCol = $driver === 'sqlite'
                ? "json_extract(data, '$.titulo')"
                : "JSON_UNQUOTE(JSON_EXTRACT(data, '$.titulo'))";
            $query->orderByRaw("{$tituloCol} ASC");
        } elseif ($sort === 'antiguas') {
            $query->orderBy('created_at');
        } else {
            // Compatibilidad: "fecha" equivale a más recientes.
            $query->orderByDesc('created_at');
        }

        $notifications = $query->paginate(20)->withQueryString();

        $notifications->getCollection()->transform(function ($n) use ($user) {
            $raw = $n->data;
            $d = is_array($raw) ? $raw : (is_string($raw) ? (json_decode($raw, true) ?: []) : []);
            $n->data = ReminderDueNotification::enrichStoredData($d, $user->id);

            return $n;
        });

        $unreadCount = $user->unreadNotificationsCount();
        $starredCount = $user->notifications()->where('starred', true)->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'unread_count' => $unreadCount,
                'starred_count' => $starredCount,
                'total' => $notifications->total(),
            ]);
        }

        return view('notifications.index', compact('notifications', 'unreadCount', 'starredCount', 'filter', 'sort'));
    }

    /**
     * Ver detalle de una notificación (para modal o panel).
     */
    public function show(Request $request, string $notification)
    {
        $user = auth()->user();
        $n = $user->notifications()->findOrFail($notification);
        $raw = $n->data;
        $d = is_array($raw) ? $raw : (is_string($raw) ? (json_decode($raw, true) ?: []) : []);
        $n->data = ReminderDueNotification::enrichStoredData($d, $user->id);

        if ($request->wantsJson()) {
            return response()->json($n);
        }

        return view('notifications.show', ['notification' => $n]);
    }

    public function markAsRead(Request $request, string $notification)
    {
        try {
            $user = auth()->user();
            $n = $user->notifications()->findOrFail($notification);
            $n->markAsRead();
            $unreadCount = $user->unreadNotificationsCount();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'unread_count' => $unreadCount,
                    'display' => $unreadCount > 99 ? '99+' : (string) $unreadCount,
                ]);
            }
            return back()->with('success', 'Notificación marcada como leída.');
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al marcar como leída.'], 422);
            }
            return back()->with('error', 'Error al marcar como leída.');
        }
    }

    public function markAllAsRead(Request $request)
    {
        try {
            $user = auth()->user();
            $user->unreadNotifications->markAsRead();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'unread_count' => 0, 'display' => '0']);
            }
            return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al marcar todas.'], 422);
            }
            return back()->with('error', 'Error al marcar todas como leídas.');
        }
    }

    public function bulkMarkAsRead(Request $request)
    {
        $validated = $request->validate([
            'notification_ids' => ['required', 'array', 'min:1'],
            'notification_ids.*' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $ids = collect($validated['notification_ids'])->map(fn ($id) => (string) $id)->unique()->values();

        $toMark = $user->notifications()->whereIn('id', $ids)->get();
        $toMark->markAsRead();

        $unreadCount = $user->unreadNotificationsCount();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'affected' => $toMark->count(),
                'unread_count' => $unreadCount,
                'display' => $unreadCount > 99 ? '99+' : (string) $unreadCount,
            ]);
        }

        return back()->with('success', 'Notificaciones seleccionadas marcadas como leídas.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'notification_ids' => ['required', 'array', 'min:1'],
            'notification_ids.*' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $ids = collect($validated['notification_ids'])->map(fn ($id) => (string) $id)->unique()->values();

        $affected = $user->notifications()->whereIn('id', $ids)->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'affected' => $affected]);
        }

        return back()->with('success', 'Notificaciones seleccionadas eliminadas.');
    }

    public function star(Request $request, string $notification)
    {
        $user = auth()->user();
        $n = $user->notifications()->findOrFail($notification);
        DB::table('notifications')->where('id', $n->id)->update(['starred' => true]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'starred' => true]);
        }
        return back();
    }

    public function unstar(Request $request, string $notification)
    {
        $user = auth()->user();
        $n = $user->notifications()->findOrFail($notification);
        DB::table('notifications')->where('id', $n->id)->update(['starred' => false]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'starred' => false]);
        }
        return back();
    }

    public function destroy(Request $request, string $notification)
    {
        $user = auth()->user();
        $n = $user->notifications()->findOrFail($notification);
        $n->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Notificación eliminada.');
    }
}
