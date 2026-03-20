<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\FilterSpec;
use App\Models\Contact;
use App\Models\Company;
use App\Services\DynamicFilterService;
use App\Services\FilterConfig;
use Illuminate\Http\Request;

class FiltrosController extends Controller
{
    /**
     * Vista de filtros avanzados: contactos y empresas (filtros dinámicos).
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Company::class);

        $user = auth()->user();
        $isAdmin = $user?->esAdmin() ?? false;
        $userId = $user?->id;

        $filterLogic = DynamicFilterService::logicFromRequest($request);
        $filterSpecs = DynamicFilterService::parseFromRequest($request);
        $resultScope = $this->resolveResultScope($request->input('result_scope'));
        $filterService = app(DynamicFilterService::class);

        $contactFields = FilterConfig::contactFieldsWithOptions();
        $companyFields = FilterConfig::companyFields();
        $companyFields['status_color']['options'] = FilterConfig::prospectStatusColorOptions();
        $operatorLabels = FilterConfig::allOperatorLabels();

        // Unificamos campos para que el constructor muestre filtros "para todo".
        // Si hay colisión de claves, prioriza contactos (config/inputs coherentes).
        $fields = array_merge($companyFields, $contactFields);

        // Separar specs por entidad (evita aplicar campos de empresa a contactos, etc.)
        $contactFieldKeys = array_merge(array_keys($contactFields), ['sector']);
        $companyFieldKeys = array_keys($companyFields);
        $contactFilterSpecs = array_values(array_filter($filterSpecs, fn ($s) => in_array($s->field, $contactFieldKeys, true)));
        $companyFilterSpecs = array_values(array_map(function ($spec) {
            if ($spec->field === 'domicilio') {
                return new FilterSpec('datos_fiscales', $spec->operator, $spec->value);
            }
            return $spec;
        }, array_filter($filterSpecs, fn ($s) => in_array($s->field, array_merge($companyFieldKeys, ['domicilio']), true))));
        $hasAnyFilters = count($filterSpecs) > 0;
        $hasContactFilters = count($contactFilterSpecs) > 0;
        $hasCompanyFilters = count($companyFilterSpecs) > 0;

        // Queries base para construir opciones (multi-select) tipo Excel:
        // deben mostrar todos los valores existentes en cada columna.
        // Incluimos soft-deleted para poblar catálogos completos tipo Excel.
        $baseContactsForOptions = Contact::withTrashed();
        $baseCompaniesForOptions = Company::query();

        // Queries base con alcance de usuario para resultados/sugerencias operativas.
        $baseContacts = Contact::query();
        if (! $isAdmin && $userId) {
            $baseContacts->where('created_by', $userId)
                ->where('approval_status', 'aprobado');
        }

        $baseCompanies = Company::query();
        if (! $isAdmin && $userId) {
            $baseCompanies->accessibleForExecutive($user);
        }

        $distinctToOptions = function ($query, string $col, int $limit = 200) {
            return $query->whereNotNull($col)
                ->where($col, '!=', '')
                ->distinct()
                ->orderBy($col)
                ->limit($limit)
                ->pluck($col)
                ->unique()
                ->values()
                ->mapWithKeys(fn ($v) => [(string)$v => (string)$v])
                ->toArray();
        };

        // Opciones (contactos)
        $contactFields['nombre_completo']['options'] = $distinctToOptions((clone $baseContactsForOptions), 'nombre_completo', 5000);
        $contactFields['telefono']['options'] = $distinctToOptions((clone $baseContactsForOptions), 'telefono', 5000);
        $contactFields['celular']['options'] = $distinctToOptions((clone $baseContactsForOptions), 'celular', 5000);
        $contactFields['email']['options'] = $distinctToOptions((clone $baseContactsForOptions), 'email', 5000);
        $contactFields['notas']['options'] = $distinctToOptions((clone $baseContactsForOptions), 'notas', 5000);
        $contactFields['departamento']['options'] = $distinctToOptions((clone $baseContactsForOptions), 'departamento', 5000);
        $contactFields['puesto_de_trabajo']['options'] = $distinctToOptions((clone $baseContactsForOptions), 'puesto_de_trabajo', 5000);

        // municipio/estado: unimos opciones de contactos y empresas para que "sea por igual"
        $municipioOptsContacts = array_keys($distinctToOptions((clone $baseContactsForOptions), 'municipio', 5000));
        $municipioOptsCompanies = array_keys($distinctToOptions((clone $baseCompaniesForOptions), 'municipio', 5000));
        $municipioOpts = collect(array_merge($municipioOptsContacts, $municipioOptsCompanies))
            ->unique()
            ->values()
            ->mapWithKeys(fn ($v) => [(string)$v => (string)$v])
            ->toArray();

        $estadoOptsContacts = array_keys($distinctToOptions((clone $baseContactsForOptions), 'estado', 5000));
        $estadoOptsCompanies = array_keys($distinctToOptions((clone $baseCompaniesForOptions), 'estado', 5000));
        $estadoOpts = collect(array_merge($estadoOptsContacts, $estadoOptsCompanies))
            ->unique()
            ->values()
            ->mapWithKeys(fn ($v) => [(string)$v => (string)$v])
            ->toArray();

        $contactFields['municipio']['options'] = $municipioOpts;
        $contactFields['estado']['options'] = $estadoOpts;

        // Comercial: usar empresas que estén relacionadas con el alcance del usuario
        $comercialQuery = Company::query()
            ->whereNotNull('nombre_comercial')
            ->where('nombre_comercial', '!=', '');
        if (! $isAdmin && $userId) {
            $comercialQuery->accessibleForExecutive($user);
        }

        $comercialValues = $comercialQuery
            ->orderBy('nombre_comercial')
            ->distinct()
            ->pluck('nombre_comercial')
            ->unique()
            ->values();

        $contactFields['comercial']['options'] = $comercialValues
            ->mapWithKeys(fn ($v) => [(string)$v => (string)$v])
            ->toArray();

        $contactFields['domicilio']['options'] = [
            'con_domicilio' => 'Con domicilio',
            'sin_domicilio' => 'Sin domicilio',
        ];
        $contactFields['no_recibir_correos']['options'] = [
            '1' => 'Sí',
            '0' => 'No',
        ];

        // Opciones (empresas)
        $companyFields['sector']['options'] = $distinctToOptions((clone $baseCompaniesForOptions), 'sector', 5000);
        $companyFields['comercial']['options'] = $distinctToOptions((clone $baseCompaniesForOptions), 'nombre_comercial', 5000);
        $companyFields['datos_fiscales']['options'] = [
            'con_domicilio' => 'Con domicilio',
            'sin_domicilio' => 'Sin domicilio',
        ];

        // Recálculo de referencia para que el formulario use las opciones actualizadas.
        $fields = array_merge($companyFields, $contactFields);

        $filtersForChips = $this->mapFilterSpecsToChips($filterSpecs, $fields, $operatorLabels);

        // Ejecutar filtros sobre ambas entidades (mismo set de specs, pero filtrados por entidad)
        $contacts = null;
        $shouldQueryContacts = match ($resultScope) {
            'contacto' => ! $hasAnyFilters || $hasContactFilters,
            'ambos' => ! $hasAnyFilters || $hasContactFilters,
            default => false,
        };
        if ($shouldQueryContacts) {
            $contactsQuery = Contact::query()->with('company');
            if (! $isAdmin && $userId) {
                $contactsQuery->where('created_by', $userId)
                    ->where('approval_status', 'aprobado');
            }
            $filterService->applyToContactQuery($contactsQuery, $contactFilterSpecs, $filterLogic);
            $contacts = $contactsQuery->latest()->paginate(20)->appends($request->except('_token'));
        }

        $companies = null;
        $shouldQueryCompanies = match ($resultScope) {
            'empresa' => ! $hasAnyFilters || $hasCompanyFilters,
            'ambos' => ! $hasAnyFilters || $hasCompanyFilters,
            default => false,
        };
        if ($shouldQueryCompanies) {
            $companiesQuery = Company::with('contacts');
            if (! $isAdmin && $userId) {
                $companiesQuery->accessibleForExecutive($user);
            }
            $filterService->applyToCompanyQuery($companiesQuery, $companyFilterSpecs, $filterLogic);
            $companies = $companiesQuery->latest()->paginate(20)->appends($request->except('_token'));
        }

        // Sugerencias para autocompletar en inputs de texto (datalist).
        // Usamos el alcance del usuario (admin o creador) definido en $baseContacts.
        $suggestionDistinct = function (string $col, int $limit = 200) use ($baseContacts): array {
            return (clone $baseContacts)
                ->whereNotNull($col)
                ->where($col, '!=', '')
                ->distinct()
                ->orderBy($col)
                ->limit($limit)
                ->pluck($col)
                ->values()
                ->map(fn ($v) => (string) $v)
                ->toArray();
        };

        $fieldSuggestions = [
            'nombre_completo' => $suggestionDistinct('nombre_completo', 200),
            'telefono' => $suggestionDistinct('telefono', 200),
            'celular' => $suggestionDistinct('celular', 200),
            'email' => $suggestionDistinct('email', 200),
        ];

        return view('filtros.index', [
            'contacts' => $contacts,
            'companies' => $companies,
            'filterLogic' => $filterLogic,
            'filterSpecs' => $filterSpecs,
            'filtersForChips' => $filtersForChips,
            'operatorLabels' => $operatorLabels,
            'fields' => $fields,
            'contactFields' => $contactFields,
            'companyFields' => $companyFields,
            'fieldSuggestions' => $fieldSuggestions,
            'resultScope' => $resultScope,
            'prospectStatusLabels' => FilterConfig::prospectStatusColorOptions(),
        ]);
    }

    /**
     * Responde filtros por AJAX (sin recargar página).
     */
    public function ajax(Request $request)
    {
        $this->authorize('viewAny', Company::class);

        $user = auth()->user();
        $isAdmin = $user?->esAdmin() ?? false;
        $userId = $user?->id;

        $filterLogic = DynamicFilterService::logicFromRequest($request);
        $filterSpecs = DynamicFilterService::parseFromRequest($request);
        $resultScope = $this->resolveResultScope($request->input('result_scope'));
        $filterService = app(DynamicFilterService::class);

        $contactFields = FilterConfig::contactFieldsWithOptions();
        $companyFields = FilterConfig::companyFields();
        $companyFields['status_color']['options'] = FilterConfig::prospectStatusColorOptions();
        $operatorLabels = FilterConfig::allOperatorLabels();

        $fields = array_merge($companyFields, $contactFields);

        $contactFieldKeys = array_merge(array_keys($contactFields), ['sector']);
        $companyFieldKeys = array_keys($companyFields);
        $contactFilterSpecs = array_values(array_filter($filterSpecs, fn ($s) => in_array($s->field, $contactFieldKeys, true)));
        $companyFilterSpecs = array_values(array_map(function ($spec) {
            if ($spec->field === 'domicilio') {
                return new FilterSpec('datos_fiscales', $spec->operator, $spec->value);
            }
            return $spec;
        }, array_filter($filterSpecs, fn ($s) => in_array($s->field, array_merge($companyFieldKeys, ['domicilio']), true))));
        $hasAnyFilters = count($filterSpecs) > 0;
        $hasContactFilters = count($contactFilterSpecs) > 0;
        $hasCompanyFilters = count($companyFilterSpecs) > 0;

        $filtersForChips = $this->mapFilterSpecsToChips($filterSpecs, $fields, $operatorLabels);

        $contacts = null;
        $shouldQueryContacts = match ($resultScope) {
            'contacto' => ! $hasAnyFilters || $hasContactFilters,
            'ambos' => ! $hasAnyFilters || $hasContactFilters,
            default => false,
        };
        if ($shouldQueryContacts) {
            $contactsQuery = Contact::query()->with('company');
            if (! $isAdmin && $userId) {
                $contactsQuery->where('created_by', $userId)
                    ->where('approval_status', 'aprobado');
            }
            $filterService->applyToContactQuery($contactsQuery, $contactFilterSpecs, $filterLogic);
            $contacts = $contactsQuery->latest()->paginate(20)->appends($request->except('_token'));
        }

        $companies = null;
        $shouldQueryCompanies = match ($resultScope) {
            'empresa' => ! $hasAnyFilters || $hasCompanyFilters,
            'ambos' => ! $hasAnyFilters || $hasCompanyFilters,
            default => false,
        };
        if ($shouldQueryCompanies) {
            $companiesQuery = Company::with('contacts');
            if (! $isAdmin && $userId) {
                $companiesQuery->accessibleForExecutive($user);
            }
            $filterService->applyToCompanyQuery($companiesQuery, $companyFilterSpecs, $filterLogic);
            $companies = $companiesQuery->latest()->paginate(20)->appends($request->except('_token'));
        }

        $clearUrl = route('filtros.index');

        return response()->json([
            'chipsHtml' => view('filtros.partials.chips', [
                'filtersForChips' => $filtersForChips,
                'clearUrl' => $clearUrl,
            ])->render(),
            'resultsHtml' => view('filtros.partials.results', [
                'contacts' => $contacts,
                'companies' => $companies,
                'filterSpecs' => $filterSpecs,
                'resultScope' => $resultScope,
            ])->render(),
        ]);
    }

