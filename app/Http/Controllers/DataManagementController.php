<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Sale;
use App\Models\User;
use App\Rules\CommaSeparatedEmails;
use App\Support\ContactEmailList;
use App\Support\MexicanStates;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Gestión de Datos: visualización y edición de empresas/contactos.
 * Admin: además exportar/importar tablas.
 */
class DataManagementController extends Controller
{
    /** Columnas fijas del CSV de exportación (empresas y contactos). */
    private const EXPORT_CSV_HEADERS = [
        'Genero',
        'Nombre de Empresa',
        'Nombre completo',
        'Teléfono',
        'Celular',
        'Email',
        'Area de trabajo',
        'Puesto de trabajo',
        'Ciudad',
        'Estado',
        'Comercial',
        'Giro',
        'Notas',
        'Domicilio',
        'No desea recibir correos',
    ];

    /**
     * Columnas del bloque Excel «empresa + contactos» (A = etiqueta; B–K = datos).
     */
    private const EXPORT_NESTED_BLOCK_HEADERS = [
        'Nombre de Empresa',
        'Nombre completo',
        'Teléfono',
        'Celular',
        'Email',
        'Area de trabajo',
        'Puesto de trabajo',
        'Ciudad',
        'Estado',
        'Comercial',
    ];

    /**
     * Página principal de Gestión de Datos
     */
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->esAdmin();

        $companiesQuery = Company::withCount('contacts');
        if (! $isAdmin) {
            $companiesQuery->accessibleForExecutive($user);
        }
        $companies = $companiesQuery->orderBy('nombre_comercial')->paginate(10, ['*'], 'companies_page');

        $contactsQuery = Contact::with('company');
        if (! $isAdmin) {
            $contactsQuery->accessibleForExecutive($user);
        }
        $contacts = $contactsQuery->latest()->paginate(10, ['*'], 'contacts_page');

