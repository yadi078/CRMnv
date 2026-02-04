<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controlador de Aprobaciones
 * 
 * Gestiona la aprobación de empresas y usuarios
 * Solo accesible para administradores
 */
class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Lista empresas pendientes de aprobación
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
     * Lista usuarios pendientes de aprobación
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

        return back()->with('success', 'Usuario aprobado exitosamente.');
    }
}
