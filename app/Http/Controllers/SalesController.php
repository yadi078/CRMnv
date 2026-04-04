<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesAdminUserView;
use App\Models\Contact;
use App\Models\Sale;
use App\Models\SaleParticipant;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controlador de Historial de Ventas
 *
 * Gestión de cursos y servicios vendidos por empresa.
 */
class SalesController extends Controller
{
    use ResolvesAdminUserView;

    /**
     * Ventas visibles en un ámbito (empresa o contacto): ejecutivo solo las propias;
     * administrador ve todas las de ese ámbito (alineado con la ficha de empresa).
     */
    private function scopedSalesBaseQuery(Request $request): Builder
    {
        $query = Sale::with(['company', 'contact', 'creator']);

        if (! $request->user()->esAdmin()) {
            $query->where('created_by', $request->user()->id);
        }

        $filtroFecha = $request->get('filtro_fecha', 'todos');
        if ($filtroFecha !== 'todos') {
            $dias = match ($filtroFecha) {
                '7' => 7,
                '14' => 14,
                '30' => 30,
                default => null,
            };
            if ($dias !== null) {
                $query->where('fecha_venta', '>=', now()->subDays($dias));
            }
        }

        return $query;
    }

    /**
     * Historial de ventas filtrado por empresa (paginado).
     */
    public function indexForCompany(Request $request, Company $company)
    {
        $this->authorize('viewAny', Sale::class);
        $this->authorize('view', $company);

        $sales = $this->scopedSalesBaseQuery($request)
            ->where('company_id', $company->id)
            ->latest('fecha_venta')
            ->paginate(15)
            ->withQueryString();

        return view('user.sales.index-scoped', [
            'scope' => 'company',
            'company' => $company,
            'contact' => null,
            'sales' => $sales,
            'formAction' => route('user.sales.by-company', $company),
        ]);
    }

    /**
     * Historial de ventas filtrado por contacto (y su empresa), paginado.
     */
    public function indexForContact(Request $request, Contact $contact)
    {
        $this->authorize('viewAny', Sale::class);
        $this->authorize('view', $contact);

        $sales = $this->paginatedSalesForContact($request, $contact);

        return view('user.sales.index-scoped', [
            'scope' => 'contact',
            'company' => $contact->company,
            'contact' => $contact,
            'sales' => $sales,
            'formAction' => route('user.sales.by-contact', $contact),
        ]);
    }

    /**
     * Misma consulta que indexForContact, reutilizable en la ficha de contacto.
     */
    public function paginatedSalesForContact(Request $request, Contact $contact)
    {
        $this->authorize('viewAny', Sale::class);
        $this->authorize('view', $contact);

        $contact->loadMissing('company');

        return $this->scopedSalesBaseQuery($request)
            ->where('company_id', $contact->company_id)
            ->where('contact_id', $contact->id)
            ->latest('fecha_venta')
            ->paginate(15)
            ->withQueryString();
    }

    /**
     * Mostrar listado de ventas (solo las del usuario actual)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Sale::class);

        $query = Sale::with(['company', 'contact', 'creator'])
            ->where('created_by', auth()->id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_servicio', 'like', "%{$search}%")
                    ->orWhereHas('company', fn ($q) => $q->where('nombre_comercial', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $filtroFecha = $request->get('filtro_fecha', 'todos');
        if ($filtroFecha !== 'todos') {
            $dias = match ($filtroFecha) {
                '7' => 7,
                '14' => 14,
                '30' => 30,
                default => null,
            };
            if ($dias !== null) {
                $query->where('fecha_venta', '>=', now()->subDays($dias));
            }
        }

        $sales = $query->latest('fecha_venta')->paginate(15)->withQueryString();
        $user = $request->user();
        $companies = Company::query()
            ->when($user->esAdmin(), fn ($q) => $q->aprobados())
            ->when(! $user->esAdmin(), fn ($q) => $q->accessibleForExecutive($user))
            ->orderBy('nombre_comercial')
            ->get(['id', 'nombre_comercial']);

        return view('user.sales.index', compact('sales', 'companies'));
    }

    /**
     * Mostrar formulario para crear venta
     */
    public function create(Request $request)
    {
        $this->authorize('create', Sale::class);

        $companyId = $request->get('company_id', old('company_id'));
        $user = $request->user();
        $companies = Company::forExecutiveFollowUpAndSales($user);
        $contacts = $companyId
            ? Contact::where('company_id', $companyId)
                ->when(! $user->esAdmin(), fn ($q) => $q->accessibleForExecutive($user))
                ->orderBy('nombre_completo')
                ->get()
            : collect();
        $company = $companyId ? Company::find($companyId) : null;
        $prefillContactId = $request->get('contact_id');
        $resolvedContactId = old('contact_id', $prefillContactId);
        $contact = ($resolvedContactId && $companyId) ? Contact::find($resolvedContactId) : null;

        return $this->resolveView('sales.create', 'user.sales.create', compact('companies', 'companyId', 'contacts', 'company', 'contact', 'prefillContactId'));
    }

