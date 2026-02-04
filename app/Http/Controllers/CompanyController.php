<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador de Empresas
 * 
 * Gestiona CRUD de empresas con validación de duplicados,
 * sistema de aprobación y carga masiva vía Excel
 */
class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Company::class);

        $query = Company::with(['creator', 'approver', 'contacts']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_comercial', 'like', "%{$search}%")
                  ->orWhere('rfc', 'like', "%{$search}%")
                  ->orWhere('ejecutivo_asignado', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_color')) {
            $query->porColor($request->status_color);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        // Si es usuario normal, solo ver aprobados
        if (!auth()->user()->can('companies.approve')) {
            $query->aprobados();
        }

        $companies = $query->latest()->paginate(15);

        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Company::class);

        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Los usuarios normales crean registros en estado 'pendiente'
     */
    public function store(StoreCompanyRequest $request)
    {
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $approvalStatus = $user->can('companies.approve') ? 'aprobado' : 'pendiente';

            $company = Company::create([
                'nombre_comercial' => $request->nombre_comercial,
                'rfc' => strtoupper($request->rfc),
                'sector' => $request->sector,
                'municipio' => $request->municipio,
                'estado' => $request->estado,
                'ejecutivo_asignado' => $request->ejecutivo_asignado,
                'datos_fiscales' => $request->datos_fiscales,
                'status_color' => $request->status_color ?? 'amarillo',
                'approval_status' => $approvalStatus,
                'created_by' => $user->id,
                'approved_by' => $approvalStatus === 'aprobado' ? $user->id : null,
                'approved_at' => $approvalStatus === 'aprobado' ? now() : null,
            ]);

            DB::commit();

            return redirect()->route('companies.show', $company)
                ->with('success', $approvalStatus === 'aprobado' 
                    ? 'Empresa creada exitosamente.' 
                    : 'Empresa creada. Pendiente de aprobación por un administrador.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear empresa: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error al crear la empresa. Por favor, intente nuevamente.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $this->authorize('view', $company);

        $company->load(['contacts', 'followUps.asignado', 'creator', 'approver']);

        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $this->authorize('update', $company);

        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        DB::beginTransaction();
        try {
            $company->update([
                'nombre_comercial' => $request->nombre_comercial,
                'rfc' => strtoupper($request->rfc),
                'sector' => $request->sector,
                'municipio' => $request->municipio,
                'estado' => $request->estado,
                'ejecutivo_asignado' => $request->ejecutivo_asignado,
                'datos_fiscales' => $request->datos_fiscales,
                'status_color' => $request->status_color ?? $company->status_color,
            ]);

            // Actualizar semáforo automáticamente
            $company->actualizarSemáforo();

            DB::commit();

            return redirect()->route('companies.show', $company)
                ->with('success', 'Empresa actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar empresa: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error al actualizar la empresa. Por favor, intente nuevamente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     * Solo administradores pueden borrar definitivamente
     */
    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);

        DB::beginTransaction();
        try {
            $company->delete();

            DB::commit();

            return redirect()->route('companies.index')
                ->with('success', 'Empresa eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar empresa: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Error al eliminar la empresa. Por favor, intente nuevamente.');
        }
    }

    /**
     * Verifica duplicados en tiempo real (AJAX)
     */
    public function checkDuplicates(Request $request)
    {
        $rfc = strtoupper($request->rfc);
        $nombreComercial = $request->nombre_comercial;

        $duplicates = [];

        if ($rfc) {
            $rfcExists = Company::where('rfc', $rfc)->exists();
            if ($rfcExists) {
                $duplicates['rfc'] = 'Ya existe una empresa con este RFC.';
            }
        }

        if ($nombreComercial) {
            $nombreExists = Company::where('nombre_comercial', $nombreComercial)->exists();
            if ($nombreExists) {
                $duplicates['nombre_comercial'] = 'Ya existe una empresa con este nombre comercial.';
            }
        }

        return response()->json([
            'has_duplicates' => !empty($duplicates),
            'duplicates' => $duplicates
        ]);
    }
}
