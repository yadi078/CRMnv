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

        // Empresas por color de semáforo
        $empresasVerde = Company::porColor('verde')->count();
        $empresasAmarillo = Company::porColor('amarillo')->count();
        $empresasRojo = Company::porColor('rojo')->count();

        // Empresas pendientes de aprobación (solo para admin)
        $empresasPendientes = 0;
        if ($user->can('companies.approve')) {
            $empresasPendientes = Company::pendientes()->count();
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
            'empresasVerde',
            'empresasAmarillo',
            'empresasRojo',
            'empresasPendientes',
            'seguimientosVencidos',
            'ultimasEmpresas',
            'proximosSeguimientos'
        ));
    }
}