        return view('data-management.index', [
            'companies' => $companies,
            'contacts' => $contacts,
            'isAdmin' => $isAdmin,
            'empresasPorEstado' => Company::countsByEstadoForUser($user),
            'entidadesMexicanas' => MexicanStates::all(),
            'comercialesExport' => $isAdmin ? self::comercialesDisponiblesParaExport() : collect(),
        ]);
    }

    /**
     * Nombres de comercial para el filtro de exportación: valores en empresas + todos los usuarios en BD.
     *
     * @return Collection<int, string>
     */
    private static function comercialesDisponiblesParaExport(): Collection
    {
        $desdeEmpresas = Company::query()
            ->whereNotNull('ejecutivo_asignado')
            ->where('ejecutivo_asignado', '!=', '')
            ->distinct()
            ->pluck('ejecutivo_asignado');

        $desdeUsuarios = User::query()
            ->orderBy('name')
            ->pluck('name');

        return $desdeEmpresas
            ->merge($desdeUsuarios)
            ->map(fn ($s) => trim((string) $s))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
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

        if ($request->has('email') && is_string($request->input('email'))) {
            $request->merge([
                'email' => ContactEmailList::normalize($request->input('email')),
            ]);
        }

        $validated = $request->validate([
            'nombre_completo' => 'sometimes|string|max:255',
            'genero' => 'nullable|string|max:50',
            'puesto_de_trabajo' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'extension' => 'nullable|string|max:255',
            'email' => ['sometimes', 'nullable', 'string', 'max:1000', new CommaSeparatedEmails()],
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
            'telefono' => 'nullable|string|max:50',
            'celular' => 'nullable|string|max:50',
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
        $tables = ['companies', 'contacts', 'sales', 'users'];
        return response()->json(['tables' => $tables]);
    }

    /** Solo admin: exportar Excel (listados o empresas con contactos anidados). */
    public function export(Request $request): StreamedResponse
    {
        $filterByInput = (string) $request->input('filter_by', '');

        $rules = [
            'table' => 'required|in:companies,contacts,companies_with_contacts',
            'filter_by' => 'required|in:none,comercial,estado',
        ];

        if ($filterByInput === 'estado') {
            $rules['estado_entidad'] = ['required', 'string', Rule::in(MexicanStates::all())];
        } elseif ($filterByInput === 'comercial') {
            $permitidos = self::comercialesDisponiblesParaExport()->all();
            $rules['ejecutivo_asignado'] = ['required', 'string', 'max:255', Rule::in($permitidos)];
        }

        $messages = [
            'table.required' => 'Indique qué desea exportar.',
            'table.in' => 'El tipo de exportación no es válido.',
            'filter_by.required' => 'Seleccione una opción en «Descargar por».',
            'filter_by.in' => 'El criterio de descarga no es válido.',
            'estado_entidad.required' => 'Seleccione un estado en la lista.',
            'estado_entidad.in' => 'El estado seleccionado no es válido.',
            'ejecutivo_asignado.required' => 'Seleccione un ejecutivo en la lista.',
            'ejecutivo_asignado.in' => 'El ejecutivo seleccionado no es válido.',
            'ejecutivo_asignado.max' => 'El nombre del ejecutivo es demasiado largo.',
        ];

        $attributes = [
            'table' => 'tipo de exportación',
            'filter_by' => 'Descargar por',
            'estado_entidad' => 'Estado',
            'ejecutivo_asignado' => 'Ejecutivo',
        ];

        $validated = $request->validate($rules, $messages, $attributes);

        $filterBy = $validated['filter_by'];
        $entidad = $validated['estado_entidad'] ?? null;
        $comercial = $validated['ejecutivo_asignado'] ?? null;

        $table = $validated['table'];

        if ($table === 'companies_with_contacts') {
            return $this->exportCompaniesWithContactsExcel($filterBy, $entidad, $comercial);
        }

        $filename = $table;
        if ($filterBy === 'comercial') {
            $filename .= '_comercial_'.Str::slug($comercial);
        } elseif ($filterBy === 'estado') {
            $filename .= '_entidad_'.Str::slug($entidad);
        }
        $filename .= '.xlsx';

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($table === 'companies' ? 'Empresas' : 'Contactos');

        $headers = self::EXPORT_CSV_HEADERS;
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->fromArray($headers, null, 'A1', true);
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);

        $row = 2;
        if ($table === 'companies') {
            $q = Company::query()->with('assignedExecutive')->orderBy('nombre_comercial');
            if ($filterBy === 'estado') {
                $q->where('estado', $entidad);
            } elseif ($filterBy === 'comercial') {
                $q->where('ejecutivo_asignado', $comercial);
            }
            foreach ($q->cursor() as $company) {
                $sheet->fromArray(self::exportCsvRowFromCompany($company), null, "A{$row}", true);
                $row++;
            }
        } else {
            $q = Contact::query()
                ->with(['company.assignedExecutive', 'assignedExecutive'])
                ->orderBy('nombre_completo');
            if ($filterBy === 'estado') {
                $q->where('estado', $entidad);
            } elseif ($filterBy === 'comercial') {
                $q->where(function ($w) use ($comercial): void {
                    $w->whereHas('company', function ($c) use ($comercial): void {
                        $c->where('ejecutivo_asignado', $comercial);
                    })->orWhereHas('assignedExecutive', function ($u) use ($comercial): void {
                        $u->where('name', $comercial);
                    });
                });
            }
            foreach ($q->cursor() as $contact) {
                $sheet->fromArray(self::exportCsvRowFromContact($contact), null, "A{$row}", true);
                $row++;
            }
        }

        foreach (range(1, count($headers)) as $colIdx) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIdx))->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return list<string|null>
     */
    private static function exportCsvRowFromCompany(Company $c): array
    {
        $comercial = $c->assignedExecutive?->name;
        if (! is_string($comercial) || trim($comercial) === '') {
            $comercial = $c->ejecutivo_asignado;
        }
        $comercial = is_string($comercial) ? trim($comercial) : '';

        return [
            '',
            (string) ($c->nombre_comercial ?? ''),
            '',
            (string) ($c->telefono ?? ''),
            (string) ($c->celular ?? ''),
            '',
            '',
            '',
            (string) ($c->municipio ?? ''),
            (string) ($c->estado ?? ''),
            $comercial,
            (string) ($c->sector ?? ''),
            '',
            trim((string) ($c->datos_fiscales ?? '')),
            '',
        ];
    }

    /**
     * @return list<string|null>
     */
    private static function exportCsvRowFromContact(Contact $contact): array
    {
        $co = $contact->company;
        $nombreEmpresa = $co !== null ? (string) ($co->nombre_comercial ?? '') : '';

        $labelComercial = $contact->comercialEjecutivoLabel();
        $comercialCol = $labelComercial === '—' ? '' : $labelComercial;

        $ciudad = trim((string) ($contact->municipio ?? ''));
        if ($ciudad === '' && $co !== null) {
            $ciudad = (string) ($co->municipio ?? '');
        }

        $estado = trim((string) ($contact->estado ?? ''));
        if ($estado === '' && $co !== null) {
            $estado = (string) ($co->estado ?? '');
        }

        $giro = $co !== null ? (string) ($co->sector ?? '') : '';

        $domicilio = trim(implode(', ', array_filter([
            $contact->calle_numero,
            $contact->colonia_cp,
        ], fn ($v) => is_string($v) && trim($v) !== '')));

        return [
            (string) ($contact->genero ?? ''),
            $nombreEmpresa,
            (string) ($contact->nombre_completo ?? ''),
            (string) ($contact->telefono ?? ''),
            (string) ($contact->celular ?? ''),
            (string) ($contact->email ?? ''),
            (string) ($contact->departamento ?? ''),
            (string) ($contact->puesto_de_trabajo ?? ''),
            $ciudad,
            $estado,
            $comercialCol,
            $giro,
            (string) ($contact->notas ?? ''),
            $domicilio,
            '',
        ];
    }

    /**
     * Banner de sección en columnas A–K (fusionadas).
     */
    private function applyNestedSectionBanner($sheet, int $row, string $label, string $rgbHex): int
    {
        $sheet->mergeCells("A{$row}:K{$row}");
        $sheet->setCellValue("A{$row}", $label);
        $style = $sheet->getStyle("A{$row}:K{$row}");
        $style->getFont()->setBold(true);
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $style->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRgb($rgbHex);

        return $row + 1;
    }

    /**
     * Fila de empresa: nombre en B; ciudad y estado en I y J; comercial en K.
     *
     * @return list<string>
     */
    private static function nestedLayoutCompanyRow(Company $c): array
    {
        $comercial = $c->assignedExecutive?->name;
        if (! is_string($comercial) || trim($comercial) === '') {
            $comercial = $c->ejecutivo_asignado;
        }
        $comercial = is_string($comercial) ? trim($comercial) : '';

        return [
            '',
            (string) ($c->nombre_comercial ?? ''),
            '',
            '',
            '',
            '',
            '',
            '',
            (string) ($c->municipio ?? ''),
            (string) ($c->estado ?? ''),
            $comercial,
        ];
    }

    /**
     * Fila de contacto: datos en C–H; B, I y J vacíos; comercial en K (misma lógica que el CRM).
     *
     * @return list<string>
     */
    private static function nestedLayoutContactRow(Contact $contact): array
    {
        $labelComercial = $contact->comercialEjecutivoLabel();
        $comercialCol = $labelComercial === '—' ? '' : $labelComercial;

        return [
            '',
            '',
            (string) ($contact->nombre_completo ?? ''),
            (string) ($contact->telefono ?? ''),
            (string) ($contact->celular ?? ''),
            (string) ($contact->email ?? ''),
            (string) ($contact->departamento ?? ''),
            (string) ($contact->puesto_de_trabajo ?? ''),
            '',
            '',
            $comercialCol,
        ];
    }

    /**
     * Excel anidado: un solo encabezado; por empresa fila «EMPRESA», fila de datos y filas de contacto (sin repetir títulos ni bloque CONTACTOS).
     */
    private function exportCompaniesWithContactsExcel(string $filterBy, ?string $entidad, ?string $comercial): StreamedResponse
    {
        $q = Company::query()
            ->with([
                'contacts' => fn ($cq) => $cq->with('assignedExecutive')->orderBy('nombre_completo'),
                'assignedExecutive',
            ])
            ->orderBy('nombre_comercial');

        if ($filterBy === 'estado') {
            $q->where('estado', $entidad);
        } elseif ($filterBy === 'comercial') {
            $q->where('ejecutivo_asignado', $comercial);
        }

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Empresas y contactos');

        $row = 1;
        $lastCol = 'K';
        $nestHeaders = array_merge([''], self::EXPORT_NESTED_BLOCK_HEADERS);

        $sheet->fromArray($nestHeaders, null, "A{$row}", true);
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFont()->setBold(true);
        $row++;

        foreach ($q->lazy() as $company) {
            $row = $this->applyNestedSectionBanner($sheet, $row, 'EMPRESA', 'FFE599');
            $sheet->fromArray(self::nestedLayoutCompanyRow($company), null, "A{$row}", true);
            $row++;

            foreach ($company->contacts as $contact) {
                $contact->setRelation('company', $company);
                $sheet->fromArray(self::nestedLayoutContactRow($contact), null, "A{$row}", true);
                $row++;
            }

            $row++;
        }

        foreach (range(1, 11) as $colIdx) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIdx))->setAutoSize(true);
        }

        $filename = 'empresas_con_contactos';
        if ($filterBy === 'comercial') {
            $filename .= '_ejecutivo_'.Str::slug($comercial);
        } elseif ($filterBy === 'estado') {
            $filename .= '_estado_'.Str::slug($entidad);
        }
        $filename .= '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
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