    /**
     * Guardar nueva venta
     */
    public function store(Request $request)
    {
        $this->authorize('create', Sale::class);

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'contact_id' => $this->contactIdRules($request),
            'tipo_curso' => 'required|string|max:255',
            'nombre_servicio' => 'nullable|string|max:255',
            'fecha_venta' => 'required|date',
            'monto' => 'nullable|numeric|min:0',
            'incluye_iva' => 'nullable|boolean',
            'tipo_pago' => 'nullable|string|max:500',
            'participantes' => 'nullable|integer|min:1',
            'notas' => 'nullable|string|max:2000',
            'colonia_cp' => 'nullable|string|max:255',
            'regimen_fiscal' => 'nullable|string|max:255',
            'forma_pago' => 'nullable|string|max:500',
            'uso_cfdi' => 'nullable|string|max:100',
            'orden_compra' => 'nullable|string|max:500',
            'facturacion_calle_numero' => 'nullable|string|max:500',
            'facturacion_rfc' => 'nullable|string|max:20',
            'email_facturacion' => 'nullable|string|max:500',
            'condiciones_pago' => 'nullable|string|max:2000',
            'modalidad' => 'nullable|string|max:255',
            'sede' => 'nullable|string|max:255',
            'fecha_evento' => 'nullable|date',
            'horario_evento' => 'nullable|string|max:120',
            'factura_referencia' => 'nullable|string|max:255',
            'participantes_texto' => 'nullable|string',
        ], [
            'company_id.required' => 'La empresa es obligatoria.',
            'company_id.exists' => 'La empresa seleccionada no es válida.',
            'tipo_curso.required' => 'El nombre del curso o servicio es obligatorio.',
            'fecha_venta.required' => 'La fecha de la venta es obligatoria.',
            'fecha_venta.date' => 'La fecha de la venta no tiene un formato válido.',
            'required' => 'Este campo es obligatorio.',
            'email' => 'Ingrese un correo electrónico válido.',
        ]);

        $validated['nombre_servicio'] = $validated['tipo_curso'];

        $fechaVenta = \Carbon\Carbon::parse($validated['fecha_venta'])->startOfDay();
        if ($fechaVenta->lt(now()->startOfDay())) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'fecha_venta' => ['La fecha de la venta no puede ser anterior a hoy.'],
            ]);
        }

        $company = Company::findOrFail($validated['company_id']);
        $this->authorize('view', $company);
        if (! empty($validated['contact_id'])) {
            $this->authorize('view', Contact::findOrFail($validated['contact_id']));
        }

        $sale = Sale::create([
            ...collect($validated)->all(),
            'incluye_iva' => $request->boolean('incluye_iva', true),
            'created_by' => auth()->id(),
            'nombre_consultor' => $request->user()?->name,
        ]);

        if ($request->input('post_action') === 'ficha') {
            if (! empty($sale->contact_id)) {
                return redirect()->route('contacts.show', $sale->contact_id)
                    ->with('download_ficha_sale_id', $sale->id);
            }

            return redirect()->route('user.sales.index')
                ->with('download_ficha_sale_id', $sale->id);
        }

        return redirect()->route('user.sales.index')
            ->with('success', 'Venta registrada correctamente.');
    }

    /**
     * Mostrar detalle de venta
     */
    public function show(Sale $sale)
    {
        $this->authorize('view', $sale);

        return redirect()->route('user.sales.ficha-pdf', $sale);
    }

    /**
     * Mostrar formulario para editar venta
     */
    public function edit(Sale $sale)
    {
        $this->authorize('update', $sale);
        $user = request()->user();
        $companies = Company::forExecutiveFollowUpAndSales($user);
        $contacts = Contact::where('company_id', $sale->company_id)
            ->when(! $user->esAdmin(), fn ($q) => $q->accessibleForExecutive($user))
            ->orderBy('nombre_completo')
            ->get();

        // Incluir el contacto comprador en la lista si existe y no está (p. ej. soft-deleted)
        if ($sale->contact_id) {
            $sale->load('contact');
            if ($sale->contact && !$contacts->contains('id', $sale->contact_id)) {
                $contacts = $contacts->prepend($sale->contact)->sortBy('nombre_completo')->values();
            }
        }

        $sale->load(['saleParticipants', 'contact', 'company']);
        return view('user.sales.edit', compact('sale', 'companies', 'contacts'));
    }

    /**
     * Actualizar venta
     */
    public function update(Request $request, Sale $sale)
    {
        $this->authorize('update', $sale);

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'contact_id' => $this->contactIdRules($request),
            'tipo_curso' => 'required|string|max:255',
            'nombre_servicio' => 'nullable|string|max:255',
            'fecha_venta' => 'required|date',
            'monto' => 'nullable|numeric|min:0',
            'incluye_iva' => 'nullable|boolean',
            'tipo_pago' => 'nullable|string|max:500',
            'participantes' => 'nullable|integer|min:1',
            'notas' => 'nullable|string|max:2000',
            'colonia_cp' => 'nullable|string|max:255',
            'regimen_fiscal' => 'nullable|string|max:255',
            'forma_pago' => 'nullable|string|max:500',
            'uso_cfdi' => 'nullable|string|max:100',
            'orden_compra' => 'nullable|string|max:500',
            'facturacion_calle_numero' => 'nullable|string|max:500',
            'facturacion_rfc' => 'nullable|string|max:20',
            'email_facturacion' => 'nullable|string|max:500',
            'condiciones_pago' => 'nullable|string|max:2000',
            'modalidad' => 'nullable|string|max:255',
            'sede' => 'nullable|string|max:255',
            'fecha_evento' => 'nullable|date',
            'horario_evento' => 'nullable|string|max:120',
            'factura_referencia' => 'nullable|string|max:255',
            'participantes_texto' => 'nullable|string',
        ], [
            'company_id.required' => 'La empresa es obligatoria.',
            'company_id.exists' => 'La empresa seleccionada no es válida.',
            'tipo_curso.required' => 'El nombre del curso o servicio es obligatorio.',
            'fecha_venta.required' => 'La fecha de la venta es obligatoria.',
            'fecha_venta.date' => 'La fecha de la venta no tiene un formato válido.',
            'required' => 'Este campo es obligatorio.',
            'email' => 'Ingrese un correo electrónico válido.',
        ]);

        $validated['nombre_servicio'] = $validated['tipo_curso'];

        $company = Company::findOrFail($validated['company_id']);
        $this->authorize('view', $company);
        if (! empty($validated['contact_id'])) {
            $this->authorize('view', Contact::findOrFail($validated['contact_id']));
        }

        $payload = [
            ...collect($validated)->all(),
            'incluye_iva' => $request->boolean('incluye_iva', $sale->incluye_iva),
        ];
        if ($sale->nombre_consultor === null) {
            $nombre = $sale->creator?->name ?? $request->user()?->name;
            if ($nombre) {
                $payload['nombre_consultor'] = $nombre;
            }
        }
        $sale->update($payload);

        return redirect()->route('companies.show', $sale->company_id)
            ->with('success', 'Venta actualizada correctamente.');
    }

    /**
     * Eliminar venta
     */
    public function destroy(Sale $sale)
    {
        $this->authorize('delete', $sale);
        $sale->delete();

        return redirect()->route('user.sales.index')
            ->with('success', 'Venta eliminada correctamente.');
    }

    /**
     * Descargar PDF de la ficha de venta (formato Ficha de Inscripción)
     */
    public function fichaPdf(Request $request, Sale $sale)
    {
        $this->authorize('view', $sale);

        $sale->load(['company', 'contact', 'creator', 'saleParticipants']);

        $pdf = Pdf::loadView('user.sales.pdf.ficha-venta', compact('sale'))
            ->setPaper('letter', 'portrait');

        $fechaNombre = $sale->fecha_venta?->format('Y-m-d') ?? now()->format('Y-m-d');
        $filename = 'Ficha_Inscripcion_' . \Str::slug($sale->nombre_servicio ?? 'venta') . '_' . $fechaNombre . '.pdf';

        if ($request->boolean('inline')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Descargar ficha de venta en formato Word (DOC) reutilizando la misma vista HTML
     */
    public function fichaWord(Sale $sale)
    {
        $this->authorize('view', $sale);

        $sale->load(['company', 'contact', 'creator', 'saleParticipants']);

        $html = view('user.sales.pdf.ficha-venta', compact('sale'))->render();

        $fechaDoc = $sale->fecha_venta?->format('Y-m-d') ?? now()->format('Y-m-d');
        $filename = 'Ficha_Inscripcion_' . \Str::slug($sale->nombre_servicio) . '_' . $fechaDoc . '.doc';

        return response($html)
            ->header('Content-Type', 'application/msword; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * @return array<int, \Illuminate\Validation\Rules\Exists|string>
     */
    protected function contactIdRules(Request $request): array
    {
        $user = $request->user();
        $exists = Rule::exists('contacts', 'id')->where(function ($query) use ($request, $user) {
            $query->where('company_id', $request->company_id);
            if ($user && ! $user->esAdmin()) {
                $query->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                        ->orWhere('assigned_user_id', $user->id);
                });
            }
        });

        return ['nullable', $exists];
    }
}
