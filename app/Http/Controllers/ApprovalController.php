<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controlador de Aprobaciones (Solicitudes pendientes)
 *
 * Centro único: usuarios y empresas que requieren autorización del admin.
 * Acciones: Aprobar y Denegar.
 */
class ApprovalController extends Controller
{
    /**
     * Centro único de solicitudes pendientes (pestañas Usuarios | Empresas)
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'empresas');

        $companiesCount = 0;
        $usersCount = 0;
        if ($request->user()->can('companies.approve')) {
            $companiesCount = Company::pendientes()->count();
        }
        if ($request->user()->can('users.approve')) {
            $usersCount = User::where('approval_status', 'pendiente')->count();
        }

        $companies = collect();
        $users = collect();
        if ($tab === 'empresas' && $request->user()->can('companies.approve')) {
            $companies = Company::pendientes()
                ->with(['creator'])
                ->latest()
                ->paginate(10, ['*'], 'companies_page');
        }
        if ($tab === 'usuarios' && $request->user()->can('users.approve')) {
            $users = User::where('approval_status', 'pendiente')
                ->with('roles')
                ->latest()
                ->paginate(10, ['*'], 'users_page');
        }

        return view('approvals.index', compact('tab', 'companies', 'users', 'companiesCount', 'usersCount'));
    }

    /**
     * Lista empresas pendientes de aprobación (vista legacy)
     */
    public function companies()
    {
        $this->authorize('companies.approve');

        $companies = Company::pendientes()
            ->with(['creator'])
            ->latest()
            ->paginate(15);

        return view('approvals.companies', compact('companies'));
    }

    /**
     * Aprueba una empresa
     */
    public function approveCompany(Company $company)
    {
        $this->authorize('companies.approve');

        $company->aprobar(auth()->id());

        return back()->with('success', 'Empresa aprobada exitosamente.');
    }

    /**
     * Denega una empresa
     */
    public function denyCompany(Request $request, Company $company)
    {
        $this->authorize('companies.approve');

        $company->denegar(auth()->id(), $request->input('motivo'));

        return back()->with('success', 'Solicitud de empresa denegada.');
    }

    /**
     * Lista usuarios pendientes de aprobación (vista legacy)
     */
    public function users()
    {
        $this->authorize('users.approve');

        $users = User::where('approval_status', 'pendiente')
            ->latest()
            ->paginate(15);

        return view('approvals.users', compact('users'));
    }

    /**
     * Aprueba un usuario
     */
    public function approveUser(User $user)
    {
        $this->authorize('users.approve');

        $user->aprobar(auth()->id());

        return back()->with('success', 'Usuario aprobado. Ya puede iniciar sesión.');
    }

    /**
     * Denega la solicitud de registro de un usuario
     */
    public function denyUser(Request $request, User $user)
    {
        $this->authorize('users.approve');

        $user->denegar(auth()->id(), $request->input('motivo'));

        return back()->with('success', 'Solicitud de registro denegada.');
    }
}