    private function resolveResultScope(?string $rawScope): string
    {
        return match (strtolower((string) $rawScope)) {
            'empresa' => 'empresa',
            'contacto' => 'contacto',
            default => 'ambos',
        };
    }

    /**
     * @param  array<int, FilterSpec>  $filterSpecs
     * @return array<int, array<string, mixed>>
     */
    private function mapFilterSpecsToChips(array $filterSpecs, array $fields, array $operatorLabels): array
    {
        $statusLabels = FilterConfig::prospectStatusColorOptions();

        return collect($filterSpecs)->map(function ($spec) use ($fields, $operatorLabels, $statusLabels) {
            $specArray = $spec instanceof FilterSpec ? $spec->toArray() : $spec;
            $fieldKey = $specArray['field'] ?? '';
            $value = $specArray['value'] ?? null;

            if ($fieldKey === 'status_color' && $value !== null) {
                if (is_array($value)) {
                    $value = array_map(fn ($v) => $statusLabels[(string) $v] ?? $v, $value);
                } else {
                    $value = $statusLabels[(string) $value] ?? $value;
                }
            }

            $cfg = $fields[$fieldKey] ?? null;

            return [
                'field' => $fieldKey,
                'operator' => $specArray['operator'] ?? '',
                'value' => $value,
                'field_label' => $cfg['label'] ?? $fieldKey,
                'operator_label' => $operatorLabels[$specArray['operator'] ?? ''] ?? $specArray['operator'] ?? '',
            ];
        })->all();
    }

