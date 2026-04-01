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

        $followUpScope = function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)->orWhere('asignado_a', $user->id);
            });
        };

        // Seguimientos pendientes (no completados), solo los del usuario (creados o asignados)
        $seguimientosPendientes = FollowUp::query()
            ->where($followUpScope)
            ->where('completado', false)
            ->count();

        // Alarmas programadas (hoy)
        $hoyInicio = now()->startOfDay();
        $hoyFin = now()->endOfDay();
        $alarmasProgramadas = FollowUp::query()
            ->where($followUpScope)
            ->where('completado', false)
            ->whereBetween('fecha_alarma', [$hoyInicio, $hoyFin])
            ->count();
        $alarmasHoy = FollowUp::query()
            ->where($followUpScope)
            ->where('completado', false)
            ->whereBetween('fecha_alarma', [$hoyInicio, $hoyFin])
            ->orderBy('fecha_alarma')
            ->limit(10)
            ->get();

        $totalEmpresas = Company::query()->accessibleForExecutive($user)->count();
        $totalContactos = Contact::query()->accessibleForExecutive($user)->count();
        $totalSeguimientos = FollowUp::query()->where($followUpScope)->count();

        $contactosProspectoBase = Contact::query()
            ->accessibleForExecutive($user)
            ->where('approval_status', 'aprobado');
        $contactosSeguimiento = (clone $contactosProspectoBase)->porStatus('seguimiento')->count();
        $contactosInteresado = (clone $contactosProspectoBase)->porStatus('interesado')->count();
        $contactosSiLeInteresa = (clone $contactosProspectoBase)->porStatus('si_le_interesa_nos_llaman_o_no_compro')->count();
        $contactosVendido = (clone $contactosProspectoBase)->porStatus('vendido')->count();
        $contactosNoEstaba = (clone $contactosProspectoBase)->porStatus('no_estaba')->count();

        $seguimientosVencidos = FollowUp::query()
            ->where($followUpScope)
            ->where('completado', false)
            ->where('fecha_alarma', '<', now())
            ->count();

        // Solicitudes: altas de empresa pendientes + eliminaciones enviadas a revisión
        $solicitudesPendientes = Company::where('created_by', $user->id)
            ->where(function ($q) {
                $q->where('approval_status', 'pendiente')
                    ->orWhere('deletion_pending', true);
            })
            ->count();

        // Mis empresas: mismo alcance que en el listado
        $misEmpresasQuery = Company::query()
            ->accessibleForExecutive($user)
            ->with(['contacts' => function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where('assigned_user_id', $user->id)
                        ->orWhere('created_by', $user->id);
                })
                    ->orderByRaw('CASE WHEN created_by = ? THEN 0 ELSE 1 END', [$user->id])
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

        // Mis contactos: cartera del ejecutivo (asignados o propios)
        $misContactos = Contact::query()->accessibleForExecutive($user)->latest()->limit(20)->get();

        // Ventas recientes del usuario
        $ventasRecientes = Sale::where('created_by', $user->id)->with('company')->latest('fecha_venta')->limit(5)->get();

        return view('user.dashboard', compact(
            'seguimientosPendientes',
            'alarmasProgramadas',
            'solicitudesPendientes',
            'misEmpresas',
            'misContactos',
            'alarmasHoy',
            'ventasRecientes',
            'totalEmpresas',
            'totalContactos',
            'totalSeguimientos',
            'contactosSeguimiento',
            'contactosInteresado',
            'contactosSiLeInteresa',
            'contactosVendido',
            'contactosNoEstaba',
            'seguimientosVencidos'
        ));
    }
}
