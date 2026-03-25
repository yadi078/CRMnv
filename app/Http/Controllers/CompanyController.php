<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesAdminUserView;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Sale;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Controlador de Empresas
 * 
 * Gestiona CRUD de empresas con validación de duplicados,
 * sistema de aprobación y carga masiva vía Excel
 */
class CompanyController extends Controller
{
    use ResolvesAdminUserView;
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
                // Usuario: ficha con solo contactos creados por él (aprobados) y sus seguimientos visibles.
                $companyContactsCard = Company::query()
                    ->with(['creator', 'approver'])
                    ->whereKey($single->id)
                    ->first();

                if ($companyContactsCard) {
                    $companyContactsCard->load([
                        'contacts' => function ($q) use ($user) {
                            $q->where('created_by', $user->id)
                                ->where('approval_status', 'aprobado');
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
            ]);
        }

        return view('companies.index', [
            'companies' => $companies,
            'companyContactsCard' => $companyContactsCard,
            'companyNames' => $companyNames,
        ]);
    }

    /**
     * Mostrar formulario para crear un nuevo recurso.
     */
    public function create()
    {
        $this->authorize('create', Company::class);

        return $this->resolveView('companies.create', 'user.companies.create');
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
                'rfc' => $request->filled('rfc') ? strtoupper($request->rfc) : null,
                'sector' => is_array($request->sector) ? implode(', ', $request->sector) : $request->sector,
                'municipio' => $request->municipio,
                'estado' => $request->estado,
                'ejecutivo_asignado' => $request->ejecutivo_asignado,
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

            return redirect()->route('companies.show', $company)
                ->with('success', $approvalStatus === 'aprobado'
                    ? 'Empresa creada exitosamente.'
                    : 'Empresa creada. Pendiente de aprobación por un administrador.');
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

        $company->load(['contacts', 'followUps.asignado', 'sales.creator', 'creator', 'approver']);

        return $this->resolveView('companies.show', 'user.companies.show', compact('company'));
    }

    /**
     * Mostrar formulario para editar el recurso especificado.
     */
    public function edit(Company $company)
    {
        $this->authorize('update', $company);

        return $this->resolveView('companies.edit', 'user.companies.edit', compact('company'));
    }

    /**
     * Importar empresas y contactos desde un archivo Excel.
     *
     * Reglas: columna "Área de trabajo" = EMPRESA define fila de empresa; cualquier otro valor, fila de contacto.
     * Obligatorio: "Nombre de empresa" en todas las filas; en filas que no son EMPRESA, también "Nombre completo".
     * Celdas vacías no sobrescriben datos ya guardados (solo se aplican valores presentes en el Excel).
     * Duplicado de contacto: mismo email/teléfono/celular/nombre en la empresa solo si además coinciden
     * área de trabajo (departamento) y puesto; si difieren, se crea otro contacto (misma persona, distinto rol).
     * Las empresas se procesan primero para que cada contacto resuelva su company_id.
     */
    public function import(Request $request)
    {
        $this->authorize('create', Company::class);

        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $user = auth()->user();

        try {
            $file = $validated['file'];
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (count($rows) < 2) {
                return back()->with('error', 'El archivo no contiene datos para importar.');
            }

            // Primera fila: encabezados
            $headerRow = array_shift($rows);
            $normalizedHeaders = [];
            foreach ($headerRow as $column => $headerValue) {
                if ($headerValue === null) {
                    continue;
                }
                $normalized = Str::of($headerValue)->lower()->trim()->replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n']);
                $normalizedHeaders[(string) $normalized] = $column;
            }

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

            $parsedRows = [];
            foreach ($rows as $row) {
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
                    continue;
                }

                $areaTrabajoValor = trim((string) ($getValue($row, ['area de trabajo', 'área de trabajo', 'area trabajo']) ?? ''));
                $isEmpresaRow = Str::lower($areaTrabajoValor) === 'empresa';

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
                    'row' => $row,
                    'company_name' => $companyName,
                    'area_trabajo' => $areaTrabajoValor,
                    'is_empresa' => $isEmpresaRow,
                    'municipio' => $municipio,
                    'estado' => $estado,
                    'sector' => $sector,
                    'ejecutivo' => $ejecutivo,
                    'datos_fiscales' => $datosFiscales,
                ];
            }

            $createdCompanies = 0;
            $updatedCompanies = 0;
            $createdContacts = 0;
            $updatedContacts = 0;
            /** @var int Filas EMPRESA que apuntan a una empresa ya existente y no aportan campos nuevos (duplicado en el Excel o sin datos opcionales). */
            $redundantEmpresaRows = 0;
            $skippedContactsNoCompany = 0;
            $skippedContactsNoName = 0;

