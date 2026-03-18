<?php

namespace App\Http\Controllers;

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
        $filterService = app(DynamicFilterService::class);

        $contactFields = FilterConfig::contactFieldsWithOptions();
        $companyFields = FilterConfig::companyFields();
        $operatorLabels = FilterConfig::allOperatorLabels();

        // Unificamos campos para que el constructor muestre filtros "para todo".
        // Si hay colisión de claves, prioriza contactos (config/inputs coherentes).
        $fields = array_merge($companyFields, $contactFields);

        // Separar specs por entidad (evita aplicar campos de empresa a contactos, etc.)
        $contactFieldKeys = array_merge(array_keys($contactFields), ['sector']);
        $companyFieldKeys = array_keys($companyFields);
        $contactFilterSpecs = array_values(array_filter($filterSpecs, fn ($s) => in_array($s->field, $contactFieldKeys, true)));
        $companyFilterSpecs = array_values(array_filter($filterSpecs, fn ($s) => in_array($s->field, $companyFieldKeys, true)));

        // Queries base para construir opciones (multi-select)
        $baseContacts = Contact::query();
        if (! $isAdmin && $userId) {
            $baseContacts->where('created_by', $userId)
                ->where('approval_status', 'aprobado');
        }

        $baseCompanies = Company::query();
        if (! $isAdmin) {
            $baseCompanies->aprobados();
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
        $contactFields['departamento']['options'] = $distinctToOptions((clone $baseContacts), 'departamento', 200);
        $contactFields['puesto_de_trabajo']['options'] = $distinctToOptions((clone $baseContacts), 'puesto_de_trabajo', 200);

        // municipio/estado: unimos opciones de contactos y empresas para que "sea por igual"
        $municipioOptsContacts = array_keys($distinctToOptions((clone $baseContacts), 'municipio', 200));
        $municipioOptsCompanies = array_keys($distinctToOptions((clone $baseCompanies), 'municipio', 200));
        $municipioOpts = collect(array_merge($municipioOptsContacts, $municipioOptsCompanies))
            ->unique()
            ->values()
            ->mapWithKeys(fn ($v) => [(string)$v => (string)$v])
            ->toArray();

        $estadoOptsContacts = array_keys($distinctToOptions((clone $baseContacts), 'estado', 200));
        $estadoOptsCompanies = array_keys($distinctToOptions((clone $baseCompanies), 'estado', 200));
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
            $comercialQuery->whereHas('contacts', function ($q) use ($userId) {
                $q->where('created_by', $userId)
                    ->where('approval_status', 'aprobado');
            });
        } elseif (! $isAdmin) {
            // Si no es admin, respetar que solo existan empresas aprobadas (ya lo contempla baseCompanies)
            $comercialQuery->whereIn('id', $baseCompanies->pluck('id'));
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

        // Opciones (empresas)
        $companyFields['sector']['options'] = $distinctToOptions((clone $baseCompanies), 'sector', 200);

        // Recálculo de referencia para que el formulario use las opciones actualizadas.
        $fields = array_merge($companyFields, $contactFields);

        $filtersForChips = collect($filterSpecs)->map(function ($spec) use ($fields, $operatorLabels) {
            $specArray = $spec instanceof \App\DataTransferObjects\FilterSpec ? $spec->toArray() : $spec;
            $fieldKey = $specArray['field'] ?? '';
            $cfg = $fields[$fieldKey] ?? null;
            return [
                'field' => $fieldKey,
                'operator' => $specArray['operator'] ?? '',
                'value' => $specArray['value'] ?? null,
                'field_label' => $cfg['label'] ?? $fieldKey,
                'operator_label' => $operatorLabels[$specArray['operator'] ?? ''] ?? $specArray['operator'] ?? '',
            ];
        })->all();

        // Ejecutar filtros sobre ambas entidades (mismo set de specs, pero filtrados por entidad)
        $contactsQuery = Contact::query()->with('company');
        if (! $isAdmin && $userId) {
            $contactsQuery->where('created_by', $userId)
                ->where('approval_status', 'aprobado');
        }
        $filterService->applyToContactQuery($contactsQuery, $contactFilterSpecs, $filterLogic);
        $contacts = $contactsQuery->latest()->paginate(20)->appends($request->except('_token'));

        $companiesQuery = Company::with('contacts');
        if (! $isAdmin) {
            $companiesQuery->aprobados();
        }
        $filterService->applyToCompanyQuery($companiesQuery, $companyFilterSpecs, $filterLogic);
        $companies = $companiesQuery->latest()->paginate(20)->appends($request->except('_token'));

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
        $filterService = app(DynamicFilterService::class);

        $contactFields = FilterConfig::contactFieldsWithOptions();
        $companyFields = FilterConfig::companyFields();
        $operatorLabels = FilterConfig::allOperatorLabels();

        $fields = array_merge($companyFields, $contactFields);

        $contactFieldKeys = array_merge(array_keys($contactFields), ['sector']);
        $companyFieldKeys = array_keys($companyFields);
        $contactFilterSpecs = array_values(array_filter($filterSpecs, fn ($s) => in_array($s->field, $contactFieldKeys, true)));
        $companyFilterSpecs = array_values(array_filter($filterSpecs, fn ($s) => in_array($s->field, $companyFieldKeys, true)));

        $filtersForChips = collect($filterSpecs)->map(function ($spec) use ($fields, $operatorLabels) {
            $specArray = $spec instanceof \App\DataTransferObjects\FilterSpec ? $spec->toArray() : $spec;
            $fieldKey = $specArray['field'] ?? '';
            $cfg = $fields[$fieldKey] ?? null;

            return [
                'field' => $fieldKey,
                'operator' => $specArray['operator'] ?? '',
                'value' => $specArray['value'] ?? null,
                'field_label' => $cfg['label'] ?? $fieldKey,
                'operator_label' => $operatorLabels[$specArray['operator'] ?? ''] ?? $specArray['operator'] ?? '',
            ];
        })->all();

        $contactsQuery = Contact::query()->with('company');
        if (! $isAdmin && $userId) {
            $contactsQuery->where('created_by', $userId)
                ->where('approval_status', 'aprobado');
        }
        $filterService->applyToContactQuery($contactsQuery, $contactFilterSpecs, $filterLogic);
        $contacts = $contactsQuery->latest()->paginate(20)->appends($request->except('_token'));

        $companiesQuery = Company::with('contacts');
        if (! $isAdmin) {
            $companiesQuery->aprobados();
        }
        $filterService->applyToCompanyQuery($companiesQuery, $companyFilterSpecs, $filterLogic);
        $companies = $companiesQuery->latest()->paginate(20)->appends($request->except('_token'));

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
            ])->render(),
        ]);
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

        if (!auth()->user()->esAdmin()) {
            $query->aprobados();
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
