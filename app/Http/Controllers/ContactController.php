<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesAdminUserView;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\NewContactAddedNotification;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Controlador de Contactos
 * 
 * Gestiona CRUD de contactos y generación de PDF
 */
class ContactController extends Controller
{
    use ResolvesAdminUserView;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Contact::class);

        $user = auth()->user();
        $isAdmin = $user->esAdmin();

        $query = Contact::with(['company', 'creator']);

        if (! $isAdmin) {
            $query->accessibleForExecutive($user);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre_completo', 'like', "%{$search}%");
        }

        if ($request->filled('status_color')) {
            $query->where('status_color', $request->status_color);
        }

        $query->latest();

        $contacts = $query->paginate(15)->withQueryString();

        $namesForDatalist = Contact::query();
        if (! $isAdmin) {
            $namesForDatalist->accessibleForExecutive($user);
        }
        $contactNames = $namesForDatalist
            ->orderBy('nombre_completo')
            ->pluck('nombre_completo')
            ->filter()
            ->unique()
            ->values();

        return $this->resolveView('contacts.index', 'user.contacts.index', compact(
            'contacts',
            'contactNames'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Contact::class);

        $companyId = $request->company_id;
        $companies = Company::forExecutiveContactForm($request->user());

        return $this->resolveView('contacts.create', 'user.contacts.create', compact('companies', 'companyId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            // Ejecutivo: pendiente. Administrador o quien tenga permiso de aprobar: alta directa sin cola.
            $approvalStatus = ($user->esAdmin() || $user->can('contacts.approve')) ? 'aprobado' : 'pendiente';

            $contact = Contact::create([
                'company_id' => $request->company_id,
                'nombre_completo' => $request->nombre_completo,
                'genero' => $request->genero,
                'puesto_de_trabajo' => $request->puesto_de_trabajo,
                'departamento' => $request->departamento,
                'celular' => $request->celular,
                'telefono' => $request->telefono,
                'extension' => $request->extension,
                'email' => $request->email,
                'email_activo' => $request->boolean('email_activo', true),
                'municipio' => $request->municipio,
                'estado' => $request->estado,
                'razon_social' => $request->razon_social,
                'nombre_comercial' => $request->nombre_comercial,
                'calle_numero' => $request->calle_numero,
                'colonia_cp' => $request->colonia_cp,
                'rfc' => $request->rfc,
                'regimen_fiscal' => $request->regimen_fiscal,
                'notas' => $request->notas,
                'status_color' => $request->input('status_color', 'seguimiento'),
                'approval_status' => $approvalStatus,
                'approved_by' => $approvalStatus === 'aprobado' ? $user->id : null,
                'approved_at' => $approvalStatus === 'aprobado' ? now() : null,
                'created_by' => $user->id,
            ]);

            $contact->load(['creator', 'company']);

            DB::commit();

            // Notificar a administradores (admin y administrador). Errores de correo/BD no revierten el contacto.
            $admins = User::administradoresParaNotificaciones();
            if ($admins->isEmpty()) {
                Log::warning('Nuevo contacto creado pero no hay usuarios con rol admin o administrador para notificar.', [
                    'contact_id' => $contact->id,
                ]);
            }
            foreach ($admins as $admin) {
                try {
                    $admin->notify(new NewContactAddedNotification($contact));
                } catch (Throwable $e) {
                    Log::error('No se pudo enviar notificación de nuevo contacto al admin', [
                        'admin_id' => $admin->id,
                        'contact_id' => $contact->id,
                        'message' => $e->getMessage(),
                    ]);
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $approvalStatus === 'aprobado'
                        ? 'Contacto creado exitosamente.'
                        : 'El contacto será visible cuando un administrador lo apruebe.',
                    'pending_approval' => $approvalStatus !== 'aprobado',
                    'redirect' => route('contacts.index'),
                ], 201);
            }

            $redirect = redirect()->route('contacts.index')
                ->with('success', $approvalStatus === 'aprobado'
                    ? 'Contacto creado exitosamente.'
                    : 'Contacto registrado correctamente.');

            if ($approvalStatus !== 'aprobado') {
                $redirect->with('warning', 'Aviso: este contacto no será visible para el resto del equipo hasta que un administrador lo apruebe.');
            }

            return $redirect;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear contacto: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            $userMessage = 'Error al crear el contacto. Por favor, intente nuevamente.';
            if (config('app.debug')) {
                $userMessage .= ' Detalle: ' . $e->getMessage();
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $userMessage,
                ], 422);
            }

            return back()->withInput()
                ->with('error', $userMessage);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);

        $contact->load(['company', 'followUps', 'creator']);

        return $this->resolveView('contacts.show', 'user.contacts.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);

        $companies = Company::forExecutiveContactForm(request()->user());

        return $this->resolveView('contacts.edit', 'user.contacts.edit', compact('contact', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $this->authorize('update', $contact);
        $this->authorize('view', Company::findOrFail($request->validated('company_id')));

        try {
            $statusAnterior = $contact->status_color;
            $nuevoStatus = $request->input('status_color', $contact->status_color);

            $data = $request->validated();
            $data['email_activo'] = $request->boolean('email_activo', $contact->email_activo);

            $contact->update($data);

            // Si el contacto pasa a "vendido", crear registro en Historial de Ventas
            // para que aparezca y se pueda completar la ficha de venta.
            if ($nuevoStatus === 'vendido' && $statusAnterior !== 'vendido' && $contact->company_id) {
                Sale::create([
                    'company_id' => $contact->company_id,
                    'contact_id' => $contact->id,
                    'nombre_servicio' => 'Venta desde contacto: ' . $contact->nombre_completo,
                    'fecha_venta' => now(),
                    'monto' => null,
                    'tipo_pago' => null,
                    'participantes' => null,
                    'notas' => 'Registrado al marcar el contacto como Vendido. Complete los datos de la venta.',
                    'created_by' => auth()->id(),
                ]);
            }

            return redirect()->to(\App\Support\CrmNavigation::redirectTargetFromRequest($request, route('contacts.show', $contact)))
                ->with('success', 'Contacto actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el contacto. Por favor, intente nuevamente.');
        }
    }

    /**
     * Actualizar rápidamente el estado del correo (activado / desactivado) desde la ficha.
     */
    public function updateEmailStatus(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $request->validate([
            'email_activo' => 'required|boolean',
        ]);

        $contact->update([
            'email_activo' => (bool) $request->email_activo,
        ]);

        return back();
    }

    /**
     * Actualizar solo las notas del contacto desde la ficha (sin pasar por la vista de edición completa).
     */
    public function updateNotes(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'notas' => 'nullable|string|max:2000',
        ]);

        $contact->update([
            'notas' => $validated['notas'] ?? null,
        ]);

        return back()->with('success', 'Notas actualizadas correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);

        try {
            $contact->delete();
            return redirect()->route('contacts.index')
                ->with('success', 'Contacto eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el contacto. Por favor, intente nuevamente.');
        }
    }

    /**
     * Solicitar eliminación de contacto (requiere aprobación de administrador).
     */
    public function requestDeletion(Request $request, Contact $contact)
    {
        $this->authorize('requestDeletion', $contact);

        $validated = $request->validate([
            'motivo' => 'required|string|max:500',
        ], [
            'motivo.required' => 'Debe escribir un motivo para solicitar la eliminación del contacto.',
            'motivo.max' => 'El motivo no puede superar los 500 caracteres.',
        ]);

        $contact->update([
            'deletion_pending' => true,
            'deletion_requested_by' => auth()->id(),
            'deletion_requested_at' => now(),
            'deletion_reason' => $validated['motivo'],
            'deletion_resolution' => null,
            'deletion_resolution_note' => null,
            'deletion_resolved_at' => null,
            'deletion_resolved_by' => null,
            'deletion_decision_user_id' => null,
        ]);

        return back()->with('success', 'Solicitud de eliminación enviada. Un administrador la revisará en Solicitudes pendientes.');
    }

    /**
     * Genera PDF de Ficha de Inscripción del contacto
     */
    public function generatePdf(Contact $contact)
    {
        $this->authorize('generatePdf', $contact);

        $contact->load(['company']);

        $pdf = Pdf::loadView('contacts.pdf.ficha-inscripcion', compact('contact'));

        return $pdf->download('Ficha_Inscripcion_' . $contact->nombre_completo . '.pdf');
    }

    /**
     * Genera Word (DOC) de Ficha de Inscripción del contacto
     */
    public function generateWord(Contact $contact)
    {
        $this->authorize('generatePdf', $contact);

        $contact->load(['company']);

        $html = view('contacts.pdf.ficha-inscripcion', compact('contact'))->render();

        $filename = 'Ficha_Inscripcion_' . $contact->nombre_completo . '.doc';

        return response($html)
            ->header('Content-Type', 'application/msword; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
