<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesAdminUserView;
use App\Models\Contact;
use App\Models\Company;
use App\Models\User;
use App\Notifications\NewContactAddedNotification;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $query = Contact::with(['company', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('company', function($q) use ($search) {
                      $q->where('nombre_comercial', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $contacts = $query->latest()->paginate(15);
        $companies = Company::aprobadosOrdenados()->get();

        return $this->resolveView('contacts.index', 'user.contacts.index', compact('contacts', 'companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Contact::class);

        $companyId = $request->company_id;
        $companies = Company::aprobadosOrdenados()->get();

        return $this->resolveView('contacts.create', 'user.contacts.create', compact('companies', 'companyId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        DB::beginTransaction();
        try {
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
                'municipio' => $request->municipio,
                'estado' => $request->estado,
                'notas' => $request->notas,
                'created_by' => auth()->id(),
            ]);

            $contact->load(['creator', 'company']);

            DB::commit();

            // Notificar a todos los admins cuando se agrega un nuevo cliente (desde vista admin o usuario)
            foreach (User::role('admin')->get() as $admin) {
                $admin->notify(new NewContactAddedNotification($contact));
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contacto creado exitosamente.',
                    'redirect' => route('contacts.index'),
                ], 201);
            }

            return redirect()->route('contacts.index')
                ->with('success', 'Contacto creado exitosamente.');
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

        $companies = Company::aprobadosOrdenados()->get();

        return $this->resolveView('contacts.edit', 'user.contacts.edit', compact('contact', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        try {
            $contact->update($request->validated());
            return redirect()->route('contacts.show', $contact)
                ->with('success', 'Contacto actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el contacto. Por favor, intente nuevamente.');
        }
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
     * Genera PDF de Ficha de Inscripción del contacto
     */
    public function generatePdf(Contact $contact)
    {
        $this->authorize('generatePdf', $contact);

        $contact->load(['company']);

        $pdf = Pdf::loadView('contacts.pdf.ficha-inscripcion', compact('contact'));

        return $pdf->download('Ficha_Inscripcion_' . $contact->nombre_completo . '.pdf');
    }
}
