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
 * y sistema visual de semáforo para prospectos
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

        // Empresas por estado de prospecto
        $empresasSeguimiento = Company::porStatus('seguimiento')->count();
        $empresasInteresado = Company::porStatus('interesado')->count();
        $empresasSiLeInteresa = Company::porStatus('si_le_interesa_nos_llaman_o_no_compro')->count();
        $empresasVendido = Company::porStatus('vendido')->count();
        $empresasNoEstaba = Company::porStatus('no_estaba')->count();

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
            'empresasSeguimiento',
            'empresasInteresado',
            'empresasSiLeInteresa',
            'empresasVendido',
            'empresasNoEstaba',
            'empresasPendientes',
            'usuariosPendientes',
            'seguimientosVencidos',
            'ultimasEmpresas',
            'proximosSeguimientos'
        ));
    }
}
