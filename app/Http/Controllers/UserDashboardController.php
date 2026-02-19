<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\FollowUp;
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

        // Solicitudes pendientes (empresas del usuario en estado pendiente de aprobación)
        $solicitudesPendientes = Company::where('created_by', $user->id)->pendientes()->count();

        // Mis empresas: las creadas por el usuario (aprobadas + pendientes), con primer contacto
        $misEmpresasQuery = Company::where('created_by', $user->id)
            ->with(['contacts' => fn ($q) => $q->orderBy('id')->limit(1)]);
        if (request()->filled('q_empresas')) {
            $q = request('q_empresas');
            $misEmpresasQuery->where('nombre_comercial', 'like', "%{$q}%");
        }
        $misEmpresas = $misEmpresasQuery->latest()->limit(20)->get();

        // Mis contactos: los creados por el usuario
        $misContactos = Contact::where('created_by', $user->id)->latest()->limit(20)->get();

        return view('user.dashboard', compact(
            'seguimientosPendientes',
            'alarmasProgramadas',
            'solicitudesPendientes',
            'misEmpresas',
            'misContactos',
            'alarmasHoy'
        ));
    }
}
