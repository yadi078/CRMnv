<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Reminder;
use App\Notifications\ReminderDueNotification;
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
     */
    public function unreadCount(Request $request)
    {
        try {
            $count = auth()->user()->unreadNotifications()->count();
            $display = $count > 99 ? '99+' : (string) $count;

            return response()->json([
                'unread_count' => $count,
                'display' => $display,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['unread_count' => 0, 'display' => '0'], 200);
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Al abrir Notificaciones: crear notificaciones para recordatorios que vencen en ≤10 min o ya vencieron (por si el scheduler no está activo)
        $now = now();
        $limit = $now->copy()->addMinutes(10);
        $dueReminders = Reminder::where('user_id', $user->id)
            ->whereNull('notification_sent_at')
            ->where('is_done', false)
            ->where(function ($q) use ($limit) {
                $q->where(function ($q2) use ($limit) {
                    $q2->whereNotNull('start_at')->where('start_at', '<=', $limit);
                })->orWhere(function ($q2) use ($limit) {
                    $q2->whereNull('start_at')->whereNotNull('scheduled_for')->where('scheduled_for', '<=', $limit);
                });
            })
            ->get();
        foreach ($dueReminders as $reminder) {
            $user->notify(new ReminderDueNotification($reminder));
            $reminder->update(['notification_sent_at' => $now]);
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
        $sort = $request->get('orden', 'fecha');
        if ($sort === 'alfabetico') {
            $driver = DB::connection()->getDriverName();
            $tituloCol = $driver === 'sqlite'
                ? "json_extract(data, '$.titulo')"
                : "JSON_UNQUOTE(JSON_EXTRACT(data, '$.titulo'))";
            $query->orderByRaw("{$tituloCol} ASC");
        } else {
            $query->orderByDesc('created_at');
        }

        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();
        $starredCount = $user->notifications()->where('starred', true)->count();

        // Recordatorios personales
        $reminders = Reminder::where('user_id', $user->id)
            ->orderByRaw('CASE WHEN COALESCE(start_at, scheduled_for) IS NULL THEN 1 ELSE 0 END')
            ->orderBy(DB::raw('COALESCE(start_at, scheduled_for)'))
            ->get();

        // Contactos para el selector del modal de recordatorio (nombre del cliente)
        $contactsQuery = Contact::with('company')->orderBy('nombre_completo');
        if (! $user->esAdmin()) {
            $contactsQuery->where('created_by', $user->id);
        }
        $contactsForReminder = $contactsQuery->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'nombre_completo' => $c->nombre_completo,
                'email' => $c->email ?? '',
                'telefono' => $c->telefono ?? $c->celular ?? '',
                'extension' => $c->extension ?? '',
                'departamento' => $c->departamento ?? '',
                'puesto_de_trabajo' => $c->puesto_de_trabajo ?? '',
                'empresa' => $c->company?->nombre_comercial ?? $c->nombre_comercial ?? '',
            ];
        })->values()->toArray();

        // IDs de notificaciones no leídas tipo recordatorio ya "vistas" (no disparar alarma al cargar).
        // Excluimos las creadas en los últimos 15 segundos para que las recién creadas sí disparen alarma al hacer el primer poll.
        $cutoff = $now->copy()->subSeconds(15);
        $reminderAlertIds = $user->unreadNotifications()
            ->where('created_at', '<', $cutoff)
            ->get()
            ->filter(function ($n) {
                $d = is_array($n->data) ? $n->data : [];
                return ($d['tipo'] ?? '') === 'recordatorio';
            })
            ->pluck('id')
            ->values()
            ->toArray();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'unread_count' => $unreadCount,
                'starred_count' => $starredCount,
                'total' => $notifications->total(),
            ]);
        }

        return view('notifications.index', compact('notifications', 'unreadCount', 'starredCount', 'filter', 'sort', 'reminders', 'reminderAlertIds', 'contactsForReminder'));
    }

    /**
     * Para polling: devuelve notificaciones no leídas de tipo recordatorio (para alarma y notificación del navegador).
     */
    public function reminderAlerts(Request $request)
    {
        $user = auth()->user();
        $all = $user->unreadNotifications()->orderByDesc('created_at')->limit(50)->get();
        $items = $all->filter(function ($n) {
            $d = is_array($n->data) ? $n->data : [];
            return ($d['tipo'] ?? '') === 'recordatorio';
        })->take(20)->map(function ($n) {
            $d = is_array($n->data) ? $n->data : [];
            return [
                'id' => $n->id,
                'titulo' => $d['titulo'] ?? 'Recordatorio',
                'mensaje' => $d['mensaje'] ?? '',
                'fecha_prevista' => $d['fecha_prevista'] ?? null,
            ];
        })->values();

        return response()->json(['items' => $items]);
    }

    /**
     * Ver detalle de una notificación (para modal o panel).
     */
    public function show(Request $request, string $notification)
    {
        $user = auth()->user();
        $n = $user->notifications()->findOrFail($notification);

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
            $unreadCount = $user->unreadNotifications()->count();

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
