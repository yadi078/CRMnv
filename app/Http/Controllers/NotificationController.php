<?php

namespace App\Http\Controllers;

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
        return back()->with('success', 'Notificación destacada.');
    }

    public function unstar(Request $request, string $notification)
    {
        $user = auth()->user();
        $n = $user->notifications()->findOrFail($notification);
        DB::table('notifications')->where('id', $n->id)->update(['starred' => false]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'starred' => false]);
        }
        return back()->with('success', 'Quitada de destacadas.');
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