    protected function buildContactsQuery(Request $request)
    {
        $query = Contact::query();

        // Nombre: operador + valor
        if ($request->filled('nombre_valor')) {
            $valor = $request->nombre_valor;
            $op = $request->get('nombre_op', 'contiene');
            if ($op === 'igual') {
                $query->where('nombre_completo', $valor);
            } elseif ($op === 'contiene') {
                $query->where('nombre_completo', 'like', "%{$valor}%");
            } elseif ($op === 'empieza') {
                $query->where('nombre_completo', 'like', "{$valor}%");
            } elseif ($op === 'termina') {
                $query->where('nombre_completo', 'like', "%{$valor}");
            } elseif ($op === 'vacio') {
                $query->where(function ($q) {
                    $q->whereNull('nombre_completo')->orWhere('nombre_completo', '');
                });
            } elseif ($op === 'no_vacio') {
                $query->whereNotNull('nombre_completo')->where('nombre_completo', '!=', '');
            }
        }

        // Género
        if ($request->filled('genero')) {
            if ($request->genero === 'vacio') {
                $query->where(function ($q) {
                    $q->whereNull('genero')->orWhere('genero', '');
                });
            } else {
                $query->where('genero', $request->genero);
            }
        }

        // Teléfono: con/sin/exacto/prefijo
        if ($request->filled('telefono_tipo')) {
            if ($request->telefono_tipo === 'con') {
                $query->whereNotNull('telefono')->where('telefono', '!=', '');
            } elseif ($request->telefono_tipo === 'sin') {
                $query->where(function ($q) {
                    $q->whereNull('telefono')->orWhere('telefono', '');
                });
            } elseif ($request->telefono_tipo === 'exacto' && $request->filled('telefono_valor')) {
                $query->where('telefono', $request->telefono_valor);
            } elseif ($request->telefono_tipo === 'prefijo' && $request->filled('telefono_valor')) {
                $query->where('telefono', 'like', $request->telefono_valor . '%');
            }
        }

        // Celular: con/sin
        if ($request->filled('celular_tipo')) {
            if ($request->celular_tipo === 'con') {
                $query->whereNotNull('celular')->where('celular', '!=', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('celular')->orWhere('celular', '');
                });
            }
        }

