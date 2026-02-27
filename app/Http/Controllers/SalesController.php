<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Company;
use Illuminate\Http\Request;

/**
 * Controlador de Historial de Ventas
 *
 * Gestión de cursos y servicios vendidos por empresa.
 */
class SalesController extends Controller
{
    /**
     * Mostrar listado de ventas
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Sale::class);

        $query = Sale::with(['company', 'creator']);

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

        $companyId = $request->company_id;
        $companies = Company::aprobadosOrdenados()->get();

        return view('user.sales.create', compact('companies', 'companyId'));
    }

    /**
     * Guardar nueva venta
     */
    public function store(Request $request)
    {
        $this->authorize('create', Sale::class);

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'nombre_servicio' => 'required|string|max:255',
            'fecha_venta' => 'required|date',
            'monto' => 'nullable|numeric|min:0',
            'tipo_pago' => 'nullable|string|max:50',
            'participantes' => 'nullable|integer|min:1',
            'notas' => 'nullable|string|max:2000',
        ]);

        $sale = Sale::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('user.sales.index')
            ->with('success', 'Venta registrada exitosamente.');
    }

    /**
     * Mostrar detalle de venta
     */
    public function show(Sale $sale)
    {
        $this->authorize('view', $sale);
        $sale->load(['company', 'creator']);

        return view('user.sales.show', compact('sale'));
    }

    /**
     * Mostrar formulario para editar venta
     */
    public function edit(Sale $sale)
    {
        $this->authorize('update', $sale);
        $companies = Company::aprobadosOrdenados()->get();

        return view('user.sales.edit', compact('sale', 'companies'));
    }

    /**
     * Actualizar venta
     */
    public function update(Request $request, Sale $sale)
    {
        $this->authorize('update', $sale);

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'nombre_servicio' => 'required|string|max:255',
            'fecha_venta' => 'required|date',
            'monto' => 'nullable|numeric|min:0',
            'tipo_pago' => 'nullable|string|max:50',
            'participantes' => 'nullable|integer|min:1',
            'notas' => 'nullable|string|max:2000',
        ]);

        $sale->update($validated);

        return redirect()->route('user.sales.index')
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
}
