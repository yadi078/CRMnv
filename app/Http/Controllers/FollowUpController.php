<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Seguimientos
 * 
 * Gestiona bitácora de notas y sistema de alarmas programadas
 */
class FollowUpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', FollowUp::class);

        $query = FollowUp::with(['company', 'contact', 'asignado', 'creator']);

        if ($request->filled('completado')) {
            $query->where('completado', $request->completado);
        }

        if ($request->filled('tipo_accion')) {
            $query->where('tipo_accion', $request->tipo_accion);
        }

        $followUps = $query->latest('fecha_alarma')->paginate(15);

        return view('follow-ups.index', compact('followUps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', FollowUp::class);

        $companyId = $request->company_id;
        $contactId = $request->contact_id;

        $companies = Company::aprobados()->orderBy('nombre_comercial')->get();
        $contacts = Contact::with('company')->orderBy('nombre_completo')->get();

        return view('follow-ups.create', compact('companies', 'contacts', 'companyId', 'contactId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', FollowUp::class);

        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'tipo_accion' => 'required|in:llamada,reunión,cierre',
            'fecha_alarma' => 'required|date|after_or_equal:now',
            'bitacora_notas' => 'nullable|string',
            'asignado_a' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $followUp = FollowUp::create([
                'company_id' => $validated['company_id'] ?? null,
                'contact_id' => $validated['contact_id'] ?? null,
                'tipo_accion' => $validated['tipo_accion'],
                'fecha_alarma' => $validated['fecha_alarma'],
                'bitacora_notas' => $validated['bitacora_notas'] ?? null,
                'asignado_a' => $validated['asignado_a'] ?? auth()->id(),
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('follow-ups.show', $followUp)
                ->with('success', 'Seguimiento creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Error al crear el seguimiento. Por favor, intente nuevamente.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FollowUp $followUp)
    {
        $this->authorize('view', $followUp);

        $followUp->load(['company', 'contact', 'asignado', 'creator']);

        return view('follow-ups.show', compact('followUp'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FollowUp $followUp)
    {
        $this->authorize('update', $followUp);

        $companies = Company::aprobados()->orderBy('nombre_comercial')->get();
        $contacts = Contact::with('company')->orderBy('nombre_completo')->get();

        return view('follow-ups.edit', compact('followUp', 'companies', 'contacts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FollowUp $followUp)
    {
        $this->authorize('update', $followUp);

        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'tipo_accion' => 'required|in:llamada,reunión,cierre',
            'fecha_alarma' => 'required|date',
            'bitacora_notas' => 'nullable|string',
            'asignado_a' => 'nullable|exists:users,id',
        ]);

        $followUp->update($validated);

        return redirect()->route('follow-ups.show', $followUp)
            ->with('success', 'Seguimiento actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FollowUp $followUp)
    {
        $this->authorize('delete', $followUp);

        $followUp->delete();

        return redirect()->route('follow-ups.index')
            ->with('success', 'Seguimiento eliminado exitosamente.');
    }

    /**
     * Marca un seguimiento como completado
     */
    public function complete(FollowUp $followUp)
    {
        $this->authorize('update', $followUp);

        $followUp->completar();

        return back()->with('success', 'Seguimiento marcado como completado.');
    }
}