        // Email: con/sin/dominio
        if ($request->filled('email_tipo')) {
            if ($request->email_tipo === 'con') {
                $query->whereNotNull('email')->where('email', '!=', '');
            } elseif ($request->email_tipo === 'sin') {
                $query->where(function ($q) {
                    $q->whereNull('email')->orWhere('email', '');
                });
            } elseif ($request->email_tipo === 'dominio' && $request->filled('email_dominio')) {
                $query->where('email', 'like', '%@' . $request->email_dominio);
            }
        }

        // Área (departamento)
        if ($request->filled('departamento')) {
            $query->where('departamento', 'like', '%' . $request->departamento . '%');
        }

        // Puesto
        if ($request->filled('puesto')) {
            $query->where('puesto_de_trabajo', 'like', '%' . $request->puesto . '%');
        }

        // Ciudad / Municipio
        if ($request->filled('municipio')) {
            $query->where('municipio', 'like', '%' . $request->municipio . '%');
        }

        // Estado
        if ($request->filled('estado_contacto')) {
            $query->where('estado', 'like', '%' . $request->estado_contacto . '%');
        }

        // Notas: con/sin/contiene
        if ($request->filled('notas_tipo')) {
            if ($request->notas_tipo === 'con') {
                $query->whereNotNull('notas')->where('notas', '!=', '');
            } elseif ($request->notas_tipo === 'sin') {
                $query->where(function ($q) {
                    $q->whereNull('notas')->orWhere('notas', '');
                });
            } elseif ($request->notas_tipo === 'contiene' && $request->filled('notas_valor')) {
                $query->where('notas', 'like', '%' . $request->notas_valor . '%');
            }
        }

