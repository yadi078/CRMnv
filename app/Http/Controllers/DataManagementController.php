<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
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
        $contact->load('company');
        return response()->json($contact);
    }

    public function updateContact(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'nombre_completo' => 'sometimes|string|max:255',
            'puesto_de_trabajo' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:255',
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
        $contact->delete();
        return response()->json(['success' => true]);
    }

    public function getCompany(Company $company)
    {
        $company->load('contacts');
        return response()->json($company);
    }

    public function updateCompany(Request $request, Company $company)
    {
        $validated = $request->validate([
            'nombre_comercial' => 'sometimes|string|max:255',
            'rfc' => 'sometimes|string|size:13',
            'sector' => 'nullable|string',
            'municipio' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'ejecutivo_asignado' => 'nullable|string|max:255',
            'datos_fiscales' => 'nullable|string',
            'status_color' => 'sometimes|in:verde,amarillo,rojo',
        ]);
        $company->update($validated);
        return response()->json(['success' => true, 'company' => $company->fresh()]);
    }

    public function destroyCompany(Company $company)
    {
        $company->delete();
        return response()->json(['success' => true]);
    }

    /** Solo admin: listar tablas para exportar/importar */
    public function getTables()
    {
        $tables = ['companies', 'contacts', 'follow_ups', 'users'];
        return response()->json(['tables' => $tables]);
    }

    /** Solo admin: exportar tabla a CSV */
    public function export(Request $request): StreamedResponse
    {
        $request->validate(['table' => 'required|in:companies,contacts,follow_ups']);
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
            'table' => 'required|in:companies,contacts,follow_ups',
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
                DB::table($table)->insert($data);
                $inserted++;
            }
        }

        return back()->with('success', "Se importaron {$inserted} registros en {$table}.");
    }
}
