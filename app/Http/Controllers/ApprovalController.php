<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use App\Notifications\DeletionRequestResolvedNotification;
use App\Notifications\UserApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

/**
 * Controlador de Aprobaciones (Solicitudes pendientes)
 *
 * Centro único: altas de empresas, bajas solicitadas por usuarios, y registro de usuarios.
 */
class ApprovalController extends Controller
{
    /**
     * Centro único de solicitudes pendientes (pestañas Empresas | Contactos | Usuarios)
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'empresas');
        if (! in_array($tab, ['empresas', 'contactos', 'usuarios'], true)) {
            $tab = 'empresas';
        }

        $companies = collect();
        $contacts = collect();
        $users = collect();
        $companiesCount = 0;
        $contactsCount = 0;
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

        if ($request->user()->can('companies.approve')) {
            $contactsCount = Contact::pendientesAprobacion()->count();
            if ($tab === 'contactos') {
                $contacts = Contact::pendientesAprobacion()
                    ->with(['company', 'creator', 'deletionRequester'])
                    ->latest()
                    ->paginate(10, ['*'], 'contacts_page');
            }
        }

        $totalPendientes = $companiesCount + $contactsCount + $usersCount;

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

        $requesterId = $company->deletion_requested_by;
        $nombre = $company->nombre_comercial;

        $company->update([
            'deletion_pending' => false,
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
        ]);
        $company->delete();

        if ($requesterId) {
            $u = User::find($requesterId);
            if ($u) {
                try {
                    $u->notify(new DeletionRequestResolvedNotification('company', 'approved', $nombre, null));
                } catch (\Throwable $e) {
                    Log::warning('No se pudo notificar eliminación aprobada (empresa)', [
                        'user_id' => $requesterId,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        return back()->with('success', 'Eliminación aprobada. La empresa se ha dado de baja.');
    }

    /**
     * Rechaza la solicitud de eliminación; la empresa permanece activa.
     */
    public function denyCompanyDeletion(Request $request, Company $company)
    {
        $this->authorize('companies.approve');

        if (! $company->deletion_pending) {
            return back()->with('error', 'No hay solicitud de eliminación pendiente para esta empresa.');
        }

        $validated = $request->validate([
            'nota_admin' => 'required|string|max:2000',
        ], [
            'nota_admin.required' => 'Debe escribir el motivo por el que no se acepta la eliminación.',
        ]);

        $requesterId = $company->deletion_requested_by;
        $nombre = $company->nombre_comercial;
        $nota = $validated['nota_admin'];

        $company->update([
            'deletion_pending' => false,
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
            'deletion_resolution' => 'denied',
            'deletion_resolution_note' => $nota,
            'deletion_resolved_at' => now(),
            'deletion_resolved_by' => auth()->id(),
            'deletion_decision_user_id' => $requesterId,
        ]);

        if ($requesterId) {
            $u = User::find($requesterId);
            if ($u) {
                try {
                    $u->notify(new DeletionRequestResolvedNotification('company', 'denied', $nombre, $nota));
                } catch (\Throwable $e) {
                    Log::warning('No se pudo notificar eliminación denegada (empresa)', [
                        'user_id' => $requesterId,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        return back()->with('success', 'Solicitud de eliminación rechazada. El usuario verá el motivo en su ficha y en notificaciones.');
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

        try {
            $user->notify(new UserApprovedNotification($entrarUrl));
        } catch (\Throwable $e) {
            Log::error('Notificación de aprobación de usuario falló (correo o BD)', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('warning', 'Usuario aprobado. No se pudo enviar el correo automático; revise la configuración MAIL en .env o los logs. El usuario puede iniciar sesión con su contraseña o desde notificaciones en el panel si aplica.');
        }

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

    /**
     * Aprueba un contacto pendiente.
     */
    public function approveContact(Contact $contact)
    {
        $this->authorize('companies.approve');

        if ($contact->deletion_pending) {
            return back()->with('error', 'Este contacto tiene una solicitud de eliminación. Use «Aprobar eliminación» o «Denegar eliminación».');
        }
        if ($contact->approval_status !== 'pendiente') {
            return back()->with('error', 'Este contacto no está pendiente de aprobación.');
        }

        $contact->aprobar(auth()->id());

        return back()->with('success', 'Contacto aprobado exitosamente.');
    }

    /**
     * Deniega un contacto pendiente.
     */
    public function denyContact(Request $request, Contact $contact)
    {
        $this->authorize('companies.approve');

        if ($contact->deletion_pending) {
            return back()->with('error', 'Este contacto tiene una solicitud de eliminación. Use las acciones de eliminación en su lugar.');
        }
        if ($contact->approval_status !== 'pendiente') {
            return back()->with('error', 'Solo se pueden denegar contactos pendientes de aprobación.');
        }

        $contact->denegar(auth()->id(), $request->input('motivo'));

        return back()->with('success', 'Solicitud de contacto denegada.');
    }

    /**
     * Aprueba la eliminación solicitada por un usuario para un contacto.
     */
    public function approveContactDeletion(Contact $contact)
    {
        $this->authorize('companies.approve');

        if (! $contact->deletion_pending) {
            return back()->with('error', 'No hay solicitud de eliminación pendiente para este contacto.');
        }

        $requesterId = $contact->deletion_requested_by;
        $nombre = $contact->nombre_completo;

        $contact->update([
            'deletion_pending' => false,
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
            'deletion_reason' => null,
        ]);
        $contact->delete();

        if ($requesterId) {
            $u = User::find($requesterId);
            if ($u) {
                try {
                    $u->notify(new DeletionRequestResolvedNotification('contact', 'approved', $nombre, null));
                } catch (\Throwable $e) {
                    Log::warning('No se pudo notificar eliminación aprobada (contacto)', [
                        'user_id' => $requesterId,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        return back()->with('success', 'Eliminación aprobada. El contacto se ha dado de baja.');
    }

    /**
     * Rechaza la solicitud de eliminación y mantiene activo el contacto.
     */
    public function denyContactDeletion(Request $request, Contact $contact)
    {
        $this->authorize('companies.approve');

        if (! $contact->deletion_pending) {
            return back()->with('error', 'No hay solicitud de eliminación pendiente para este contacto.');
        }

        $validated = $request->validate([
            'nota_admin' => 'required|string|max:2000',
        ], [
            'nota_admin.required' => 'Debe escribir el motivo por el que no se acepta la eliminación.',
        ]);

        $requesterId = $contact->deletion_requested_by;
        $nombre = $contact->nombre_completo;
        $nota = $validated['nota_admin'];

        $contact->update([
            'deletion_pending' => false,
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
            // Conservar deletion_reason (motivo que envió el usuario) como referencia
            'deletion_resolution' => 'denied',
            'deletion_resolution_note' => $nota,
            'deletion_resolved_at' => now(),
            'deletion_resolved_by' => auth()->id(),
            'deletion_decision_user_id' => $requesterId,
        ]);

        if ($requesterId) {
            $u = User::find($requesterId);
            if ($u) {
                try {
                    $u->notify(new DeletionRequestResolvedNotification('contact', 'denied', $nombre, $nota));
                } catch (\Throwable $e) {
                    Log::warning('No se pudo notificar eliminación denegada (contacto)', [
                        'user_id' => $requesterId,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        return back()->with('success', 'Solicitud de eliminación rechazada. El usuario verá el motivo en su ficha y en notificaciones.');
    }
}
