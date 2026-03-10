<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\FollowUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador del Dashboard
 *
 * Muestra resumen de actividad, seguimientos pendientes
 * y semáforo de prospectos por contactos (estado de cada contacto)
 */
class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Estadísticas generales
        $totalEmpresas = Company::count();
        $totalContactos = Contact::count();
        $totalSeguimientos = FollowUp::count();
        $seguimientosPendientes = FollowUp::pendientes()->count();

        // Contactos por estado de prospecto (semáforo)
        $contactosSeguimiento = Contact::porStatus('seguimiento')->count();
        $contactosInteresado = Contact::porStatus('interesado')->count();
        $contactosSiLeInteresa = Contact::porStatus('si_le_interesa_nos_llaman_o_no_compro')->count();
        $contactosVendido = Contact::porStatus('vendido')->count();
        $contactosNoEstaba = Contact::porStatus('no_estaba')->count();

        // Solicitudes pendientes: empresas y usuarios (solo para admin)
        $empresasPendientes = 0;
        $usuariosPendientes = 0;
        if ($user->can('companies.approve')) {
            $empresasPendientes = Company::pendientes()->count();
        }
        if ($user->can('users.approve')) {
            $usuariosPendientes = \App\Models\User::where('approval_status', 'pendiente')->count();
        }

        // Seguimientos vencidos
        $seguimientosVencidos = FollowUp::where('completado', false)
            ->where('fecha_alarma', '<', now())
            ->count();

        // Últimas empresas creadas (solo aprobadas para usuarios sin permiso de aprobación)
        $ultimasEmpresasQuery = Company::with('creator')->latest()->limit(5);
        if (!$user->can('companies.approve')) {
            $ultimasEmpresasQuery->aprobados();
        }
        $ultimasEmpresas = $ultimasEmpresasQuery->get();

        // Próximos seguimientos
        $proximosSeguimientos = FollowUp::with(['company', 'contact', 'asignado'])
            ->where('completado', false)
            ->where('fecha_alarma', '>=', now())
            ->orderBy('fecha_alarma', 'asc')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalEmpresas',
            'totalContactos',
            'totalSeguimientos',
            'seguimientosPendientes',
            'contactosSeguimiento',
            'contactosInteresado',
            'contactosSiLeInteresa',
            'contactosVendido',
            'contactosNoEstaba',
            'empresasPendientes',
            'usuariosPendientes',
            'seguimientosVencidos',
            'ultimasEmpresas',
            'proximosSeguimientos'
        ));
    }
}
