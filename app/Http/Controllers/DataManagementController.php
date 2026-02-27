<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Gestión de Datos: visualización y edición de empresas/contactos.
 * Admin: además exportar/importar tablas.
 */
class DataManagementController extends Controller
{
    /**
     * Página principal de Gestión de Datos
     */
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->esAdmin();

        $companiesQuery = Company::withCount('contacts');
        if (! $isAdmin) {
            $companiesQuery->aprobados();
        }
        $companies = $companiesQuery->orderBy('nombre_comercial')->paginate(10, ['*'], 'companies_page');

        $contactsQuery = Contact::with('company');
        if (! $isAdmin) {
            $contactsQuery->whereHas('company', fn ($q) => $q->aprobados());
        }
        $contacts = $contactsQuery->latest()->paginate(10, ['*'], 'contacts_page');

        return view('data-management.index', [
            'companies' => $companies,
            'contacts' => $contacts,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function getContact(Contact $contact)
    {
        $this->authorize('view', $contact);
        $contact->load('company');
        return response()->json($contact);
    }

    public function updateContact(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'nombre_completo' => 'sometimes|string|max:255',
            'genero' => 'nullable|string|max:50',
            'puesto_de_trabajo' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'extension' => 'nullable|string|max:255',
            'email' => 'sometimes|email',
            'municipio' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
        ]);
        $contact->update($validated);
        return response()->json(['success' => true, 'contact' => $contact->fresh()]);
    }

    public function destroyContact(Contact $contact)
    {
        $this->authorize('delete', $contact);

        $contact->delete();
        return response()->json(['success' => true]);
    }

    public function getCompany(Company $company)
    {
        $this->authorize('view', $company);
        $company->load('contacts');
        return response()->json($company);
    }

    public function updateCompany(Request $request, Company $company)
    {
        $this->authorize('update', $company);

        $validated = $request->validate([
            'nombre_comercial' => 'sometimes|string|max:255',
            'rfc' => 'sometimes|nullable|string|max:13',
            'sector' => 'nullable|string',
            'municipio' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'ejecutivo_asignado' => 'nullable|string|max:255',
            'datos_fiscales' => 'nullable|string',
            'status_color' => 'sometimes|in:seguimiento,interesado,si_le_interesa_nos_llaman_o_no_compro,vendido,no_estaba',
        ]);

        $statusAnterior = $company->status_color;
        $company->update($validated);

        if (isset($validated['status_color']) && $validated['status_color'] === 'vendido' && $statusAnterior !== 'vendido') {
            Sale::create([
                'company_id' => $company->id,
                'nombre_servicio' => 'Venta registrada desde prospecto',
                'fecha_venta' => now(),
                'monto' => null,
                'tipo_pago' => null,
                'participantes' => null,
                'notas' => 'Registrado automáticamente al cambiar estado de prospecto a Vendido.',
                'created_by' => auth()->id(),
            ]);
        }

        return response()->json(['success' => true, 'company' => $company->fresh()]);
    }

    public function destroyCompany(Company $company)
    {
        $this->authorize('delete', $company);

        $company->delete();
        return response()->json(['success' => true]);
    }

    /** Solo admin: listar tablas para exportar/importar */
    public function getTables()
    {
        $tables = ['companies', 'contacts', 'follow_ups', 'sales', 'users'];
        return response()->json(['tables' => $tables]);
    }

    /** Solo admin: exportar tabla a CSV */
    public function export(Request $request): StreamedResponse
    {
        $request->validate(['table' => 'required|in:companies,contacts,follow_ups,sales']);
        $table = $request->table;

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$table}.csv\"",
        ];

        return response()->stream(function () use ($table) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM UTF-8

            $rows = DB::table($table)->get();
            if ($rows->isNotEmpty()) {
                fputcsv($handle, array_keys((array) $rows->first()));
                foreach ($rows as $row) {
                    fputcsv($handle, (array) $row);
                }
            }
            fclose($handle);
        }, 200, $headers);
    }

    /** Solo admin: importar CSV (estructura simple) */
    public function import(Request $request)
    {
        $request->validate([
            'table' => 'required|in:companies,contacts,follow_ups,sales',
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $table = $request->table;
        $path = $file->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        $header = array_shift($rows);
        $header = array_map('trim', $header);
        $inserted = 0;
        foreach ($rows as $row) {
            if (count($row) !== count($header)) {
                continue;
            }
            $data = array_combine($header, $row);
            $data = array_filter($data, fn ($v) => $v !== '' && $v !== null);
            if (! empty($data)) {
                $data['created_at'] = $data['created_at'] ?? now();
                $data['updated_at'] = $data['updated_at'] ?? now();
                DB::table($table)->insert($data);
                $inserted++;
            }
        }

        return back()->with('success', "Se importaron {$inserted} registros en {$table}.");
    }
}
