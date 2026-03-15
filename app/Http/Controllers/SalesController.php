<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Sale;
use App\Models\SaleParticipant;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controlador de Historial de Ventas
 *
 * Gestión de cursos y servicios vendidos por empresa.
 */
class SalesController extends Controller
{
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
        $companies = Company::aprobadosOrdenados()->get(['id', 'nombre_comercial']);

        return view('user.sales.index', compact('sales', 'companies'));
    }

    /**
     * Mostrar formulario para crear venta
     */
    public function create(Request $request)
    {
        $this->authorize('create', Sale::class);

        $companyId = $request->get('company_id', old('company_id'));
        $companies = Company::aprobadosOrdenados()->get();
        $contacts = $companyId
            ? Contact::where('company_id', $companyId)->orderBy('nombre_completo')->get()
            : collect();
        $company = $companyId ? Company::find($companyId) : null;
        $contact = (old('contact_id') && $companyId) ? Contact::find(old('contact_id')) : null;

        return view('user.sales.create', compact('companies', 'companyId', 'contacts', 'company', 'contact'));
    }

    /**
     * Guardar nueva venta
     */
    public function store(Request $request)
    {
        $this->authorize('create', Sale::class);

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'contact_id' => [
                'nullable',
                Rule::exists('contacts', 'id')->where('company_id', $request->company_id),
            ],
            'nombre_servicio' => 'required|string|max:255',
            'fecha_venta' => 'required|date',
            'monto' => 'nullable|numeric|min:0',
            'incluye_iva' => 'nullable|boolean',
            'tipo_pago' => 'nullable|string|max:50',
            'participantes' => 'nullable|integer|min:1',
            'notas' => 'nullable|string|max:2000',
            'colonia_cp' => 'nullable|string|max:255',
            'regimen_fiscal' => 'nullable|string|max:255',
            'forma_pago' => 'nullable|string|max:100',
            'uso_cfdi' => 'nullable|string|max:100',
            'orden_compra' => 'nullable|string|max:100',
            'participantes_nombres' => 'nullable|array',
            'participantes_nombres.*' => 'nullable|string|max:255',
            'participantes_emails' => 'nullable|array',
            'participantes_emails.*' => 'nullable|email|max:255',
        ]);

        $sale = Sale::create([
            ...collect($validated)->except(['participantes_nombres', 'participantes_emails'])->all(),
            'incluye_iva' => $request->boolean('incluye_iva', true),
            'created_by' => auth()->id(),
        ]);

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

        return redirect()->route('user.sales.index')
            ->with('success', 'Venta registrada exitosamente.');
    }

    /**
     * Mostrar detalle de venta
     */
    public function show(Sale $sale)
    {
        $this->authorize('view', $sale);
        $sale->load(['company', 'contact', 'creator', 'saleParticipants']);

        return view('user.sales.show', compact('sale'));
    }

    /**
     * Mostrar formulario para editar venta
     */
    public function edit(Sale $sale)
    {
        $this->authorize('update', $sale);
        $companies = Company::aprobadosOrdenados()->get();
        $contacts = Contact::where('company_id', $sale->company_id)->orderBy('nombre_completo')->get();

        // Incluir el contacto comprador en la lista si existe y no está (p. ej. soft-deleted)
        if ($sale->contact_id) {
            $sale->load('contact');
            if ($sale->contact && !$contacts->contains('id', $sale->contact_id)) {
                $contacts = $contacts->prepend($sale->contact)->sortBy('nombre_completo')->values();
            }
        }

        $sale->load('saleParticipants');
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
            'contact_id' => [
                'nullable',
                Rule::exists('contacts', 'id')->where('company_id', $request->company_id),
            ],
            'nombre_servicio' => 'required|string|max:255',
            'fecha_venta' => 'required|date',
            'monto' => 'nullable|numeric|min:0',
            'incluye_iva' => 'nullable|boolean',
            'tipo_pago' => 'nullable|string|max:50',
            'participantes' => 'nullable|integer|min:1',
            'notas' => 'nullable|string|max:2000',
            'colonia_cp' => 'nullable|string|max:255',
            'regimen_fiscal' => 'nullable|string|max:255',
            'forma_pago' => 'nullable|string|max:100',
            'uso_cfdi' => 'nullable|string|max:100',
            'orden_compra' => 'nullable|string|max:100',
            'participantes_nombres' => 'nullable|array',
            'participantes_nombres.*' => 'nullable|string|max:255',
            'participantes_emails' => 'nullable|array',
            'participantes_emails.*' => 'nullable|email|max:255',
        ]);

        $sale->update([
            ...collect($validated)->except(['participantes_nombres', 'participantes_emails'])->all(),
            'incluye_iva' => $request->boolean('incluye_iva', $sale->incluye_iva),
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

        return redirect()->route('user.sales.show', $sale)
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
    public function fichaPdf(Sale $sale)
    {
        $this->authorize('view', $sale);

        $sale->load(['company', 'contact', 'creator', 'saleParticipants']);

        $pdf = Pdf::loadView('user.sales.pdf.ficha-venta', compact('sale'));

        $filename = 'Ficha_Inscripcion_' . \Str::slug($sale->nombre_servicio) . '_' . $sale->fecha_venta->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
