<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Log;

/**
 * Controlador de Gestión de Datos
 * 
 * Permite visualizar, editar, exportar e importar datos de Contactos y Empresas
 * con control de acceso por roles (solo admin puede exportar/importar)
 */
class DataManagementController extends Controller
{
    /**
     * Muestra la vista principal con tabs para Contactos y Empresas
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'contacts'); // Por defecto muestra contactos
        
        $contacts = [];
        $companies = [];
        
        if ($tab === 'contacts') {
            $contacts = Contact::with(['company', 'creator'])
                ->latest()
                ->paginate(15, ['*'], 'contacts_page');
        } else {
            $companies = Company::with(['creator', 'approver'])
                ->latest()
                ->paginate(15, ['*'], 'companies_page');
        }
        
        return view('data-management.index', compact('contacts', 'companies', 'tab'));
    }

    /**
     * Obtiene un contacto en JSON para edición en modal (AJAX)
     */
    public function getContact(Contact $contact)
    {
        if (!auth()->user()->esAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $contact->load('company');
        $companies = Company::orderBy('nombre_comercial')->get(['id', 'nombre_comercial']);
        return response()->json([
            'contact' => $contact,
            'companies' => $companies,
        ]);
    }

    /**
     * Obtiene una empresa en JSON para edición en modal (AJAX)
     */
    public function getCompany(Company $company)
    {
        if (!auth()->user()->esAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return response()->json(['company' => $company]);
    }

    /**
     * Elimina un contacto (AJAX)
     */
    public function destroyContact(Contact $contact)
    {
        if (!auth()->user()->esAdmin()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        try {
            $contact->delete();
            return response()->json(['success' => true, 'message' => 'Contacto eliminado exitosamente.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar contacto: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el contacto.'], 500);
        }
    }

    /**
     * Elimina una empresa (AJAX)
     */
    public function destroyCompany(Company $company)
    {
        if (!auth()->user()->esAdmin()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        try {
            $company->delete();
            return response()->json(['success' => true, 'message' => 'Empresa eliminada exitosamente.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar empresa: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar la empresa.'], 500);
        }
    }

    /**
     * Actualiza un contacto mediante AJAX (edición inline)
     */
    public function updateContact(Request $request, Contact $contact)
    {
        // Verificar permisos - usuarios normales necesitan aprobación
        if (!auth()->user()->esAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar. Debes solicitar permiso al administrador.'
            ], 403);
        }

        $validated = $request->validate([
            'nombre_completo' => 'sometimes|required|string|max:255',
            'puesto_de_trabajo' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:20',
            'extension' => 'nullable|string|max:10',
            'email' => 'sometimes|required|email|unique:contacts,email,' . $contact->id,
            'municipio' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
            'company_id' => 'sometimes|required|exists:companies,id',
        ]);

        try {
            $contact->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Contacto actualizado exitosamente.',
                'contact' => $contact->load('company')
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar contacto: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el contacto.'
            ], 500);
        }
    }

