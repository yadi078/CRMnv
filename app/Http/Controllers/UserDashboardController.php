<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Panel para usuarios con rol "usuario".
 * Según el informe: Tablero con resumen de actividad, estatus semáforo y acceso a módulos.
 */
class UserDashboardController extends Controller
{
    /**
     * Vista principal del usuario normal.
     * Resumen de actividad (seguimientos pendientes, alarmas del día), estatus visual (semáforo).
     */
    public function index(): View|RedirectResponse
    {
        if (auth()->user()->esAdmin()) {
            return redirect()->route('dashboard');
        }

        $user = auth()->user();

        // Seguimientos pendientes (no completados)
        $seguimientosPendientes = FollowUp::where('completado', false)->count();

        // Alarmas programadas (hoy)
        $hoyInicio = now()->startOfDay();
        $hoyFin = now()->endOfDay();
        $alarmasProgramadas = FollowUp::where('completado', false)
            ->whereBetween('fecha_alarma', [$hoyInicio, $hoyFin])
            ->count();
        $alarmasHoy = FollowUp::where('completado', false)
            ->whereBetween('fecha_alarma', [$hoyInicio, $hoyFin])
            ->orderBy('fecha_alarma')
            ->limit(10)
            ->get();

        // Solicitudes: altas de empresa pendientes + eliminaciones enviadas a revisión
        $solicitudesPendientes = Company::where('created_by', $user->id)
            ->where(function ($q) {
                $q->where('approval_status', 'pendiente')
                    ->orWhere('deletion_pending', true);
            })
            ->count();

        // Mis empresas: mismo alcance que en el listado (propias + aprobadas con contacto propio)
        $misEmpresasQuery = Company::query()
            ->accessibleForExecutive($user)
            ->with(['contacts' => function ($q) use ($user) {
                $q->orderByRaw('CASE WHEN created_by = ? THEN 0 ELSE 1 END', [$user->id])
                    ->orderBy('id')
                    ->limit(1);
            }]);
        $busqueda = trim((string) request('q_empresas', ''));
        if ($busqueda !== '') {
            $misEmpresasQuery->where(function ($query) use ($busqueda) {
                $query->where('nombre_comercial', 'like', '%'.$busqueda.'%')
                    ->orWhere('rfc', 'like', '%'.$busqueda.'%');
            });
        }
        $misEmpresas = $misEmpresasQuery->latest()->limit(20)->get();

        // Mis contactos: los creados por el usuario
        $misContactos = Contact::where('created_by', $user->id)->latest()->limit(20)->get();

        // Ventas recientes del usuario
        $ventasRecientes = Sale::where('created_by', $user->id)->with('company')->latest('fecha_venta')->limit(5)->get();

        return view('user.dashboard', compact(
            'seguimientosPendientes',
            'alarmasProgramadas',
            'solicitudesPendientes',
            'misEmpresas',
            'misContactos',
            'alarmasHoy',
            'ventasRecientes'
        ));
    }
}
