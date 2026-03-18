<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesAdminUserView;
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
    use ResolvesAdminUserView;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', FollowUp::class);

        $query = FollowUp::with(['company', 'contact', 'asignado', 'creator']);

        // Usuario no admin: solo seguimientos que creó o tiene asignados
        $user = auth()->user();
        if (! $user->esAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)->orWhere('asignado_a', $user->id);
            });
        }

        if ($request->filled('completado')) {
            $query->where('completado', $request->completado);
        }

        if ($request->filled('tipo_accion')) {
            $query->where('tipo_accion', $request->tipo_accion);
        }

        $followUps = $query->latest('fecha_alarma')->paginate(15);

        return $this->resolveView('follow-ups.index', 'user.follow-ups.index', compact('followUps'));
    }

    /**
     *  Si se pasa company_id o contact_id, se carga la empresa o contacto respectivamente.
     */
    public function create(Request $request)
    {
        $this->authorize('create', FollowUp::class);

        $companyId = $request->company_id;
        $contactId = $request->contact_id;

        $companies = Company::aprobadosOrdenados()->get();
        $contacts = Contact::with('company')->orderBy('nombre_completo')->get();

        return $this->resolveView('follow-ups.create', 'user.follow-ups.create', compact('companies', 'contacts', 'companyId', 'contactId'));
    }

    /**
     * se valida que se haya pasado company_id o contact_id, y se crea el seguimiento con la empresa o contacto respectivamente.
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

        return $this->resolveView('follow-ups.show', 'user.follow-ups.show', compact('followUp'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FollowUp $followUp)
    {
        $this->authorize('update', $followUp);

        $companies = Company::aprobadosOrdenados()->get();
        $contacts = Contact::with('company')->orderBy('nombre_completo')->get();

        return $this->resolveView('follow-ups.edit', 'user.follow-ups.edit', compact('followUp', 'companies', 'contacts'));
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
            'fecha_alarma' => 'required|date|after_or_equal:now',
            'bitacora_notas' => 'nullable|string',
            'asignado_a' => 'nullable|exists:users,id',
        ]);

        try {
            $followUp->update($validated);
            return redirect()->route('follow-ups.show', $followUp)
                ->with('success', 'Seguimiento actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el seguimiento. Por favor, intente nuevamente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FollowUp $followUp)
    {
        $this->authorize('delete', $followUp);

        try {
            $followUp->delete();
            return redirect()->route('follow-ups.index')
                ->with('success', 'Seguimiento eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el seguimiento. Por favor, intente nuevamente.');
        }
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