    /**
     * Actualiza una empresa mediante AJAX (edición inline)
     */
    public function updateCompany(Request $request, Company $company)
    {
        // Verificar permisos - usuarios normales necesitan aprobación
        if (!auth()->user()->esAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar. Debes solicitar permiso al administrador.'
            ], 403);
        }

        $validated = $request->validate([
            'nombre_comercial' => 'sometimes|required|string|max:255|unique:companies,nombre_comercial,' . $company->id,
            'rfc' => 'sometimes|required|string|max:13|unique:companies,rfc,' . $company->id,
            'sector' => 'nullable|string',
            'municipio' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'ejecutivo_asignado' => 'nullable|string|max:255',
            'datos_fiscales' => 'nullable|string',
            'status_color' => 'nullable|in:verde,amarillo,rojo',
        ]);

        try {
            $company->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Empresa actualizada exitosamente.',
                'company' => $company
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar empresa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la empresa.'
            ], 500);
        }
    }

    /**
     * Obtiene la lista de todas las tablas disponibles en la base de datos
     * Solo para administradores
     * Prioriza las tablas más importantes para el admin
     */
    public function getTables()
    {
        if (!auth()->check() || !auth()->user()->esAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();
            
            // Tablas importantes que el admin necesita (en orden de prioridad)
            $importantTables = [
                'contacts',
                'companies',
                'follow_ups',
                'users',
                'roles',
                'permissions',
                'model_has_roles',
                'model_has_permissions',
                'role_has_permissions',
            ];
            
            $excludedTables = ['migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'password_reset_tokens', 'sessions', 'personal_access_tokens'];
            
            $tableNames = [];
            
            if ($driver === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($tables as $table) {
                    $tableName = is_object($table) ? $table->name : $table['name'];
                    if (!in_array($tableName, $excludedTables)) {
                        $tableNames[] = $tableName;
                    }
                }
            } else {
                // MySQL/MariaDB para XAMPP
                $databaseName = $connection->getDatabaseName();
                $tables = DB::select("SELECT table_name as name FROM information_schema.tables WHERE table_schema = ?", [$databaseName]);
                foreach ($tables as $table) {
                    $tableName = is_object($table) ? $table->name : $table['name'];
                    if (!in_array($tableName, $excludedTables)) {
                        $tableNames[] = $tableName;
                    }
                }
            }

            // Separar tablas importantes y otras
            $importantFound = [];
            $otherTables = [];
            
            foreach ($tableNames as $table) {
                if (in_array($table, $importantTables)) {
                    $importantFound[] = $table;
                } else {
                    $otherTables[] = $table;
                }
            }
            
            // Ordenar las importantes según el orden definido
            usort($importantFound, function($a, $b) use ($importantTables) {
                $posA = array_search($a, $importantTables);
                $posB = array_search($b, $importantTables);
                return ($posA === false ? 999 : $posA) - ($posB === false ? 999 : $posB);
            });
            
            // Ordenar las otras alfabéticamente
            sort($otherTables);
            
            // Combinar: primero las importantes, luego las otras
            $sortedTables = array_merge($importantFound, $otherTables);

            return response()->json([
                'success' => true,
                'tables' => array_values($sortedTables)
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener tablas: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener las tablas de la base de datos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exporta las tablas seleccionadas a un archivo Excel
     * Solo para administradores
     */
    public function export(Request $request)
    {
        if (!auth()->user()->esAdmin()) {
            return redirect()->route('data-management.index')
                ->with('error', 'No tienes permisos para exportar datos.');
        }

        $request->validate([
            'tables' => 'required|array|min:1',
            'tables.*' => 'string'
        ]);

        try {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0); // Eliminar la hoja por defecto

            $selectedTables = $request->input('tables', []);

            foreach ($selectedTables as $tableName) {
                try {
                    // Obtener datos de la tabla
                    $data = DB::table($tableName)->get();
                    
                    if ($data->isEmpty()) {
                        continue; // Saltar tablas vacías
                    }

                    // Crear nueva hoja
                    $sheet = $spreadsheet->createSheet();
                    $sheet->setTitle($this->sanitizeSheetName($tableName));

                    // Obtener columnas del primer registro
                    $firstRecord = $data->first();
                    $columns = array_keys((array) $firstRecord);
                    
                    // Escribir encabezados (API PhpSpreadsheet 5: coordenadas como 'A1', 'B1', etc.)
                    $col = 1;
                    foreach ($columns as $column) {
                        $cellRef = Coordinate::stringFromColumnIndex($col) . '1';
                        $sheet->setCellValue($cellRef, $column);
                        $sheet->getStyle($cellRef)->getFont()->setBold(true);
                        $col++;
                    }

                    // Escribir datos
                    $row = 2;
                    foreach ($data as $record) {
                        $col = 1;
                        foreach ($columns as $column) {
                            $value = is_object($record) ? ($record->$column ?? '') : ($record[$column] ?? '');
                            // Convertir objetos DateTime a string
                            if ($value instanceof \DateTime) {
                                $value = $value->format('Y-m-d H:i:s');
                            }
                            $cellRef = Coordinate::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValue($cellRef, $value);
                            $col++;
                        }
                        $row++;
                    }

                    // Auto-ajustar ancho de columnas
                    foreach (range(1, count($columns)) as $colIndex) {
                        $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
                    }
                } catch (\Exception $e) {
                    Log::warning("Error al exportar tabla {$tableName}: " . $e->getMessage());
                    continue; // Continuar con la siguiente tabla
                }
            }

            // Si no hay hojas, crear una vacía
            if ($spreadsheet->getSheetCount() === 0) {
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle('Sin datos');
            }

            // Generar archivo
            $writer = new Xlsx($spreadsheet);
            $fileName = 'exportacion_datos_' . date('Y-m-d_His') . '.xlsx';
            $filePath = storage_path('app/temp/' . $fileName);
            
            // Crear directorio si no existe
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $writer->save($filePath);

            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error al exportar datos: ' . $e->getMessage());
            return redirect()->route('data-management.index')
                ->with('error', 'Error al exportar los datos. Por favor, intente nuevamente.');
        }
    }

    /**
     * Procesa la importación de archivos Excel
     * Solo para administradores
     * 
     * Formato esperado del Excel:
     * - Columna 1 (A): ID (se ignora, no se usa para validación)
     * - Columna 2 (B): Nombre/Razón Social (clave de validación para evitar duplicados)
     * - Columnas siguientes: Otros campos según encabezados
     * 
     * Validación de duplicados:
     * - Se usa el nombre de la columna 2 (normalizado: trim + case-insensitive)
     * - Si el nombre existe → actualiza el registro
     * - Si el nombre no existe → crea nuevo registro
     */
    public function import(Request $request)
    {
        if (!auth()->user()->esAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para importar datos.'
            ], 403);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx', 'xls'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Solo se permiten archivos Excel (.xlsx o .xls).',
            ], 422);
        }

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            
            $results = [
                'created' => 0,
                'updated' => 0,
                'errors' => []
            ];

            // Procesar cada hoja del Excel
            foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                $sheetName = $worksheet->getTitle();
                
                // Mapear nombre de hoja a modelo
                $model = $this->getModelFromTableName($sheetName);
                if (!$model) {
                    $results['errors'][] = "Tabla '{$sheetName}' no reconocida. Se omite.";
                    continue;
                }

                $rows = $worksheet->toArray();
                if (empty($rows) || count($rows) < 2) {
                    continue; // Necesitamos al menos encabezados y una fila de datos
                }

                // Primera fila son los encabezados (para referencia, pero usaremos posición)
                $headers = $rows[0];
                $dataRows = array_slice($rows, 1);

                // Determinar el campo clave según el modelo
                $keyField = $this->getKeyFieldForModel($model);

                DB::beginTransaction();
                try {
                    foreach ($dataRows as $rowIndex => $row) {
                        // Filtrar filas completamente vacías
                        if (empty(array_filter($row, function($val) { return $val !== null && trim($val) !== ''; }))) {
                            continue;
                        }

                        // Columna 1 = ID (ignorar, no usar para validación)
                        // Columna 2 = Nombre (clave de validación)
                        $nameValue = isset($row[1]) ? trim((string)$row[1]) : null;
                        
                        if (empty($nameValue)) {
                            $results['errors'][] = "Fila " . ($rowIndex + 2) . " en hoja '{$sheetName}': El nombre (columna 2) está vacío. Se omite.";
                            continue;
                        }

                        // Normalizar el nombre: trim y case-insensitive para comparación
                        $normalizedName = $this->normalizeName($nameValue);

                        // Mapear todas las columnas del Excel a campos de la BD
                        $mappedData = $this->mapExcelColumnsByPosition($model, $row, $headers);
                        
                        if (empty($mappedData)) {
                            $results['errors'][] = "Fila " . ($rowIndex + 2) . " en hoja '{$sheetName}': No se pudieron mapear los datos. Se omite.";
                            continue;
                        }

                        // Asegurar que el campo clave esté presente con el valor original (no normalizado)
                        $mappedData[$keyField] = $nameValue;
                        
                        // Validación especial para contactos: company_id es obligatorio
                        if ($model === Contact::class) {
                            if (!isset($mappedData['company_id']) || empty($mappedData['company_id'])) {
                                $results['errors'][] = "Fila " . ($rowIndex + 2) . " en hoja '{$sheetName}': El campo 'company_id' (empresa) es obligatorio para contactos. Se omite.";
                                continue;
                            }
                            
                            // Si company_id es un nombre de empresa en lugar de ID, intentar buscarlo
                            if (!is_numeric($mappedData['company_id'])) {
                                $companyName = trim($mappedData['company_id']);
                                $company = Company::whereRaw('LOWER(TRIM(nombre_comercial)) = ?', [strtolower($companyName)])->first();
                                if ($company) {
                                    $mappedData['company_id'] = $company->id;
                                } else {
                                    $results['errors'][] = "Fila " . ($rowIndex + 2) . " en hoja '{$sheetName}': No se encontró la empresa '{$companyName}'. Se omite.";
                                    continue;
                                }
                            }
                        }
                        
                        // Upsert: buscar por nombre normalizado (case-insensitive)
                        $record = $this->findOrCreateRecordByName($model, $keyField, $normalizedName, $mappedData);
                        
                        if ($record->wasRecentlyCreated) {
                            $results['created']++;
                        } else {
                            // Remover 'id' y campos no fillable de los datos de actualización
                            unset($mappedData['id']);
                            // Remover campos que no son fillable
                            $fillable = (new $model)->getFillable();
                            $mappedData = array_intersect_key($mappedData, array_flip($fillable));
                            $record->update($mappedData);
                            $results['updated']++;
                        }
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $results['errors'][] = "Error en hoja '{$sheetName}': " . $e->getMessage();
                    Log::error("Error al importar hoja {$sheetName}: " . $e->getMessage());
                }
            }

            // Construir mensaje de resumen
            $message = "Importación completada. ";
            $message .= $results['created'] . " registro" . ($results['created'] != 1 ? 's' : '') . " creado" . ($results['created'] != 1 ? 's' : '') . ", ";
            $message .= $results['updated'] . " registro" . ($results['updated'] != 1 ? 's' : '') . " actualizado" . ($results['updated'] != 1 ? 's' : '') . ".";
            
            if (!empty($results['errors'])) {
                $message .= " Se encontraron " . count($results['errors']) . " error" . (count($results['errors']) != 1 ? 'es' : '') . ".";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Error al importar datos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al importar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el modelo correspondiente a un nombre de tabla
     */
    private function getModelFromTableName(string $tableName): ?string
    {
        $mapping = [
            'contacts' => Contact::class,
            'companies' => Company::class,
        ];

        return $mapping[$tableName] ?? null;
    }

    /**
     * Mapea las columnas del Excel a los campos del modelo
     */
    private function mapExcelColumnsToModel(string $modelClass, array $data): array
    {
        $mapped = [];
        $fillable = (new $modelClass)->getFillable();

        // Mapeo de nombres comunes
        $columnMapping = [
            'nombre completo' => 'nombre_completo',
            'nombrecompleto' => 'nombre_completo',
            'nombre' => 'nombre_completo',
            'puesto de trabajo' => 'puesto_de_trabajo',
            'puestodetrabajo' => 'puesto_de_trabajo',
            'puesto' => 'puesto_de_trabajo',
            'nombre comercial' => 'nombre_comercial',
            'nombrecomercial' => 'nombre_comercial',
            'rfc' => 'rfc',
            'email' => 'email',
            'correo' => 'email',
            'celular' => 'celular',
            'telefono' => 'celular',
            'teléfono' => 'celular',
            'extension' => 'extension',
            'extensión' => 'extension',
            'municipio' => 'municipio',
            'estado' => 'estado',
            'sector' => 'sector',
            'ejecutivo asignado' => 'ejecutivo_asignado',
            'ejecutivoasignado' => 'ejecutivo_asignado',
            'datos fiscales' => 'datos_fiscales',
            'datosfiscales' => 'datos_fiscales',
            'status color' => 'status_color',
            'statuscolor' => 'status_color',
            'notas' => 'notas',
            'company id' => 'company_id',
            'companyid' => 'company_id',
            'empresa' => 'company_id',
        ];

        foreach ($data as $key => $value) {
            $normalizedKey = strtolower(trim($key));
            
            // Buscar en el mapeo
            if (isset($columnMapping[$normalizedKey])) {
                $mappedKey = $columnMapping[$normalizedKey];
                if (in_array($mappedKey, $fillable)) {
                    $mapped[$mappedKey] = $value;
                }
            } elseif (in_array($normalizedKey, $fillable)) {
                // Si el nombre coincide directamente con un campo fillable
                $mapped[$normalizedKey] = $value;
            }
        }

        return $mapped;
    }

    /**
     * Obtiene el campo clave para validación según el modelo
     */
    private function getKeyFieldForModel(string $modelClass): string
    {
        if ($modelClass === Contact::class) {
            return 'nombre_completo';
        } elseif ($modelClass === Company::class) {
            return 'nombre_comercial';
        }
        
        return 'id'; // Fallback
    }

    /**
     * Normaliza un nombre para comparación (trim y case-insensitive)
     */
    private function normalizeName(string $name): string
    {
        return strtolower(trim($name));
    }

    /**
     * Busca un registro existente por nombre normalizado o crea uno nuevo (Upsert)
     * NO usa ID para validación, solo el nombre (campo clave)
     */
    private function findOrCreateRecordByName(string $modelClass, string $keyField, string $normalizedName, array $data)
    {
        $model = new $modelClass;
        
        // Buscar por nombre normalizado (case-insensitive)
        // Intentar usar consulta SQL primero para mejor rendimiento
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        
        $record = null;
        
        if ($driver === 'sqlite') {
            // Para SQLite, usar LOWER y TRIM
            $record = $model->whereRaw("LOWER(TRIM({$keyField})) = ?", [$normalizedName])->first();
        } else {
            // Para MySQL/PostgreSQL, usar LOWER y TRIM
            $record = $model->whereRaw("LOWER(TRIM({$keyField})) = ?", [$normalizedName])->first();
        }
        
        // Si la consulta SQL falla o no encuentra nada, hacer búsqueda en memoria como fallback
        if (!$record) {
            $allRecords = $model->select($keyField, 'id')->get();
            foreach ($allRecords as $candidate) {
                $recordName = $this->normalizeName($candidate->$keyField ?? '');
                if ($recordName === $normalizedName) {
                    $record = $model->find($candidate->id);
                    break;
                }
            }
        }

        if ($record) {
            return $record;
        }

        // No se encontró, crear nuevo registro
        // Remover 'id' si existe (no se debe incluir al crear)
        unset($data['id']);
        
        // Asegurar que created_by esté presente
        if (!isset($data['created_by'])) {
            $data['created_by'] = auth()->id();
        }
        
        // Asegurar que el campo clave esté presente con el valor original (no normalizado)
        // El valor original ya debería estar en $data[$keyField] desde el método de importación
        
        return $model->create($data);
    }

    /**
     * Mapea las columnas del Excel por posición a los campos del modelo
     * Columna 1 = ID (ignorar)
     * Columna 2 = Nombre (clave de validación)
     * Resto de columnas según mapeo
     */
    private function mapExcelColumnsByPosition(string $modelClass, array $row, array $headers): array
    {
        $mapped = [];
        $fillable = (new $modelClass)->getFillable();
        
        // Mapeo de posición de columna a campo (empezando desde índice 0)
        // Columna 0 = ID (ignorar)
        // Columna 1 = Nombre (ya se maneja por separado)
        // Columna 2+ = Otros campos según encabezado
        
        // Mapeo de nombres de encabezados comunes a campos de BD
        $headerMapping = [
            'id' => null, // Ignorar
            'nombre completo' => 'nombre_completo',
            'nombrecompleto' => 'nombre_completo',
            'nombre' => 'nombre_completo',
            'nombre comercial' => 'nombre_comercial',
            'nombrecomercial' => 'nombre_comercial',
            'razon social' => 'nombre_comercial',
            'razonsocial' => 'nombre_comercial',
            'rfc' => 'rfc',
            'email' => 'email',
            'correo' => 'email',
            'e-mail' => 'email',
            'celular' => 'celular',
            'telefono' => 'celular',
            'teléfono' => 'celular',
            'extension' => 'extension',
            'extensión' => 'extension',
            'puesto de trabajo' => 'puesto_de_trabajo',
            'puestodetrabajo' => 'puesto_de_trabajo',
            'puesto' => 'puesto_de_trabajo',
            'cargo' => 'puesto_de_trabajo',
            'departamento' => 'departamento',
            'municipio' => 'municipio',
            'estado' => 'estado',
            'sector' => 'sector',
            'giro' => 'sector',
            'ejecutivo asignado' => 'ejecutivo_asignado',
            'ejecutivoasignado' => 'ejecutivo_asignado',
            'vendedor' => 'ejecutivo_asignado',
            'datos fiscales' => 'datos_fiscales',
            'datosfiscales' => 'datos_fiscales',
            'status color' => 'status_color',
            'statuscolor' => 'status_color',
            'estatus' => 'status_color',
            'notas' => 'notas',
            'company id' => 'company_id',
            'companyid' => 'company_id',
            'empresa' => 'company_id',
            'id empresa' => 'company_id',
            'id_empresa' => 'company_id',
            'empresa id' => 'company_id',
            'empresaid' => 'company_id',
        ];

        // Procesar cada columna (empezando desde índice 2, ya que 0=ID y 1=Nombre)
        for ($colIndex = 2; $colIndex < count($row); $colIndex++) {
            $value = $row[$colIndex] ?? null;
            
            // Saltar valores vacíos
            if ($value === null || (is_string($value) && trim($value) === '')) {
                continue;
            }

            // Obtener el nombre del encabezado (si existe)
            $headerName = isset($headers[$colIndex]) ? strtolower(trim((string)$headers[$colIndex])) : null;
            
            $fieldName = null;
            
            // Buscar en el mapeo de encabezados
            if ($headerName && isset($headerMapping[$headerName])) {
                $fieldName = $headerMapping[$headerName];
            } elseif ($headerName && in_array($headerName, $fillable)) {
                // Si el encabezado coincide directamente con un campo fillable
                $fieldName = $headerName;
            }
            
            // Si encontramos un campo válido, agregarlo
            if ($fieldName && in_array($fieldName, $fillable)) {
                // Limpiar el valor
                $mapped[$fieldName] = is_string($value) ? trim($value) : $value;
            }
        }

        return $mapped;
    }

    /**
     * Sanitiza el nombre de la hoja para Excel (máximo 31 caracteres, sin caracteres especiales)
     */
    private function sanitizeSheetName(string $name): string
    {
        $name = substr($name, 0, 31);
        $name = preg_replace('/[\\\\\/\?\*\[\]:]/', '', $name);
        return $name ?: 'Sheet';
    }
}
