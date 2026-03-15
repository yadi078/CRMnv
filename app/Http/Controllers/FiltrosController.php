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

        $tab = $request->get('tab', 'contactos');
        $filterLogic = DynamicFilterService::logicFromRequest($request);
        $filterSpecs = DynamicFilterService::parseFromRequest($request);
        $contacts = collect();
        $companies = collect();

        $filterService = app(DynamicFilterService::class);

        if ($tab === 'contactos') {
            $query = Contact::query()->with('company');
            $filterService->applyToContactQuery($query, $filterSpecs, $filterLogic);
            $contacts = $query->latest()->paginate(20)->withQueryString();
        } else {
            $query = Company::with('contacts');
            if (!auth()->user()->esAdmin()) {
                $query->aprobados();
            }
            $filterService->applyToCompanyQuery($query, $filterSpecs, $filterLogic);
            $companies = $query->latest()->paginate(20)->withQueryString();
        }

        $contactFields = FilterConfig::contactFieldsWithOptions();
        $companyFields = FilterConfig::companyFields();
        $operatorLabels = FilterConfig::allOperatorLabels();
        $fields = $tab === 'contactos' ? $contactFields : $companyFields;

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

        $sectorOptions = Company::whereNotNull('sector')->orderBy('sector')->pluck('sector')->unique()->values();
        $estadoOptions = Company::whereNotNull('estado')->orderBy('estado')->pluck('estado')->unique()->values();
        $companiesList = Company::aprobadosOrdenados()->get();

        return view('filtros.index', [
            'contacts' => $contacts,
            'companies' => $companies,
            'tab' => $tab,
            'filterLogic' => $filterLogic,
            'filterSpecs' => $filterSpecs,
            'filtersForChips' => $filtersForChips,
            'contactFields' => $contactFields,
            'companyFields' => $companyFields,
            'operatorLabels' => $operatorLabels,
            'fields' => $fields,
            'sectorOptions' => $sectorOptions,
            'estadoOptions' => $estadoOptions,
            'companiesList' => $companiesList,
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
