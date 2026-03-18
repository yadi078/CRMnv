<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesAdminUserView;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Sale;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
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

        if ($request->filled('sector')) {
            $query->where('sector', $request->sector);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('ejecutivo_asignado')) {
            $query->where('ejecutivo_asignado', $request->ejecutivo_asignado);
        }

        $user = auth()->user();
        $isAdmin = $user->esAdmin();
        if ($isAdmin && $request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        if (!$isAdmin) {
            $query->aprobados();
        }

        $companies = $query->latest()->paginate(15);

        // Si el resultado del filtro devuelve solo una empresa,
        // la usaremos para mostrar una ficha con sus contactos (filtrados por estado prospecto si aplica).
        $companyContactsCard = null;
        if ($companies->count() === 1) {
            $companyContactsCard = $companies->first();
            if ($request->filled('status_color')) {
                $companyContactsCard->setRelation('contacts',
                    $companyContactsCard->contacts->where('status_color', $request->status_color)->values()
                );
            }
        }

        // Lista de nombres de empresas para autocompletar en el buscador (solo admin)
        $companyNames = collect();
        $sectorOptions = collect();
        $estadoOptions = collect();
        $ejecutivoOptions = collect();

        // Opciones para selects de filtros (admin y usuario)
        $baseQueryForOptions = Company::query();
        $sectorOptions = (clone $baseQueryForOptions)
            ->whereNotNull('sector')
            ->orderBy('sector')
            ->pluck('sector')
            ->unique()
            ->values();
        $estadoOptions = (clone $baseQueryForOptions)
            ->whereNotNull('estado')
            ->orderBy('estado')
            ->pluck('estado')
            ->unique()
            ->values();
        $ejecutivoOptions = (clone $baseQueryForOptions)
            ->whereNotNull('ejecutivo_asignado')
            ->orderBy('ejecutivo_asignado')
            ->pluck('ejecutivo_asignado')
            ->unique()
            ->values();

        if ($isAdmin) {
            $companyNames = Company::orderBy('nombre_comercial')
                ->pluck('nombre_comercial')
                ->unique();
        }

        // Usuario normal (rol usuario o no admin): vista limitada operativa
        if (!$isAdmin) {
            $misPendientes = Company::where('created_by', $user->id)->pendientes()->count();
            return view('user.companies.index', [
                'companies' => $companies,
                'misPendientes' => $misPendientes,
                'sectorOptions' => $sectorOptions,
                'estadoOptions' => $estadoOptions,
                'ejecutivoOptions' => $ejecutivoOptions,
            ]);
        }
        return view('companies.index', [
            'companies' => $companies,
            'companyContactsCard' => $companyContactsCard,
            'companyNames' => $companyNames,
            'sectorOptions' => $sectorOptions,
            'estadoOptions' => $estadoOptions,
            'ejecutivoOptions' => $ejecutivoOptions,
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
     * Cada fila del archivo representa una empresa y, opcionalmente,
     * un contacto asociado. El usuario sube un solo archivo y el sistema
     * se encarga de separar empresas y contactos.
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
                $normalized = Str::of($headerValue)->lower()->trim()->replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n']);
                $normalizedHeaders[(string) $normalized] = $column;
            }

            // Helper para obtener el valor de una columna por nombre lógico
            $getValue = function (array $row, array $candidates) use ($normalizedHeaders) {
                foreach ($candidates as $candidate) {
                    $key = Str::of($candidate)->lower()->trim()->replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'])->toString();
                    if (isset($normalizedHeaders[$key])) {
                        $col = $normalizedHeaders[$key];
                        return $row[$col] ?? null;
                    }
                }
                return null;
            };

            $createdCompanies = 0;
            $updatedCompanies = 0;
            $createdContacts = 0;

            DB::beginTransaction();

            foreach ($rows as $row) {
                // Saltar filas completamente vacías
                $isEmpty = collect($row)->filter(function ($value) {
                    return !is_null($value) && trim((string) $value) !== '';
                })->isEmpty();

                if ($isEmpty) {
                    continue;
                }

                $companyName = trim((string) $getValue($row, [
                    'nombre empresa',
                    'nombre de la empresa',
                    'empresa',
                    'nombre',
                    'nombre comercial',
                ]));

                if ($companyName === '') {
                    // Si no hay nombre de empresa, no podemos relacionar la fila
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
                        ? $datosFiscales . ' | Notas: ' . $notasEmpresa
                        : 'Notas: ' . $notasEmpresa;
                }

                $approvalStatus = $user->can('companies.approve') ? 'aprobado' : 'pendiente';

                // Buscar empresa por nombre (en tu Excel el "Nombre" es el de la empresa).
                // Si existen varias coincidencias por estado, preferimos la que coincida con el estado del renglón;
                // si no, usamos la primera.
                // Incluir soft-deleted para evitar violar el UNIQUE al crear de nuevo
                $companyCandidates = Company::withTrashed()->where('nombre_comercial', $companyName)->get();
                $company = null;
                if ($companyCandidates->isNotEmpty()) {
                    $company = $companyCandidates->firstWhere('estado', $estado) ?? $companyCandidates->first();
                }

                // Si la fila es de CONTACTO (Area de trabajo != EMPRESA) y no existe la empresa,
                // NO creamos nuevas empresas desde estas filas; esperamos que exista en el Excel.
                if (! $company && ! $isEmpresaRow) {
                    continue;
                }

                if ($company) {
                    // Si estaba borrada (soft delete), la restauramos para poder actualizar sin chocar el UNIQUE.
                    if (method_exists($company, 'trashed') && $company->trashed()) {
                        $company->restore();
                    }

                    // Para filas de EMPRESA actualizamos normalmente.
                    // Para filas de CONTACTO solo llenamos campos que estén vacíos en BD.
                    $fillSector = $sector !== '' && ($company->sector === null || trim((string) $company->sector) === '');
                    $fillMunicipio = $municipio !== '' && ($company->municipio === null || trim((string) $company->municipio) === '');
                    $fillEstado = $estado !== '' && ($company->estado === null || trim((string) $company->estado) === '');
                    $fillEjecutivo = $ejecutivo !== '' && ($company->ejecutivo_asignado === null || trim((string) $company->ejecutivo_asignado) === '');
                    $fillDatosFiscales = $isEmpresaRow && $datosFiscales !== '' && ($company->datos_fiscales === null || trim((string) $company->datos_fiscales) === '');

                    $company->fill([
                        'sector' => $isEmpresaRow ? ($sector !== '' ? $sector : $company->sector) : ($fillSector ? $sector : $company->sector),
                        'municipio' => $isEmpresaRow ? ($municipio !== '' ? $municipio : $company->municipio) : ($fillMunicipio ? $municipio : $company->municipio),
                        'estado' => $isEmpresaRow ? ($estado !== '' ? $estado : $company->estado) : ($fillEstado ? $estado : $company->estado),
                        'ejecutivo_asignado' => $isEmpresaRow ? ($ejecutivo !== '' ? $ejecutivo : $company->ejecutivo_asignado) : ($fillEjecutivo ? $ejecutivo : $company->ejecutivo_asignado),
                        'datos_fiscales' => $fillDatosFiscales ? $datosFiscales : $company->datos_fiscales,
                    ]);
                    $company->save();
                    if ($isEmpresaRow) {
                        $updatedCompanies++;
                    }
                } else {
                    $company = Company::create([
                        'nombre_comercial' => $companyName,
                        'rfc' => null,
                        'sector' => $sector !== '' ? $sector : null,
                        'municipio' => $municipio !== '' ? $municipio : null,
                        'estado' => $estado !== '' ? $estado : null,
                        'ejecutivo_asignado' => $ejecutivo !== '' ? $ejecutivo : null,
                        // Solo guardamos domicilio/notas detalladas cuando la fila es de tipo EMPRESA
                        'datos_fiscales' => $isEmpresaRow && $datosFiscales !== '' ? $datosFiscales : null,
                        'status_color' => 'seguimiento',
                        'approval_status' => $approvalStatus,
                        'created_by' => $user->id,
                        'approved_by' => $approvalStatus === 'aprobado' ? $user->id : null,
                        'approved_at' => $approvalStatus === 'aprobado' ? now() : null,
                    ]);
                    $createdCompanies++;
                }

                // Datos del contacto (si existen)
                // Las filas cuyo "Área de trabajo" es EMPRESA se consideran solo de empresa, sin contacto.
                if ($isEmpresaRow) {
                    continue;
                }

                $contactName = trim((string) ($getValue($row, ['nombre contacto', 'nombre del contacto', 'nombre completo']) ?? ''));
                $puesto = trim((string) ($getValue($row, ['puesto de trabajo', 'puesto']) ?? ''));
                $departamento = $areaTrabajoValor !== ''
                    ? $areaTrabajoValor
                    : trim((string) ($getValue($row, ['departamento']) ?? ''));
                $telefono = trim((string) ($getValue($row, ['telefono', 'teléfono']) ?? ''));
                $celular = trim((string) ($getValue($row, ['celular', 'movil', 'móvil']) ?? ''));
                $email = trim((string) ($getValue($row, ['email', 'correo', 'correo electronico', 'correo electrónico']) ?? ''));
                $notasContacto = trim((string) ($getValue($row, ['notas contacto']) ?? ''));
                $noDeseaCorreos = trim((string) ($getValue($row, ['no desea recibir correos']) ?? ''));

                // Si no hay datos relevantes de contacto, lo omitimos
                if ($contactName === '' && $puesto === '' && $departamento === '' && $telefono === '' && $celular === '' && $email === '') {
                    continue;
                }

                $emailActivo = true;
                if ($noDeseaCorreos !== '') {
                    $value = Str::lower($noDeseaCorreos);
                    if (in_array($value, ['si', 'sí', 'yes', '1', 'true'])) {
                        $emailActivo = false;
                    }
                }

                // Evitar duplicados de contacto dentro de la misma empresa: usamos company + email (si existe) o teléfono
                $contactQuery = Contact::withTrashed()->where('company_id', $company->id);
                if ($email !== '') {
                    $contactQuery->where('email', $email);
                } elseif ($celular !== '') {
                    $contactQuery->where('celular', $celular);
                } elseif ($telefono !== '') {
                    $contactQuery->where('telefono', $telefono);
                }

                $contact = $contactQuery->first();

                // Si el email ya existe en otra empresa y la BD tiene índice UNIQUE,
                // lo vaciamos para no violar la restricción. A ti no te afecta porque
                // aceptas campos vacíos y sin límite.
                if ($email !== '' && Contact::where('email', $email)
                        ->when($contact, fn ($q) => $q->where('id', '!=', $contact->id))
                        ->where('company_id', '!=', $company->id)
                        ->exists()) {
                    $email = null;
                }

                $contactData = [
                    'company_id' => $company->id,
                    'nombre_completo' => $contactName !== '' ? $contactName : ($contact->nombre_completo ?? $companyName),
                    'puesto_de_trabajo' => $puesto !== '' ? $puesto : ($contact->puesto_de_trabajo ?? null),
                    'departamento' => $departamento !== '' ? $departamento : ($contact->departamento ?? null),
                    'celular' => $celular !== '' ? $celular : ($contact->celular ?? null),
                    'telefono' => $telefono !== '' ? $telefono : ($contact->telefono ?? null),
                    'email' => $email !== '' ? $email : ($contact->email ?? null),
                    'email_activo' => $emailActivo,
                    'municipio' => $municipio !== '' ? $municipio : ($contact->municipio ?? null),
                    'estado' => $estado !== '' ? $estado : ($contact->estado ?? null),
                    'notas' => $notasContacto !== '' ? $notasContacto : ($contact->notas ?? null),
                    'status_color' => $contact->status_color ?? 'seguimiento',
                    'created_by' => $contact->created_by ?? $user->id,
                ];

                if ($contact) {
                    $contact->fill($contactData);
                    $contact->save();
                } else {
                    Contact::create($contactData);
                    $createdContacts++;
                }
            }

            DB::commit();

            $message = "Importación completada. Empresas nuevas: {$createdCompanies}, empresas actualizadas: {$updatedCompanies}, contactos nuevos: {$createdContacts}.";

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
     * Eliminar el recurso especificado del almacenamiento.
     * Solo administradores pueden borrar definitivamente.
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
