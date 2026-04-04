<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesAdminUserView;
use App\Http\Controllers\Concerns\ResolvesExecutiveAssignment;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Sale;
use App\Models\User;
use App\Services\SpreadsheetProspectStatusResolver;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controlador de Empresas
 * 
 * Gestiona CRUD de empresas con validación de duplicados,
 * sistema de aprobación y carga masiva vía Excel
 */
class CompanyController extends Controller
{
    use ResolvesAdminUserView;
    use ResolvesExecutiveAssignment;
    /**
     * Mostrar listado del recurso.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Company::class);

        $query = Company::with(['creator', 'approver', 'contacts']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre_comercial', 'like', "%{$search}%");
        }

        $user = auth()->user();
        $isAdmin = $user->esAdmin();

        if (! $isAdmin) {
            $query->accessibleForExecutive($user);
        }

        $companies = $query->latest()->paginate(15);

        $companyContactsCard = null;
        if ($companies->count() === 1) {
            $single = $companies->first();
            if ($isAdmin) {
                // Misma empresa de la página: ya trae todos los contactos (eager load).
                $companyContactsCard = $single;
            } else {
                // Usuario: contactos de su cartera en esa empresa y seguimientos visibles.
                $companyContactsCard = Company::query()
                    ->with(['creator', 'approver'])
                    ->whereKey($single->id)
                    ->first();

                if ($companyContactsCard) {
                    $companyContactsCard->load([
                        'contacts' => function ($q) use ($user) {
                            $q->where(function ($q2) use ($user) {
                                $q2->where('assigned_user_id', $user->id)
                                    ->orWhere('created_by', $user->id);
                            });
                        },
                    ]);
                    $companyContactsCard->contacts->load([
                        'followUps' => function ($q) use ($user) {
                            $q->where(function ($q2) use ($user) {
                                $q2->where('created_by', $user->id)
                                    ->orWhere('asignado_a', $user->id);
                            })->orderByDesc('fecha_alarma');
                        },
                    ]);
                }
            }
        }

        $companyNames = collect();
        if ($isAdmin) {
            $companyNames = Company::orderBy('nombre_comercial')
                ->pluck('nombre_comercial')
                ->unique();
        }

        if (!$isAdmin) {
            $misPendientes = Company::where('created_by', $user->id)->pendientes()->count();
            $misEliminacionesPendientes = Company::where('created_by', $user->id)->where('deletion_pending', true)->count();

            return view('user.companies.index', [
                'companies' => $companies,
                'misPendientes' => $misPendientes,
                'misEliminacionesPendientes' => $misEliminacionesPendientes,
                'companyContactsCard' => $companyContactsCard,
                'empresasPorEstado' => Company::countsByEstadoForUser($user),
            ]);
        }

        return view('companies.index', [
            'companies' => $companies,
            'companyContactsCard' => $companyContactsCard,
            'companyNames' => $companyNames,
            'empresasPorEstado' => Company::countsByEstadoForUser($user),
        ]);
    }

    /**
     * Mostrar formulario para crear un nuevo recurso.
     */
    public function create()
    {
        $this->authorize('create', Company::class);

        return $this->resolveView('companies.create', 'user.companies.create', $this->companyExecutiveFormContext(null));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Los ejecutivos crean en 'pendiente'; administradores (o quien apruebe) quedan 'aprobado' al instante.
     */
    public function store(StoreCompanyRequest $request)
    {
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $approvalStatus = ($user->esAdmin() || $user->can('companies.approve')) ? 'aprobado' : 'pendiente';

            $assignment = $this->resolveCompanyExecutiveForSave($request, $user, false, null);

            $company = Company::create([
                'nombre_comercial' => $request->nombre_comercial,
                'rfc' => $request->filled('rfc') ? strtoupper($request->rfc) : null,
                'sector' => is_array($request->sector) ? implode(', ', $request->sector) : $request->sector,
                'municipio' => $request->municipio,
                'estado' => $request->estado,
                'telefono' => $request->filled('telefono') ? trim((string) $request->telefono) : null,
                'celular' => $request->filled('celular') ? trim((string) $request->celular) : null,
                'ejecutivo_asignado' => $assignment['ejecutivo_asignado'],
                'assigned_user_id' => $assignment['assigned_user_id'],
                'datos_fiscales' => $request->datos_fiscales,
                'status_color' => $request->status_color ?? 'seguimiento',
                'approval_status' => $approvalStatus,
                'created_by' => $user->id,
                'approved_by' => $approvalStatus === 'aprobado' ? $user->id : null,
                'approved_at' => $approvalStatus === 'aprobado' ? now() : null,
            ]);

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $approvalStatus === 'aprobado'
                        ? 'La empresa se ha registrado correctamente.'
                        : 'Empresa creada. Pendiente de aprobación por un administrador.',
                    'company_id' => $company->id,
                ]);
            }

            $redirect = redirect()->route('companies.show', $company)
                ->with('success', $approvalStatus === 'aprobado'
                    ? 'Empresa creada exitosamente.'
                    : 'Empresa registrada correctamente.');

            if ($approvalStatus !== 'aprobado' && ! $user->esAdmin()) {
                $redirect->with('warning', 'Aviso: esta empresa no será visible para el resto del equipo hasta que un administrador la apruebe.');
            }

            return $redirect;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear empresa: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la empresa. Por favor, intente nuevamente.',
                    'errors' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()
                ->with('error', 'Error al crear la empresa. Por favor, intente nuevamente.');
        }
    }

    /**
     * Mostrar el recurso especificado.
     */
    public function show(Company $company)
    {
        $this->authorize('view', $company);

        $user = auth()->user();

        if ($user->esAdmin()) {
            $company->load(['contacts', 'followUps.asignado', 'sales.creator', 'creator', 'approver']);
        } else {
            $company->load(['creator', 'approver']);
            $company->load([
                'contacts' => function ($q) use ($user) {
                    $q->accessibleForExecutive($user)->orderBy('nombre_completo');
                },
            ]);
            $company->contacts->load([
                'followUps' => function ($q) use ($user) {
                    $q->where(function ($q2) use ($user) {
                        $q2->where('created_by', $user->id)
                            ->orWhere('asignado_a', $user->id);
                    })->orderByDesc('fecha_alarma');
                },
            ]);
            $company->load([
                'followUps' => function ($q) use ($user) {
                    $q->where(function ($q2) use ($user) {
                        $q2->where('created_by', $user->id)
                            ->orWhere('asignado_a', $user->id);
                    })->with('asignado')->orderByDesc('fecha_alarma');
                },
                'sales' => function ($q) use ($user) {
                    $q->where('created_by', $user->id)->with('creator')->latest('fecha_venta');
                },
            ]);
        }

        return $this->resolveView('companies.show', 'user.companies.show', compact('company'));
    }

    /**
     * Mostrar formulario para editar el recurso especificado.
     */
    public function edit(Company $company)
    {
        $this->authorize('update', $company);

        $company->loadMissing('assignedExecutive');

        return $this->resolveView('companies.edit', 'user.companies.edit', array_merge(
            compact('company'),
            $this->companyExecutiveFormContext($company)
        ));
    }

    /**
     * Importar empresas y contactos desde un archivo Excel.
     *
     * Fila de empresa: área de trabajo EMPRESA o ESCUELA (o celdas fusionadas heredadas de esas filas).
     * Fila de contacto: cualquier otra área; obligatorio nombre completo y nombre de empresa (si falta alguno → rechazo).
     * Puesto y demás campos son opcionales. Si no existe empresa previa para ese nombre, se crea al importar el contacto.
     * Celdas vacías no sobrescriben datos ya guardados (solo se aplican valores presentes en el Excel).
     * Duplicado de contacto: mismo email/teléfono/celular/nombre en la empresa según departamento/puesto.
     * Las empresas explícitas en archivo se procesan primero; luego contactos.
     *
     * Estado de prospecto: columna de texto o color de relleno en la fila (.xlsx/.xls); CSV no trae formato.
     */
    public function import(Request $request)
    {
        $this->authorize('create', Company::class);

        set_time_limit(0);

        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'assign_to_user_id' => 'nullable|integer|exists:users,id',
        ]);

        $user = auth()->user();

        $assignToExecutive = null;
        if (! empty($validated['assign_to_user_id'])) {
            $assignToExecutive = \App\Models\User::find($validated['assign_to_user_id']);
            if ($assignToExecutive && $assignToExecutive->esAdmin()) {
                return back()->with('error', 'No se puede asignar la importación a un administrador.');
            }
        }

        try {
            $file = $validated['file'];
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $allRows = $sheet->toArray(null, true, true, true);

            $headerRow = $allRows[1] ?? null;
            if ($headerRow === null) {
                return back()->with('error', 'El archivo no contiene datos para importar.');
            }

            $normalizedHeaders = [];
            foreach ($headerRow as $column => $headerValue) {
                if ($headerValue === null) {
                    continue;
                }
                $normalized = Str::of($headerValue)->lower()->trim()->replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n']);
                $normalizedHeaders[(string) $normalized] = $column;
            }

            $rejectedRows = [];
            $recordRejected = function (array $p, string $motivo) use (&$rejectedRows, $headerRow): void {
                $line = [
                    'Motivo del rechazo' => $motivo,
                    'Fila en Excel' => $p['excel_row'],
                ];
                foreach ($headerRow as $col => $label) {
                    if ($label === null) {
                        continue;
                    }
                    $k = trim((string) $label);
                    if ($k === '') {
                        continue;
                    }
                    if (array_key_exists($k, $line)) {
                        continue;
                    }
                    $val = $p['row'][$col] ?? null;
                    $line[$k] = $val === null ? '' : (is_scalar($val) ? (string) $val : '');
                }
                $rejectedRows[] = $line;
            };
            $recordRejectedRaw = function (int $excelRowNum, array $row, string $motivo) use (&$rejectedRows, $headerRow): void {
                $line = [
                    'Motivo del rechazo' => $motivo,
                    'Fila en Excel' => $excelRowNum,
                ];
                foreach ($headerRow as $col => $label) {
                    if ($label === null) {
                        continue;
                    }
                    $k = trim((string) $label);
                    if ($k === '') {
                        continue;
                    }
                    if (array_key_exists($k, $line)) {
                        continue;
                    }
                    $val = $row[$col] ?? null;
                    $line[$k] = $val === null ? '' : (is_scalar($val) ? (string) $val : '');
                }
                $rejectedRows[] = $line;
            };

            // Helper para obtener el valor de una columna por nombre lógico
            $getValue = function (array $row, array $candidates) use ($normalizedHeaders) {
                foreach ($candidates as $candidate) {
                    $key = Str::of($candidate)->lower()->trim()->replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'])->toString();
                    if (isset($normalizedHeaders[$key])) {
                        $col = $normalizedHeaders[$key];

                        return $row[$col] ?? null;
                    }
                }

                return null;
            };

            $resolveHeaderColumn = function (array $candidates) use ($normalizedHeaders): ?string {
                foreach ($candidates as $candidate) {
                    $key = Str::of($candidate)->lower()->trim()->replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'])->toString();
                    if (isset($normalizedHeaders[$key])) {
                        return $normalizedHeaders[$key];
                    }
                }

                return null;
            };

            $areaDeTrabajoCol = $resolveHeaderColumn(['area de trabajo', 'área de trabajo', 'area trabajo']);
            $puestoCol = $resolveHeaderColumn(['puesto de trabajo', 'puesto']);

            $parsedRows = [];
            $ultimoAreaGlobal = null;
            $ultimoNombreEmpresaParaFilaEmpresa = null;
            foreach ($allRows as $excelRowNum => $row) {
                if ((int) $excelRowNum === 1) {
                    continue;
                }

                $isEmpty = collect($row)->filter(function ($value) {
                    return ! is_null($value) && trim((string) $value) !== '';
                })->isEmpty();

                if ($isEmpty) {
                    continue;
                }

                $companyName = trim((string) $getValue($row, [
                    'nombre de empresa',
                    'nombre empresa',
                    'nombre de la empresa',
                    'empresa',
                    'nombre',
                    'nombre comercial',
                ]));

                if ($companyName === '') {
                    $recordRejectedRaw((int) $excelRowNum, $row, 'Falta nombre de empresa.');

                    continue;
                }

                $areaTrabajoValor = '';
                if ($areaDeTrabajoCol !== null) {
                    $areaTrabajoValor = $this->getSpreadsheetCellValueResolvingMerge($sheet, $areaDeTrabajoCol, (int) $excelRowNum);
                }
                if ($areaTrabajoValor === '') {
                    $areaTrabajoValor = trim((string) ($getValue($row, ['area de trabajo', 'área de trabajo', 'area trabajo']) ?? ''));
                }

                $puestoRaw = '';
                if ($puestoCol !== null) {
                    $puestoRaw = $this->getSpreadsheetCellValueResolvingMerge($sheet, $puestoCol, (int) $excelRowNum);
                }
                if ($puestoRaw === '') {
                    $puestoRaw = trim((string) ($getValue($row, ['puesto de trabajo', 'puesto']) ?? ''));
                }

                // Celdas fusionadas en columna de área (EMPRESA/ESCUELA): solo la primera fila trae texto; el resto llega vacío (misma empresa).
                if ($areaTrabajoValor === ''
                    && $ultimoAreaGlobal !== null
                    && $this->isImportCompanyAreaValue((string) $ultimoAreaGlobal)
                    && $ultimoNombreEmpresaParaFilaEmpresa !== null
                    && strcasecmp(trim($companyName), $ultimoNombreEmpresaParaFilaEmpresa) === 0) {
                    $areaTrabajoValor = is_string($ultimoAreaGlobal) ? $ultimoAreaGlobal : 'EMPRESA';
                }

                $isEmpresaRow = $this->isImportCompanyAreaValue($areaTrabajoValor);

                if (trim($areaTrabajoValor) !== '') {
                    $ultimoAreaGlobal = $areaTrabajoValor;
                }
                if ($isEmpresaRow) {
                    $ultimoNombreEmpresaParaFilaEmpresa = trim($companyName);
                }

                $municipio = trim((string) ($getValue($row, ['ciudad', 'municipio']) ?? ''));
                $estado = trim((string) ($getValue($row, ['estado']) ?? ''));
                $sector = trim((string) ($getValue($row, ['giro', 'sector']) ?? ''));
                $ejecutivo = trim((string) ($getValue($row, ['comercial', 'ejecutivo asignado']) ?? ''));
                $domicilio = trim((string) ($getValue($row, ['domicilio', 'direccion', 'dirección']) ?? ''));
                $notasEmpresa = trim((string) ($getValue($row, ['notas empresa', 'notas']) ?? ''));

                $datosFiscales = $domicilio;
                if ($notasEmpresa !== '') {
                    $datosFiscales = $datosFiscales !== ''
                        ? $datosFiscales.' | Notas: '.$notasEmpresa
                        : 'Notas: '.$notasEmpresa;
                }

                $parsedRows[] = [
                    'excel_row' => (int) $excelRowNum,
                    'row' => $row,
                    'company_name' => $companyName,
                    'area_trabajo' => $areaTrabajoValor,
                    'puesto' => $puestoRaw,
                    'is_empresa' => $isEmpresaRow,
                    'municipio' => $municipio,
                    'estado' => $estado,
                    'sector' => $sector,
                    'ejecutivo' => $ejecutivo,
                    'datos_fiscales' => $datosFiscales,
                ];
            }

            // Propagar área y puesto por nombre de empresa (filas con celdas fusionadas suelen llegar vacías en toArray).
            $lastAreaPorEmpresa = [];
            $lastPuestoPorEmpresa = [];
            foreach ($parsedRows as $idx => $p) {
                $nombreEmp = $p['company_name'];
                if ($p['is_empresa']) {
                    continue;
                }
                $a = trim((string) ($p['area_trabajo'] ?? ''));
                $pu = trim((string) ($p['puesto'] ?? ''));
                if ($a === '' && isset($lastAreaPorEmpresa[$nombreEmp])) {
                    $parsedRows[$idx]['area_trabajo'] = $lastAreaPorEmpresa[$nombreEmp];
                    $a = trim((string) $parsedRows[$idx]['area_trabajo']);
                }
                if ($pu === '' && isset($lastPuestoPorEmpresa[$nombreEmp])) {
                    $parsedRows[$idx]['puesto'] = $lastPuestoPorEmpresa[$nombreEmp];
                    $pu = trim((string) $parsedRows[$idx]['puesto']);
                }
                if ($a !== '' && ! $this->isImportCompanyAreaValue($a)) {
                    $lastAreaPorEmpresa[$nombreEmp] = $a;
                }
                if ($pu !== '') {
                    $lastPuestoPorEmpresa[$nombreEmp] = $pu;
                }
            }

            $createdCompanies = 0;
            $updatedCompanies = 0;
            $createdContacts = 0;
            $updatedContacts = 0;
            $approvalStatus = ($user->esAdmin() || $user->can('companies.approve')) ? 'aprobado' : 'pendiente';

            /** @var array<int, array{name: string, canonical: string, company: Company}> $empresaRegistry */
            $empresaRegistry = [];

            DB::beginTransaction();

            // --- Paso 1: solo filas EMPRESA (crear/actualizar y registrar para vincular contactos) ---
            foreach ($parsedRows as $p) {
                if (! $p['is_empresa']) {
                    continue;
                }

                $importStatus = SpreadsheetProspectStatusResolver::resolve(
                    $sheet,
                    (int) $p['excel_row'],
                    $p['row'],
                    $normalizedHeaders,
                    $getValue
                );

                $companyName = $p['company_name'];
                $municipio = $p['municipio'];
                $estado = $p['estado'];
                $sector = $p['sector'];
                $ejecutivo = $p['ejecutivo'];
                $datosFiscales = $p['datos_fiscales'];

                $companyCandidates = Company::withTrashed()->where('nombre_comercial', $companyName)->get();
                $company = null;
                if ($companyCandidates->isNotEmpty()) {
                    $company = $companyCandidates->firstWhere('estado', $estado) ?? $companyCandidates->first();
                }

                if ($company) {
                    if (method_exists($company, 'trashed') && $company->trashed()) {
                        $company->restore();
                    }

                    $statusAnterior = $company->status_color;
                    $updates = [];
                    if ($sector !== '') {
                        $updates['sector'] = $sector;
                    }
                    if ($municipio !== '') {
                        $updates['municipio'] = $municipio;
                    }
                    if ($estado !== '') {
                        $updates['estado'] = $estado;
                    }
                    if ($ejecutivo !== '') {
                        $updates['ejecutivo_asignado'] = $ejecutivo;
                    }
                    if ($datosFiscales !== '') {
                        $updates['datos_fiscales'] = $datosFiscales;
                    }
                    if ($importStatus !== null) {
                        $updates['status_color'] = $importStatus;
                    }
                    if ($assignToExecutive) {
                        $updates['assigned_user_id'] = $assignToExecutive->id;
                        $updates['ejecutivo_asignado'] = $assignToExecutive->name;
                    }
                    if ($updates !== []) {
                        $company->fill($updates);
                        if ($company->isDirty()) {
                            $company->save();
                            $updatedCompanies++;
                            if ($company->status_color === 'vendido' && $statusAnterior !== 'vendido') {
                                Sale::create([
                                    'company_id' => $company->id,
                                    'nombre_servicio' => 'Venta registrada desde prospecto',
                                    'fecha_venta' => now(),
                                    'monto' => null,
                                    'tipo_pago' => null,
                                    'participantes' => null,
                                    'notas' => 'Registrado automáticamente al importar estado de prospecto Vendido.',
                                    'created_by' => auth()->id(),
                                ]);
                            }
                        }
                    }
                } else {
                    $statusColor = $importStatus ?? 'seguimiento';
                    $company = Company::create([
                        'nombre_comercial' => $companyName,
                        'rfc' => null,
                        'sector' => $sector !== '' ? $sector : null,
                        'municipio' => $municipio !== '' ? $municipio : null,
                        'estado' => $estado !== '' ? $estado : null,
                        'ejecutivo_asignado' => $assignToExecutive ? $assignToExecutive->name : ($ejecutivo !== '' ? $ejecutivo : null),
                        'assigned_user_id' => $assignToExecutive?->id,
                        'datos_fiscales' => $datosFiscales !== '' ? $datosFiscales : null,
                        'status_color' => $statusColor,
                        'approval_status' => $approvalStatus,
                        'created_by' => $assignToExecutive ? $assignToExecutive->id : $user->id,
                        'approved_by' => $approvalStatus === 'aprobado' ? $user->id : null,
                        'approved_at' => $approvalStatus === 'aprobado' ? now() : null,
                    ]);
                    $createdCompanies++;
                    if ($statusColor === 'vendido') {
                        Sale::create([
                            'company_id' => $company->id,
                            'nombre_servicio' => 'Venta registrada desde prospecto',
                            'fecha_venta' => now(),
                            'monto' => null,
                            'tipo_pago' => null,
                            'participantes' => null,
                            'notas' => 'Registrado automáticamente al importar estado de prospecto Vendido.',
                            'created_by' => auth()->id(),
                        ]);
                    }
                }

                $empresaRegistry[] = [
                    'name' => $companyName,
                    'canonical' => $this->canonicalCompanyNameForImport($companyName),
                    'company' => $company->fresh(),
                ];
            }

            // --- Paso 2: filas de contacto ---
            foreach ($parsedRows as $p) {
                if ($p['is_empresa']) {
                    continue;
                }

                $importStatus = SpreadsheetProspectStatusResolver::resolve(
                    $sheet,
                    (int) $p['excel_row'],
                    $p['row'],
                    $normalizedHeaders,
                    $getValue
                );

                $row = $p['row'];
                $companyName = $p['company_name'];
                $areaTrabajoValor = $p['area_trabajo'];
                $municipio = $p['municipio'];
                $estado = $p['estado'];

                $contactName = trim((string) ($getValue($row, ['nombre contacto', 'nombre del contacto', 'nombre completo']) ?? ''));
                if ($contactName === '') {
                    $recordRejected($p, 'Fila de contacto sin nombre completo.');

                    continue;
                }

                $company = $this->resolveCompanyForImportedContact($companyName, $p['estado'], $empresaRegistry);
                if (! $company) {
                    $company = $this->createCompanyFromImportParsedRow(
                        $p,
                        $user,
                        $assignToExecutive,
                        $approvalStatus,
                        $importStatus
                    );
                    $createdCompanies++;
                }

                $puesto = trim((string) ($p['puesto'] ?? ''));
                if ($puesto === '') {
                    $puesto = trim((string) ($getValue($row, ['puesto de trabajo', 'puesto']) ?? ''));
                }
                $departamento = $areaTrabajoValor !== ''
                    ? $areaTrabajoValor
                    : trim((string) ($getValue($row, ['departamento']) ?? ''));
                $telefono = trim((string) ($getValue($row, ['telefono', 'teléfono']) ?? ''));
                $celular = trim((string) ($getValue($row, ['celular', 'movil', 'móvil']) ?? ''));
                $email = trim((string) ($getValue($row, ['email', 'correo', 'correo electronico', 'correo electrónico']) ?? ''));
                $notasContacto = trim((string) ($getValue($row, ['notas contacto', 'notas']) ?? ''));
                $genero = trim((string) ($getValue($row, ['genero', 'género']) ?? ''));
                $noDeseaCorreos = trim((string) ($getValue($row, ['no desea recibir correos']) ?? ''));

                $contactQuery = Contact::withTrashed()->where('company_id', $company->id);
                if ($email !== '') {
                    $contactQuery->where('email', $email);
                } elseif ($celular !== '') {
                    $contactQuery->where('celular', $celular);
                } elseif ($telefono !== '') {
                    $contactQuery->where('telefono', $telefono);
                } else {
                    $contactQuery->where('nombre_completo', $contactName);
                }

                $this->applyImportContactDuplicateRoleFilters($contactQuery, $departamento, $puesto);

                $contact = $contactQuery->first();

                if ($email !== '' && Contact::where('email', $email)
                    ->when($contact, fn ($q) => $q->where('id', '!=', $contact->id))
                    ->where('company_id', '!=', $company->id)
                    ->exists()) {
                    $email = '';
                }

                if ($contact) {
                    if (method_exists($contact, 'trashed') && $contact->trashed()) {
                        $contact->restore();
                    }

                    $contactData = ['nombre_completo' => $contactName];
                    if ($genero !== '') {
                        $contactData['genero'] = $genero;
                    }
                    if ($puesto !== '') {
                        $contactData['puesto_de_trabajo'] = $puesto;
                    }
                    if ($departamento !== '') {
                        $contactData['departamento'] = $departamento;
                    }
                    if ($celular !== '') {
                        $contactData['celular'] = $celular;
                    }
                    if ($telefono !== '') {
                        $contactData['telefono'] = $telefono;
                    }
                    if ($email !== '') {
                        $contactData['email'] = $email;
                    }
                    if ($municipio !== '') {
                        $contactData['municipio'] = $municipio;
                    }
                    if ($estado !== '') {
                        $contactData['estado'] = $estado;
                    }
                    if ($notasContacto !== '') {
                        $contactData['notas'] = $notasContacto;
                    }
                    if ($noDeseaCorreos !== '') {
                        $value = Str::lower($noDeseaCorreos);
                        $contactData['email_activo'] = ! in_array($value, ['si', 'sí', 'yes', '1', 'true'], true);
                    }
                    if ($importStatus !== null) {
                        $contactData['status_color'] = $importStatus;
                    }
                    if ($assignToExecutive) {
                        $contactData['assigned_user_id'] = $assignToExecutive->id;
                    }

                    $contact->fill($contactData);
                    if ($contact->isDirty()) {
                        $contact->save();
                        $updatedContacts++;
                    }
                } else {
                    $contactApprovalStatus = ($user->esAdmin() || $user->can('contacts.approve')) ? 'aprobado' : 'pendiente';

                    $contactData = [
                        'company_id' => $company->id,
                        'nombre_completo' => $contactName,
                        'email_activo' => true,
                        'status_color' => $importStatus ?? 'seguimiento',
                        'approval_status' => $contactApprovalStatus,
                        'approved_by' => $contactApprovalStatus === 'aprobado' ? $user->id : null,
                        'approved_at' => $contactApprovalStatus === 'aprobado' ? now() : null,
                        'created_by' => $assignToExecutive ? $assignToExecutive->id : $user->id,
                        'assigned_user_id' => $assignToExecutive?->id,
                    ];
                    if ($genero !== '') {
                        $contactData['genero'] = $genero;
                    }
                    if ($puesto !== '') {
                        $contactData['puesto_de_trabajo'] = $puesto;
                    }
                    if ($departamento !== '') {
                        $contactData['departamento'] = $departamento;
                    }
                    if ($celular !== '') {
                        $contactData['celular'] = $celular;
                    }
                    if ($telefono !== '') {
                        $contactData['telefono'] = $telefono;
                    }
                    if ($email !== '') {
                        $contactData['email'] = $email;
                    }
                    if ($municipio !== '') {
                        $contactData['municipio'] = $municipio;
                    }
                    if ($estado !== '') {
                        $contactData['estado'] = $estado;
                    }
                    if ($notasContacto !== '') {
                        $contactData['notas'] = $notasContacto;
                    }
                    if ($noDeseaCorreos !== '') {
                        $value = Str::lower($noDeseaCorreos);
                        $contactData['email_activo'] = ! in_array($value, ['si', 'sí', 'yes', '1', 'true'], true);
                    }

                    Contact::create($contactData);
                    $createdContacts++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error al importar empresas y contactos desde Excel: '.$e->getMessage(), [
                'exception' => $e::class,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Ocurrió un error al procesar el archivo. Verifica el formato y vuelve a intentarlo.');
        }

        // Tras commit, los datos ya están guardados: fallos en caché/sesión no deben mostrarse como "importación fallida".
        $empresasTotal = $createdCompanies + $updatedCompanies;
        $contactosTotal = $createdContacts + $updatedContacts;
        $message = "Se importaron {$empresasTotal} empresas y {$contactosTotal} contactos.";

        $rejectedToken = null;
        if ($rejectedRows !== []) {
            try {
                $rejectedToken = (string) Str::uuid();
                $relativePath = $this->importRejectedStoragePath(auth()->id(), $rejectedToken);
                Storage::disk('local')->put(
                    $relativePath,
                    json_encode($rejectedRows, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE)
                );
            } catch (\Throwable $e) {
                Log::warning('Importación guardada en base de datos; no se pudo guardar el listado de rechazados en disco.', [
                    'message' => $e->getMessage(),
                    'exception' => $e::class,
                ]);
            }
        }

        return back()->with('import_flash', [
            'message' => $message,
            'rejected_token' => $rejectedToken,
        ]);
    }

    /**
     * Descarga un Excel con las filas que no se importaron (motivo + datos originales).
     * El token se genera al finalizar una importación y caduca en breve.
     */
    public function downloadImportRejected(Request $request): StreamedResponse|RedirectResponse
    {
        $this->authorize('create', Company::class);

        $validated = $request->validate([
            'token' => 'required|uuid',
        ]);

        $relativePath = $this->importRejectedStoragePath(auth()->id(), $validated['token']);
        if (! Storage::disk('local')->exists($relativePath)) {
            return redirect()->back()->with('error', 'El listado de registros rechazados ya no está disponible o expiró. Importe de nuevo el archivo si lo necesita.');
        }

        try {
            $raw = Storage::disk('local')->get($relativePath);
            $rows = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            Log::warning('No se pudo leer archivo de rechazados de importación.', ['message' => $e->getMessage()]);

            return redirect()->back()->with('error', 'No se pudo leer el listado de rechazados. Importe de nuevo el archivo si lo necesita.');
        }

        if (! is_array($rows) || $rows === []) {
            Storage::disk('local')->delete($relativePath);

            return redirect()->back()->with('error', 'El listado de registros rechazados estaba vacío o corrupto.');
        }

        Storage::disk('local')->delete($relativePath);

        $spreadsheet = $this->buildSpreadsheetFromImportRejectedRows($rows);
        $writer = new Xlsx($spreadsheet);
        $filename = 'registros-rechazados-importacion-'.now()->format('Y-m-d-His').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Ruta relativa al disco `local` (storage/app/…) para el JSON de rechazados de una importación.
     */
    private function importRejectedStoragePath(int|string $userId, string $token): string
    {
        return 'import-rejected/'.$userId.'/'.$token.'.json';
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function buildSpreadsheetFromImportRejectedRows(array $rows): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        if ($rows === []) {
            $sheet->setCellValue('A1', 'Sin registros rechazados');

            return $spreadsheet;
        }

        $headers = array_keys($rows[0]);
        $table = [$headers];
        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $h) {
                $v = $row[$h] ?? '';
                $line[] = is_scalar($v) ? (string) $v : '';
            }
            $table[] = $line;
        }
        $sheet->fromArray($table, null, 'A1');

        return $spreadsheet;
    }

    /**
     * Devolver solo el formulario de edición (para modal en listado).
     */
    public function editForm(Company $company)
    {
        $this->authorize('update', $company);

        $company->loadMissing('assignedExecutive');

        return view('companies.partials.edit-form', array_merge(
            compact('company'),
            $this->companyExecutiveFormContext($company)
        ));
    }

    /**
     * Actualizar el recurso especificado en el almacenamiento.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        DB::beginTransaction();
        try {
            $statusAnterior = $company->status_color;
            $nuevoStatus = $request->status_color ?? $company->status_color;

            $assignment = $this->resolveCompanyExecutiveForSave($request, $request->user(), true, $company);

            $company->update([
                'nombre_comercial' => $request->nombre_comercial,
                'rfc' => $request->filled('rfc') ? strtoupper($request->rfc) : null,
                'sector' => is_array($request->sector) ? implode(', ', $request->sector) : $request->sector,
                'municipio' => $request->municipio,
                'estado' => $request->estado,
                'telefono' => $request->filled('telefono') ? trim((string) $request->telefono) : null,
                'celular' => $request->filled('celular') ? trim((string) $request->celular) : null,
                'ejecutivo_asignado' => $assignment['ejecutivo_asignado'],
                'assigned_user_id' => $assignment['assigned_user_id'],
                'datos_fiscales' => $request->datos_fiscales,
                'status_color' => $nuevoStatus,
            ]);

            if ($nuevoStatus === 'vendido' && $statusAnterior !== 'vendido') {
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

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Empresa actualizada exitosamente.',
                ]);
            }

            return redirect()->to(\App\Support\CrmNavigation::redirectTargetFromRequest($request, route('companies.show', $company)))
                ->with('success', 'Empresa actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar empresa: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la empresa. Por favor, intente nuevamente.',
                    'errors' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()
                ->with('error', 'Error al actualizar la empresa. Por favor, intente nuevamente.');
        }
    }

    /**
     * Solicitud de eliminación (usuario sin permiso companies.delete).
     */
    public function requestDeletion(Company $company)
    {
        $this->authorize('requestDeletion', $company);

        $company->update([
            'deletion_pending' => true,
            'deletion_requested_by' => auth()->id(),
            'deletion_requested_at' => now(),
            'deletion_resolution' => null,
            'deletion_resolution_note' => null,
            'deletion_resolved_at' => null,
            'deletion_resolved_by' => null,
            'deletion_decision_user_id' => null,
        ]);

        return redirect()->route('companies.index')
            ->with('success', 'Solicitud de eliminación enviada. Un administrador la revisará en Solicitudes pendientes.');
    }

    /**
     * Eliminar el recurso especificado del almacenamiento.
     * Solo quien tiene permiso de borrado directo (p. ej. administradores).
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

    /**
     * Área de trabajo que identifica una fila como empresa en la importación (cabecera de razón social).
     */
    private function isImportCompanyAreaValue(string $area): bool
    {
        $n = Str::lower(trim($area));

        return $n === 'empresa' || $n === 'escuela';
    }

    /**
     * Alta mínima de empresa cuando un contacto trae nombre de empresa sin fila EMPRESA/ESCUELA previa en el archivo.
     *
     * @param  array<string, mixed>  $p
     */
    private function createCompanyFromImportParsedRow(
        array $p,
        User $user,
        ?User $assignToExecutive,
        string $approvalStatus,
        ?string $importStatus
    ): Company {
        $companyName = $p['company_name'];
        $municipio = $p['municipio'];
        $estado = $p['estado'];
        $sector = $p['sector'];
        $ejecutivo = $p['ejecutivo'];
        $datosFiscales = $p['datos_fiscales'];
        $statusColor = $importStatus ?? 'seguimiento';
        $company = Company::create([
            'nombre_comercial' => $companyName,
            'rfc' => null,
            'sector' => $sector !== '' ? $sector : null,
            'municipio' => $municipio !== '' ? $municipio : null,
            'estado' => $estado !== '' ? $estado : null,
            'ejecutivo_asignado' => $assignToExecutive ? $assignToExecutive->name : ($ejecutivo !== '' ? $ejecutivo : null),
            'assigned_user_id' => $assignToExecutive?->id,
            'datos_fiscales' => $datosFiscales !== '' ? $datosFiscales : null,
            'status_color' => $statusColor,
            'approval_status' => $approvalStatus,
            'created_by' => $assignToExecutive ? $assignToExecutive->id : $user->id,
            'approved_by' => $approvalStatus === 'aprobado' ? $user->id : null,
            'approved_at' => $approvalStatus === 'aprobado' ? now() : null,
        ]);
        if ($statusColor === 'vendido') {
            Sale::create([
                'company_id' => $company->id,
                'nombre_servicio' => 'Venta registrada desde prospecto',
                'fecha_venta' => now(),
                'monto' => null,
                'tipo_pago' => null,
                'participantes' => null,
                'notas' => 'Registrado automáticamente al importar estado de prospecto Vendido.',
                'created_by' => auth()->id(),
            ]);
        }

        return $company;
    }

    /**
     * Obtiene el valor visible de una celda; si está en un rango combinado y no es la celda maestra,
     * devuelve el valor de la esquina superior izquierda (donde Excel guarda el texto).
     */
    private function getSpreadsheetCellValueResolvingMerge(Worksheet $sheet, string $columnLetter, int $rowNumber): string
    {
        $coordinate = $columnLetter.$rowNumber;
        $cell = $sheet->getCell($coordinate);
        $raw = $cell->getValue();
        $hasValue = $raw !== null && (! is_string($raw) || trim($raw) !== '');

        if ($hasValue) {
            $formatted = $cell->getFormattedValue();

            return trim((string) ($formatted !== '' ? $formatted : $raw));
        }

        if ($cell->isInMergeRange() && ! $cell->isMergeRangeValueCell()) {
            $mergeRange = $cell->getMergeRange();
            if (is_string($mergeRange) && $mergeRange !== '') {
                $ranges = Coordinate::splitRange($mergeRange);
                $startCell = $ranges[0][0] ?? null;
                if ($startCell !== null && $startCell !== '') {
                    $master = $sheet->getCell($startCell);
                    $mv = $master->getValue();
                    $formatted = $master->getFormattedValue();

                    return trim((string) (($formatted !== null && $formatted !== '') ? $formatted : $mv));
                }
            }
        }

        return '';
    }

    /**
     * Normaliza el nombre de empresa para comparar variantes (mayúsculas, espacios, acentos).
     */
    private function canonicalCompanyNameForImport(string $name): string
    {
        $s = Str::lower(trim(preg_replace('/\s+/u', ' ', $name)));
        $s = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $s);

        return $s;
    }

    /**
     * Resuelve la empresa de un contacto: nombre exacto en BD, coincidencia con filas EMPRESA del archivo
     * o similitud de texto (p. ej. typo entre columnas).
     *
     * @param  array<int, array{name: string, canonical: string, company: Company}>  $empresaRegistry
     */
    private function resolveCompanyForImportedContact(string $nameFromExcel, string $estadoFromRow, array $empresaRegistry): ?Company
    {
        $trimmed = trim($nameFromExcel);
        if ($trimmed === '') {
            return null;
        }

        $companyCandidates = Company::withTrashed()->where('nombre_comercial', $trimmed)->get();
        if ($companyCandidates->isNotEmpty()) {
            return $companyCandidates->firstWhere('estado', $estadoFromRow) ?? $companyCandidates->first();
        }

        foreach ($empresaRegistry as $entry) {
            if (strcasecmp($trimmed, $entry['name']) === 0) {
                return $entry['company'];
            }
        }

        $canon = $this->canonicalCompanyNameForImport($trimmed);
        foreach ($empresaRegistry as $entry) {
            if ($canon === $entry['canonical']) {
                return $entry['company'];
            }
        }

        $best = null;
        $bestPct = 0.0;
        foreach ($empresaRegistry as $entry) {
            similar_text($canon, $entry['canonical'], $pct);
            if ($pct > $bestPct) {
                $bestPct = $pct;
                $best = $entry['company'];
            }
        }
        if ($best !== null && $bestPct >= 86.0) {
            return $best;
        }

        return null;
    }

    /**
     * Restringe la búsqueda de “mismo contacto” al mismo rol: área de trabajo y puesto deben coincidir
     * (vacío/null se trata como equivalente a vacío en ambos lados).
     */
    private function applyImportContactDuplicateRoleFilters(Builder $query, string $departamento, string $puesto): void
    {
        if (trim($departamento) === '') {
            $query->where(function (Builder $q) {
                $q->whereNull('departamento')->orWhere('departamento', '');
            });
        } else {
            $query->where('departamento', $departamento);
        }

        if (trim($puesto) === '') {
            $query->where(function (Builder $q) {
                $q->whereNull('puesto_de_trabajo')->orWhere('puesto_de_trabajo', '');
            });
        } else {
            $query->where('puesto_de_trabajo', $puesto);
        }
    }
}
