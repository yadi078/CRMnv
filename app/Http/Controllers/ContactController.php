<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesAdminUserView;
use App\Http\Controllers\Concerns\ResolvesExecutiveAssignment;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Sale;
use App\Models\User;
use App\Models\WorkArea;
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
    use ResolvesExecutiveAssignment;
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
        $prefillCompany = null;
        if ($request->filled('company_id')) {
            $cid = (int) $request->input('company_id');
            if ($cid > 0) {
                $prefillCompany = Company::query()
                    ->with('assignedExecutive')
                    ->find($cid);
                if ($prefillCompany) {
                    $this->authorize('view', $prefillCompany);
                }
            }
        }

        $companies = Company::forExecutiveContactForm($request->user());
        $workAreas = WorkArea::namesForContactForms();

        return $this->resolveView('contacts.create', 'user.contacts.create', array_merge(
            compact('companies', 'companyId', 'workAreas'),
            $this->contactExecutiveFormContext(null, $prefillCompany)
        ));
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
                'assigned_user_id' => $this->resolveContactExecutiveForSave($request, $user, false, null),
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

        $sale = $contact->latestSale();

        $contactSales = app(SalesController::class)->paginatedSalesForContact(request(), $contact);

        return $this->resolveView('contacts.show', 'user.contacts.show', compact('contact', 'sale', 'contactSales'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);

        $contact->loadMissing('company', 'assignedExecutive');

        $companies = Company::forExecutiveContactForm(request()->user());
        $workAreas = WorkArea::namesForContactForms();

        // Asegurar que la empresa actual del contacto esté en el desplegable (p. ej. pendiente de aprobación o fuera del listado filtrado).
        if ($contact->company instanceof Company
            && ! $companies->contains(fn (Company $c): bool => (int) $c->id === (int) $contact->company->id)) {
            $companies = $companies->push($contact->company)->sortBy('nombre_comercial')->values();
        }

        $sale = $contact->latestSale();
        if ($sale) {
            $sale->load(['saleParticipants', 'creator']);
        }

        $contactFichaPdfReady = $contact->fichaPdfCompletaUsingSale($sale);

        return $this->resolveView('contacts.edit', 'user.contacts.edit', array_merge(
            compact('contact', 'companies', 'workAreas', 'sale', 'contactFichaPdfReady'),
            $this->contactExecutiveFormContext($contact)
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        try {
            $statusAnterior = $contact->status_color;
            $nuevoStatus = $request->input('status_color', $contact->status_color);

            $data = $request->validated();
            $data['email_activo'] = $request->boolean('email_activo', $contact->email_activo);
            if ($request->has('ficha_registro_desbloqueada')) {
                $data['ficha_registro_desbloqueada'] = $request->boolean('ficha_registro_desbloqueada');
            }

            if ($request->user()->esAdmin()) {
                $data['assigned_user_id'] = $this->resolveContactExecutiveForSave($request, $request->user(), true, $contact);
            } else {
                unset($data['assigned_user_id']);
            }

            $contact->update($data);

            if ($request->boolean('ficha_registro_desbloqueada') && ($sale = $contact->latestSale())
                && $request->filled('sale_id') && (int) $request->input('sale_id') === (int) $sale->id) {
                $this->authorize('update', $sale);
                $this->syncSaleFromContactForm($request, $contact, $sale);
            }

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
                    'nombre_consultor' => $request->user()?->name,
                ]);
            }

            return redirect()->to(\App\Support\CrmNavigation::redirectTargetFromRequest($request, route('contacts.show', $contact)))
                ->with('success', 'Contacto actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar contacto: '.$e->getMessage(), [
                'contact_id' => $contact->id,
                'exception' => $e::class,
                'trace' => $e->getTraceAsString(),
            ]);

            $msg = 'Error al actualizar el contacto. Por favor, intente nuevamente.';
            if (config('app.debug')) {
                $msg .= ' Detalle: '.$e->getMessage();
            }

            return back()->withInput()->with('error', $msg);
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
     * Actualiza la venta vinculada desde el formulario de edición de contacto (ficha de registro).
     */
    protected function syncSaleFromContactForm(Request $request, Contact $contact, Sale $sale): void
    {
        $validated = $request->validate([
            'sale_id' => 'required|integer|in:' . $sale->id,
            'nombre_servicio' => 'nullable|string|max:255',
            'tipo_curso' => 'nullable|string|max:255',
            'fecha_venta' => 'nullable|date',
            'monto' => 'nullable|numeric|min:0',
            'incluye_iva' => 'nullable|boolean',
            'tipo_pago' => 'nullable|string|max:500',
            'participantes' => 'nullable|integer|min:1',
            'sale_notas' => 'nullable|string|max:2000',
            'sale_colonia_cp' => 'nullable|string|max:255',
            'forma_pago' => 'nullable|string|max:100',
            'uso_cfdi' => 'nullable|string|max:100',
            'orden_compra' => 'nullable|string|max:100',
            'condiciones_pago' => 'nullable|string|max:2000',
            'modalidad' => 'nullable|string|max:255',
            'sede' => 'nullable|string|max:255',
            'fecha_evento' => 'nullable|date',
            'horario_evento' => 'nullable|string|max:120',
            'factura_referencia' => 'nullable|string|max:255',
            'participantes_nombres' => 'nullable|array',
            'participantes_nombres.*' => 'nullable|string|max:100|regex:/^[\pL\s]+$/u',
            'participantes_emails' => 'nullable|array',
            'participantes_emails.*' => 'nullable|email|max:255',
        ], [
            'participantes_nombres.*.regex' => 'El nombre de cada participante solo puede contener letras y espacios.',
            'participantes_emails.*.email' => 'Cada correo de participante debe ser un correo electrónico válido.',
        ]);

        $sale->update([
            'company_id' => $contact->company_id,
            'contact_id' => $contact->id,
            'nombre_consultor' => $sale->nombre_consultor
                ?? $sale->creator?->name
                ?? $request->user()?->name,
            'nombre_servicio' => $validated['nombre_servicio'] ?? $sale->nombre_servicio,
            'tipo_curso' => array_key_exists('tipo_curso', $validated) ? $validated['tipo_curso'] : $sale->tipo_curso,
            'fecha_venta' => $validated['fecha_venta'] ?? $sale->fecha_venta?->format('Y-m-d'),
            'monto' => array_key_exists('monto', $validated) ? $validated['monto'] : $sale->monto,
            'incluye_iva' => $request->boolean('incluye_iva', $sale->incluye_iva ?? true),
            'tipo_pago' => $validated['tipo_pago'] ?? $sale->tipo_pago,
            'participantes' => $validated['participantes'] ?? $sale->participantes,
            'notas' => array_key_exists('sale_notas', $validated) ? $validated['sale_notas'] : $sale->notas,
            'colonia_cp' => $validated['sale_colonia_cp'] ?? $sale->colonia_cp,
            'regimen_fiscal' => $contact->regimen_fiscal ?? $sale->regimen_fiscal,
            'forma_pago' => $validated['forma_pago'] ?? $sale->forma_pago,
            'uso_cfdi' => $validated['uso_cfdi'] ?? $sale->uso_cfdi,
            'orden_compra' => $validated['orden_compra'] ?? $sale->orden_compra,
            'condiciones_pago' => $validated['condiciones_pago'] ?? $sale->condiciones_pago,
            'modalidad' => $validated['modalidad'] ?? $sale->modalidad,
            'sede' => $validated['sede'] ?? $sale->sede,
            'fecha_evento' => $validated['fecha_evento'] ?? $sale->fecha_evento,
            'horario_evento' => $validated['horario_evento'] ?? $sale->horario_evento,
            'factura_referencia' => $validated['factura_referencia'] ?? $sale->factura_referencia,
        ]);

        $sale->saleParticipants()->delete();
        if ($request->filled('participantes_nombres') && is_array($request->participantes_nombres)) {
            foreach ($request->participantes_nombres as $i => $nombre) {
                if (trim((string) $nombre) !== '') {
                    $sale->saleParticipants()->create([
                        'nombre' => $nombre,
                        'email' => $request->participantes_emails[$i] ?? null,
                        'orden' => $i,
                    ]);
                }
            }
        }
    }

    /**
     * Genera PDF de Ficha de Inscripción del contacto
     */
    public function generatePdf(Contact $contact)
    {
        $this->authorize('generatePdf', $contact);

        $contact->load(['company']);
        $sale = $contact->latestSale();
        if ($sale && $contact->fichaPdfCompleta()) {
            $sale->load(['company', 'contact', 'creator', 'saleParticipants']);

            $pdf = Pdf::loadView('user.sales.pdf.ficha-venta', compact('sale'));

            $slug = \Illuminate\Support\Str::slug($sale->nombre_servicio);
            $fechaNombre = $sale->fecha_venta?->format('Y-m-d') ?? now()->format('Y-m-d');
            $filename = 'Ficha_Inscripcion_' . $slug . '_' . $fechaNombre . '.pdf';

            return $pdf->download($filename);
        }

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
        $sale = $contact->latestSale();
        if ($sale && $contact->fichaPdfCompleta()) {
            $sale->load(['company', 'contact', 'creator', 'saleParticipants']);
            $html = view('user.sales.pdf.ficha-venta', compact('sale'))->render();
            $fechaDoc = $sale->fecha_venta?->format('Y-m-d') ?? now()->format('Y-m-d');
            $filename = 'Ficha_Inscripcion_' . \Illuminate\Support\Str::slug($sale->nombre_servicio) . '_' . $fechaDoc . '.doc';

            return response($html)
                ->header('Content-Type', 'application/msword; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        $html = view('contacts.pdf.ficha-inscripcion', compact('contact'))->render();

        $filename = 'Ficha_Inscripcion_' . $contact->nombre_completo . '.doc';

        return response($html)
            ->header('Content-Type', 'application/msword; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
