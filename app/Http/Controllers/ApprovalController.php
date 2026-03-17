<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use App\Notifications\UserApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Controlador de Aprobaciones (Solicitudes pendientes)
 *
 * Centro único: usuarios y empresas que requieren autorización del admin.
 * Acciones: Aprobar y Denegar.
 */
class ApprovalController extends Controller
{
    /**
     * Centro único de solicitudes pendientes (pestañas Empresas | Usuarios)
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'empresas');

        $companies = collect();
        $contacts = collect();
        $users = collect();
        $companiesCount = 0;
        $contactsCount = 0;
        $usersCount = 0;

        if ($request->user()->can('companies.approve')) {
            $companiesCount = Company::pendientes()->count();
            if ($tab === 'empresas') {
                $companies = Company::pendientes()
                    ->with(['creator'])
                    ->latest()
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

        // Contactos pendientes (solo admins)
        if ($request->user()->esAdmin()) {
            $contactsCount = Contact::pendientes()->count();
            if ($tab === 'contactos') {
                $contacts = Contact::pendientes()
                    ->with(['company', 'creator'])
                    ->latest()
                    ->paginate(10, ['*'], 'contacts_page');
            }
        }

        $totalPendientes = $companiesCount + $usersCount + $contactsCount;

        return view('approvals.index', compact(
            'tab',
            'companies',
            'contacts',
            'users',
            'companiesCount',
            'contactsCount',
            'usersCount',
            'totalPendientes'
        ));
    }

    /**
     * Aprueba un contacto
     */
    public function approveContact(Contact $contact)
    {
        abort_unless(auth()->user()->esAdmin(), 403);

        $contact->aprobar(auth()->id());

        return back()->with('success', 'Contacto aprobado exitosamente.');
    }

    /**
     * Denega un contacto
     */
    public function denyContact(Request $request, Contact $contact)
    {
        abort_unless(auth()->user()->esAdmin(), 403);

        $contact->denegar(auth()->id(), $request->input('motivo'));

        return back()->with('success', 'Solicitud de contacto denegada.');
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