        // Domicilio: con/sin (calle_numero o colonia_cp)
        if ($request->filled('domicilio')) {
            if ($request->domicilio === 'con') {
                $query->where(function ($q) {
                    $q->whereNotNull('calle_numero')->where('calle_numero', '!=', '')
                        ->orWhereNotNull('colonia_cp')->where('colonia_cp', '!=', '');
                });
            } else {
                $query->where(function ($q) {
                    $q->whereNull('calle_numero')->orWhere('calle_numero', '');
                })->where(function ($q) {
                    $q->whereNull('colonia_cp')->orWhere('colonia_cp', '');
                });
            }
        }

        // Desea recibir correos (email_activo)
        if ($request->filled('email_activo')) {
            if ($request->email_activo === '1') {
                $query->where('email_activo', true);
            } else {
                $query->where('email_activo', false);
            }
        }

        // Empresa
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Estado prospecto
        if ($request->filled('status_color')) {
            $query->where('status_color', $request->status_color);
        }

        // Filtros operativos
        if ($request->filled('operativo')) {
            if ($request->operativo === 'contactables') {
                $query->where(function ($q) {
                    $q->whereNotNull('telefono')->where('telefono', '!=', '')
                        ->orWhereNotNull('celular')->where('celular', '!=', '')
                        ->orWhereNotNull('email')->where('email', '!=', '');
                });
            } elseif ($request->operativo === 'no_contactables') {
                $query->where(function ($q) {
                    $q->whereNull('telefono')->orWhere('telefono', '');
                })->where(function ($q) {
                    $q->whereNull('celular')->orWhere('celular', '');
                })->where(function ($q) {
                    $q->whereNull('email')->orWhere('email', '');
                });
            } elseif ($request->operativo === 'lista_mailing') {
                $query->where('email_activo', true)->whereNotNull('email')->where('email', '!=', '');
            } elseif ($request->operativo === 'lista_llamadas') {
                $query->where(function ($q) {
                    $q->whereNotNull('telefono')->where('telefono', '!=', '')
                        ->orWhereNotNull('celular')->where('celular', '!=', '');
                });
            }
        }

        return $query;
    }

    protected function buildCompaniesQuery(Request $request)
    {
        $query = Company::with('creator');

        $u = auth()->user();
        if ($u && ! $u->esAdmin()) {
            $query->accessibleForExecutive($u);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $op = $request->get('search_op', 'contiene');
            if ($op === 'igual') {
                $query->where('nombre_comercial', $search);
            } elseif ($op === 'empieza') {
                $query->where('nombre_comercial', 'like', "{$search}%");
            } elseif ($op === 'termina') {
                $query->where('nombre_comercial', 'like', "%{$search}");
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre_comercial', 'like', "%{$search}%")
                        ->orWhere('rfc', 'like', "%{$search}%")
                        ->orWhere('ejecutivo_asignado', 'like', "%{$search}%");
                });
            }
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
        if ($request->filled('municipio')) {
            $query->where('municipio', 'like', '%' . $request->municipio . '%');
        }
        if ($request->filled('ejecutivo_asignado')) {
            $query->where('ejecutivo_asignado', $request->ejecutivo_asignado);
        }
        if ($request->filled('domicilio_empresa')) {
            if ($request->domicilio_empresa === 'con') {
                $query->whereNotNull('datos_fiscales')->where('datos_fiscales', '!=', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('datos_fiscales')->orWhere('datos_fiscales', '');
                });
            }
        }
        if (auth()->user()->esAdmin() && $request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        return $query;
    }
}