            $approvalStatus = $user->can('companies.approve') ? 'aprobado' : 'pendiente';

            /** @var array<int, array{name: string, canonical: string, company: Company}> $empresaRegistry */
            $empresaRegistry = [];

            DB::beginTransaction();

            // --- Paso 1: solo filas EMPRESA (crear/actualizar y registrar para vincular contactos) ---
            foreach ($parsedRows as $p) {
                if (! $p['is_empresa']) {
                    continue;
                }

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
                    if ($updates !== []) {
                        $company->fill($updates);
                        if ($company->isDirty()) {
                            $company->save();
                            $updatedCompanies++;
                        } else {
                            $redundantEmpresaRows++;
                        }
                    } else {
                        $redundantEmpresaRows++;
                    }
                } else {
                    $company = Company::create([
                        'nombre_comercial' => $companyName,
                        'rfc' => null,
                        'sector' => $sector !== '' ? $sector : null,
                        'municipio' => $municipio !== '' ? $municipio : null,
                        'estado' => $estado !== '' ? $estado : null,
                        'ejecutivo_asignado' => $ejecutivo !== '' ? $ejecutivo : null,
                        'datos_fiscales' => $datosFiscales !== '' ? $datosFiscales : null,
                        'status_color' => 'seguimiento',
                        'approval_status' => $approvalStatus,
                        'created_by' => $user->id,
                        'approved_by' => $approvalStatus === 'aprobado' ? $user->id : null,
                        'approved_at' => $approvalStatus === 'aprobado' ? now() : null,
                    ]);
                    $createdCompanies++;
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

                $row = $p['row'];
                $companyName = $p['company_name'];
                $areaTrabajoValor = $p['area_trabajo'];
                $municipio = $p['municipio'];
                $estado = $p['estado'];

                $company = $this->resolveCompanyForImportedContact($companyName, $p['estado'], $empresaRegistry);
                if (! $company) {
                    $skippedContactsNoCompany++;

                    continue;
                }

                $contactName = trim((string) ($getValue($row, ['nombre contacto', 'nombre del contacto', 'nombre completo']) ?? ''));
                if ($contactName === '') {
                    $skippedContactsNoName++;

                    continue;
                }

                $puesto = trim((string) ($getValue($row, ['puesto de trabajo', 'puesto']) ?? ''));
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

                    $contact->fill($contactData);
                    if ($contact->isDirty()) {
                        $contact->save();
                        $updatedContacts++;
                    }
                } else {
                    $contactApprovalStatus = $user->can('contacts.approve') ? 'aprobado' : 'pendiente';

                    $contactData = [
                        'company_id' => $company->id,
                        'nombre_completo' => $contactName,
                        'email_activo' => true,
                        'status_color' => 'seguimiento',
                        'approval_status' => $contactApprovalStatus,
                        'approved_by' => $contactApprovalStatus === 'aprobado' ? $user->id : null,
                        'approved_at' => $contactApprovalStatus === 'aprobado' ? now() : null,
                        'created_by' => $user->id,
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

            $message = "Importación completada. Empresas nuevas: {$createdCompanies}, empresas actualizadas: {$updatedCompanies}";
            if ($redundantEmpresaRows > 0) {
                $message .= ", filas EMPRESA duplicadas/sin cambios (mismo nombre comercial, sin datos nuevos): {$redundantEmpresaRows}";
            }
            $message .= ". Contactos nuevos: {$createdContacts}, contactos actualizados (mismo criterio de duplicado): {$updatedContacts}.";
            if ($skippedContactsNoCompany > 0) {
                $message .= " Filas de contacto sin empresa coincidente (revisa nombres y que exista una fila con Área de trabajo = EMPRESA): {$skippedContactsNoCompany}.";
            }
            if ($skippedContactsNoName > 0) {
                $message .= " Filas de contacto omitidas sin nombre completo: {$skippedContactsNoName}.";
            }

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al importar empresas y contactos desde Excel: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Ocurrió un error al procesar el archivo. Verifica el formato y vuelve a intentarlo.');
        }
    }

    /**
     * Devolver solo el formulario de edición (para modal en listado).
     */
    public function editForm(Company $company)
    {
        $this->authorize('update', $company);

        return view('companies.partials.edit-form', compact('company'));
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

            $company->update([
                'nombre_comercial' => $request->nombre_comercial,
                'rfc' => $request->filled('rfc') ? strtoupper($request->rfc) : null,
                'sector' => is_array($request->sector) ? implode(', ', $request->sector) : $request->sector,
                'municipio' => $request->municipio,
                'estado' => $request->estado,
                'ejecutivo_asignado' => $request->ejecutivo_asignado,
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

            return redirect()->route('companies.show', $company)
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
