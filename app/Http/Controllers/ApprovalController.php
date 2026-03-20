<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Notifications\UserApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Controlador de Aprobaciones (Solicitudes pendientes)
 *
 * Centro único: altas de empresas, bajas solicitadas por usuarios, y registro de usuarios.
 */
class ApprovalController extends Controller
{
    /**
     * Centro único de solicitudes pendientes (pestañas Empresas | Usuarios)
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'empresas');
        if (! in_array($tab, ['empresas', 'usuarios'], true)) {
            $tab = 'empresas';
        }

        $companies = collect();
        $users = collect();
        $companiesCount = 0;
        $usersCount = 0;

        if ($request->user()->can('companies.approve')) {
            $companiesCount = Company::pendientesAprobacion()->count();
            if ($tab === 'empresas') {
                $companies = Company::pendientesAprobacion()
                    ->with(['creator', 'deletionRequester'])
                    ->latest('updated_at')
                    ->paginate(10, ['*'], 'companies_page');
            }
        }
        if ($request->user()->can('users.approve')) {
            $usersCount = User::where('approval_status', 'pendiente')->count();
            if ($tab === 'usuarios') {
                $users = User::where('approval_status', 'pendiente')
                    ->with('roles')
                    ->latest()
                    ->paginate(10, ['*'], 'users_page');
            }
        }

        $totalPendientes = $companiesCount + $usersCount;

        return view('approvals.index', compact(
            'tab',
            'companies',
            'users',
            'companiesCount',
            'usersCount',
            'totalPendientes'
        ));
    }

    /**
     * Aprueba el alta de una empresa (no confundir con aprobar una eliminación).
     */
    public function approveCompany(Company $company)
    {
        $this->authorize('companies.approve');

        if ($company->deletion_pending) {
            return back()->with('error', 'Esta empresa tiene una solicitud de eliminación. Use «Aprobar eliminación» o «Denegar eliminación».');
        }
        if ($company->approval_status !== 'pendiente') {
            return back()->with('error', 'Esta empresa no está pendiente de alta.');
        }

        $company->aprobar(auth()->id());

        return back()->with('success', 'Empresa aprobada exitosamente.');
    }

    /**
     * Denega el alta de una empresa.
     */
    public function denyCompany(Request $request, Company $company)
    {
        $this->authorize('companies.approve');

        if ($company->deletion_pending) {
            return back()->with('error', 'Esta empresa tiene una solicitud de eliminación. Use las acciones de eliminación en su lugar.');
        }
        if ($company->approval_status !== 'pendiente') {
            return back()->with('error', 'Solo se pueden denegar empresas pendientes de alta.');
        }

        $company->denegar(auth()->id(), $request->input('motivo'));

        return back()->with('success', 'Solicitud de empresa denegada.');
    }

    /**
     * Aprueba la eliminación solicitada por un usuario (borrado lógico).
     */
    public function approveCompanyDeletion(Company $company)
    {
        $this->authorize('companies.approve');

        if (! $company->deletion_pending) {
            return back()->with('error', 'No hay solicitud de eliminación pendiente para esta empresa.');
        }

        $company->update([
            'deletion_pending' => false,
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
        ]);
        $company->delete();

        return back()->with('success', 'Eliminación aprobada. La empresa se ha dado de baja.');
    }

    /**
     * Rechaza la solicitud de eliminación; la empresa permanece activa.
     */
    public function denyCompanyDeletion(Company $company)
    {
        $this->authorize('companies.approve');

        if (! $company->deletion_pending) {
            return back()->with('error', 'No hay solicitud de eliminación pendiente para esta empresa.');
        }

        $company->update([
            'deletion_pending' => false,
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
        ]);

        return back()->with('success', 'Solicitud de eliminación rechazada. La empresa sigue activa.');
    }

    /**
     * Lista empresas pendientes de aprobación (vista legacy)
     */
    public function companies()
    {
        $this->authorize('companies.approve');

        $companies = Company::pendientesAprobacion()
            ->with(['creator', 'deletionRequester'])
            ->latest('updated_at')
            ->paginate(15);

        return view('approvals.companies', compact('companies'));
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
     * Aprueba un usuario y le envía un enlace para entrar automáticamente a su panel.
     */
    public function approveUser(User $user)
    {
        $this->authorize('users.approve');

        $user->aprobar(auth()->id());

        $entrarUrl = URL::temporarySignedRoute(
            'auth.auto-login',
            now()->addDays(2),
            ['user' => $user->id]
        );
        $user->notify(new UserApprovedNotification($entrarUrl));

        return back()->with('success', 'Usuario aprobado. Se le ha enviado un enlace para entrar a su panel.');
    }

    /**
     * Denega la solicitud de registro de un usuario.
     * Se elimina el usuario para que deba registrarse nuevamente si lo desea.
     */
    public function denyUser(Request $request, User $user)
    {
        $this->authorize('users.approve');

        if ($user->approval_status !== 'pendiente') {
            return back()->with('error', 'Solo se pueden denegar usuarios pendientes de aprobación.');
        }

        $user->delete();

        return back()->with('success', 'Registro denegado. El usuario ha sido eliminado y deberá registrarse nuevamente si desea intentarlo.');
    }
}
