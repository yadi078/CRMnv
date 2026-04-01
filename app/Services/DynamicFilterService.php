<?php

namespace App\Services;

use App\DataTransferObjects\FilterSpec;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Aplica filtros dinámicos a queries de Contact y Company.
 * Soporta AND/OR entre filtros.
 */
class DynamicFilterService
{
    /**
     * Valores del filtro Comercial: IDs de usuario ("123") o texto de ficha ("E:".base64).
     *
     * @param  list<mixed>  $values
     * @return array{0: list<int>, 1: list<string>, 2: list<string>} ids, textos libres, nombres de usuario (ids) para coincidir en ejecutivo_asignado
     */
    public static function splitComercialFilterValues(array $values): array
    {
        $ids = [];
        $texts = [];
        foreach ($values as $v) {
            $s = is_string($v) ? trim($v) : (string) $v;
            if ($s === '') {
                continue;
            }
            if (str_starts_with($s, 'E:')) {
                $decoded = base64_decode(substr($s, 2), true);
                if ($decoded !== false && $decoded !== '') {
                    $texts[] = $decoded;
                }

                continue;
            }
            if (ctype_digit($s)) {
                $id = (int) $s;
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
        }

        $ids = array_values(array_unique($ids));
        $texts = array_values(array_unique($texts));
        $userNames = $ids === []
            ? []
            : User::query()->whereIn('id', $ids)->pluck('name')->filter()->unique()->values()->all();

        return [$ids, $texts, $userNames];
    }

    /**
     * Cadenas que deben coincidir con companies.ejecutivo_asignado (incluye nombre de usuario y textos de catálogo).
     *
     * @param  list<string>  $userNames
     * @param  list<string>  $freeTexts
     * @return list<string>
     */
    public static function comercialEjecutivoAsignadoStrings(array $userNames, array $freeTexts): array
    {
        return array_values(array_unique(array_merge($userNames, $freeTexts)));
    }

    public function applyToContactQuery(Builder $query, array $filterSpecs, string $logic = 'and'): Builder
    {
        $valid = array_filter(array_map(fn ($f) => $f instanceof FilterSpec ? $f : FilterSpec::fromArray($f), $filterSpecs), fn (FilterSpec $s) => $s->isValid());

        if (empty($valid)) {
            return $query;
        }

        $method = strtolower($logic) === 'or' ? 'orWhere' : 'where';
        $query->where(function (Builder $q) use ($valid, $method) {
            $first = true;
            foreach ($valid as $spec) {
                $m = $first ? 'where' : $method;
                $this->applyContactFilter($q, $spec, $m);
                $first = false;
            }
        });

        return $query;
    }

    public function applyToCompanyQuery(Builder $query, array $filterSpecs, string $logic = 'and'): Builder
    {
        $valid = array_filter(array_map(fn ($f) => $f instanceof FilterSpec ? $f : FilterSpec::fromArray($f), $filterSpecs), fn (FilterSpec $s) => $s->isValid());

        if (empty($valid)) {
            return $query;
        }

        $method = strtolower($logic) === 'or' ? 'orWhere' : 'where';
        $query->where(function (Builder $q) use ($valid, $method) {
            $first = true;
            foreach ($valid as $spec) {
                $m = $first ? 'where' : $method;
                $this->applyCompanyFilter($q, $spec, $m);
                $first = false;
            }
        });

        return $query;
    }

    protected function applyContactFilter(Builder $q, FilterSpec $s, string $method): void
    {
        $col = $s->field;
        $val = $s->value;
        $op = $s->operator;

        // Sector (Giro) vive en companies; filtramos contactos por la empresa relacionada.
        if ($col === 'sector') {
            $values = is_array($val) ? $val : [$val];
            $values = array_values(array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : $v, $values), fn ($v) => $v !== null && $v !== ''));

            if ($op === 'is_empty') {
                $q->{$method}(function (Builder $b) {
                    $b->whereDoesntHave('company');
                    $b->orWhereHas('company', function (Builder $c) {
                        $c->whereNull('sector')->orWhere('sector', '');
                    });
                });
                return;
            }

            if ($op === 'is_not_empty') {
                $q->{$method}(function (Builder $b) {
                    $b->whereHas('company', function (Builder $c) {
                        $c->whereNotNull('sector')->where('sector', '!=', '');
                    });
                });
                return;
            }

            if ($op === 'equals') {
                $q->{$method}(function (Builder $b) use ($values) {
                    $b->whereHas('company', function (Builder $c) use ($values) {
                        $c->whereIn('sector', $values);
                    });
                });
                return;
            }

            if ($op === 'not_equals') {
                $q->{$method}(function (Builder $b) use ($values) {
                    $b->whereDoesntHave('company')
                        ->orWhereHas('company', function (Builder $c) use ($values) {
                            $c->whereNotIn('sector', $values);
                        });
                });
                return;
            }
        }

        // Comercial = ejecutivo asignado: IDs de usuario y/o texto de empresa.ejecutivo_asignado (p. ej. "Lic. Olivia…").
        if ($col === 'comercial') {
            $values = is_array($val) ? $val : [$val];
            $values = array_values(array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : $v, $values), fn ($v) => $v !== null && $v !== ''));
            [$ids, $freeTexts, $userNames] = self::splitComercialFilterValues($values);
            $matchStrings = self::comercialEjecutivoAsignadoStrings($userNames, $freeTexts);

            if ($op === 'is_empty') {
                $q->{$method}(function (Builder $b) {
                    $b->whereNull('assigned_user_id')
                        ->where(function (Builder $b2) {
                            $b2->whereDoesntHave('company')
                                ->orWhereHas('company', function (Builder $c) {
                                    $c->whereNull('assigned_user_id')
                                        ->where(function (Builder $c2) {
                                            $c2->whereNull('ejecutivo_asignado')->orWhere('ejecutivo_asignado', '');
                                        });
                                });
                        });
                });

                return;
            }

            if ($op === 'is_not_empty') {
                $q->{$method}(function (Builder $b) {
                    $b->whereNotNull('assigned_user_id')
                        ->orWhereHas('company', function (Builder $c) {
                            $c->whereNotNull('assigned_user_id')
                                ->orWhere(function (Builder $c2) {
                                    $c2->whereNotNull('ejecutivo_asignado')->where('ejecutivo_asignado', '!=', '');
                                });
                        });
                });

                return;
            }

            if ($op === 'equals') {
                if ($ids === [] && $matchStrings === []) {
                    return;
                }

                $q->{$method}(function (Builder $b) use ($ids, $matchStrings) {
                    $b->where(function (Builder $inner) use ($ids, $matchStrings) {
                        if ($ids !== []) {
                            $inner->where(function (Builder $w) use ($ids) {
                                $w->whereIn('assigned_user_id', $ids)
                                    ->orWhereHas('company', function (Builder $c) use ($ids) {
                                        $c->whereIn('assigned_user_id', $ids);
                                    });
                            });
                        }
                        if ($matchStrings !== []) {
                            $inner->orWhereHas('company', function (Builder $c) use ($matchStrings) {
                                $c->whereIn('ejecutivo_asignado', $matchStrings);
                            });
                        }
                    });
                });

                return;
            }

            if ($op === 'not_equals') {
                if ($ids === [] && $matchStrings === []) {
                    return;
                }

                $q->{$method}(function (Builder $b) use ($ids, $matchStrings) {
                    $b->whereNot(function (Builder $inner) use ($ids, $matchStrings) {
                        $inner->where(function (Builder $w) use ($ids, $matchStrings) {
                            if ($ids !== []) {
                                $w->where(function (Builder $w2) use ($ids) {
                                    $w2->whereIn('assigned_user_id', $ids)
                                        ->orWhereHas('company', function (Builder $c) use ($ids) {
                                            $c->whereIn('assigned_user_id', $ids);
                                        });
                                });
                            }
                            if ($matchStrings !== []) {
                                $w->orWhereHas('company', function (Builder $c) use ($matchStrings) {
                                    $c->whereIn('ejecutivo_asignado', $matchStrings);
                                });
                            }
                        });
                    });
                });

                return;
            }
        }

        // Domicilio: calle_numero o colonia_cp
        if ($col === 'domicilio') {
            if ($op === 'equals') {
                $values = is_array($val) ? $val : [$val];
                $values = array_values(array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : $v, $values), fn ($v) => $v !== null && $v !== ''));
                $hasCon = in_array('con_domicilio', $values, true);
                $hasSin = in_array('sin_domicilio', $values, true);
                if ($hasCon && $hasSin) {
                    return;
                }
                if ($hasCon) {
                    $op = 'is_not_empty';
                } elseif ($hasSin) {
                    $op = 'is_empty';
                }
            }

            if ($op === 'is_empty') {
                $q->{$method}(function (Builder $b) {
                    $b->where(function ($b2) {
                        $b2->whereNull('calle_numero')->orWhere('calle_numero', '');
                    })->where(function ($b2) {
                        $b2->whereNull('colonia_cp')->orWhere('colonia_cp', '');
                    });
                });
            } elseif ($op === 'is_not_empty') {
                $q->{$method}(function (Builder $b) {
                    $b->whereNotNull('calle_numero')->where('calle_numero', '!=', '')
                        ->orWhereNotNull('colonia_cp')->where('colonia_cp', '!=', '');
                });
            }
            return;
        }

        // Checkbox: no_recibir_correos -> email_activo = false
        if ($col === 'no_recibir_correos') {
            $rawValue = is_array($val) ? reset($val) : $val;
            $normalized = is_string($rawValue) ? strtolower(trim($rawValue)) : $rawValue;
            $wantNoRecibir = in_array($normalized, ['1', 1, true, 'si', 'sí'], true);
            $q->{$method}('email_activo', $wantNoRecibir ? false : true);
            return;
        }

        // Contact-specific: has/not has phone, cell, email
        if ($op === 'has_value') {
            $q->{$method}(function (Builder $b) use ($col) {
                $b->whereNotNull($col)->where($col, '!=', '');
            });
            return;
        }
        if ($op === 'no_value') {
            $q->{$method}(function (Builder $b) use ($col) {
                $b->whereNull($col)->orWhere($col, '');
            });
            return;
        }

        // Boolean (email_activo) - compatibilidad (por si algún filtro antiguo usa este campo)
        if ($col === 'email_activo') {
            $q->{$method}('email_activo', $val === '1' || $val === true);
            return;
        }

        $this->applyGenericOperator($q, $col, $op, $val, $method);
    }

    protected function applyCompanyFilter(Builder $q, FilterSpec $s, string $method): void
    {
        $col = $s->field;
        $val = $s->value;
        $op = $s->operator;

        // Comercial = ejecutivo asignado (empresa): IDs y/o texto en ejecutivo_asignado
        if ($col === 'comercial') {
            $values = is_array($val) ? $val : [$val];
            $values = array_values(array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : $v, $values), fn ($v) => $v !== null && $v !== ''));
            [$ids, $freeTexts, $userNames] = self::splitComercialFilterValues($values);
            $matchStrings = self::comercialEjecutivoAsignadoStrings($userNames, $freeTexts);

            if ($op === 'is_empty') {
                $q->{$method}(function (Builder $b) {
                    $b->whereNull('assigned_user_id')
                        ->where(function (Builder $b2) {
                            $b2->whereNull('ejecutivo_asignado')->orWhere('ejecutivo_asignado', '');
                        });
                });

                return;
            }

            if ($op === 'is_not_empty') {
                $q->{$method}(function (Builder $b) {
                    $b->whereNotNull('assigned_user_id')
                        ->orWhere(function (Builder $b2) {
                            $b2->whereNotNull('ejecutivo_asignado')->where('ejecutivo_asignado', '!=', '');
                        });
                });

                return;
            }

            if ($op === 'equals') {
                if ($ids === [] && $matchStrings === []) {
                    return;
                }

                $q->{$method}(function (Builder $b) use ($ids, $matchStrings) {
                    $b->where(function (Builder $inner) use ($ids, $matchStrings) {
                        if ($ids !== []) {
                            $inner->whereIn('assigned_user_id', $ids);
                        }
                        if ($matchStrings !== []) {
                            $inner->orWhereIn('ejecutivo_asignado', $matchStrings);
                        }
                    });
                });

                return;
            }

            if ($op === 'not_equals') {
                if ($ids === [] && $matchStrings === []) {
                    return;
                }

                $q->{$method}(function (Builder $b) use ($ids, $matchStrings) {
                    $b->whereNot(function (Builder $inner) use ($ids, $matchStrings) {
                        $inner->where(function (Builder $in2) use ($ids, $matchStrings) {
                            if ($ids !== []) {
                                $in2->whereIn('assigned_user_id', $ids);
                            }
                            if ($matchStrings !== []) {
                                $in2->orWhereIn('ejecutivo_asignado', $matchStrings);
                            }
                        });
                    });
                });

                return;
            }
        }

        if ($col === 'datos_fiscales' && in_array($op, ['is_empty', 'is_not_empty'], true)) {
            if ($op === 'is_empty') {
                $q->{$method}(function (Builder $b) {
                    $b->whereNull('datos_fiscales')->orWhere('datos_fiscales', '');
                });
            } else {
                $q->{$method}(function (Builder $b) {
                    $b->whereNotNull('datos_fiscales')->where('datos_fiscales', '!=', '');
                });
            }
            return;
        }

        if ($col === 'datos_fiscales' && $op === 'equals') {
            $values = is_array($val) ? $val : [$val];
            $values = array_values(array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : $v, $values), fn ($v) => $v !== null && $v !== ''));
            $hasCon = in_array('con_domicilio', $values, true);
            $hasSin = in_array('sin_domicilio', $values, true);

            if ($hasCon && $hasSin) {
                return;
            }

            if ($hasCon) {
                $q->{$method}(function (Builder $b) {
                    $b->whereNotNull('datos_fiscales')->where('datos_fiscales', '!=', '');
                });
                return;
            }

            if ($hasSin) {
                $q->{$method}(function (Builder $b) {
                    $b->whereNull('datos_fiscales')->orWhere('datos_fiscales', '');
                });
                return;
            }
        }

        $this->applyGenericOperator($q, $col, $op, $val, $method);
    }

    protected function applyGenericOperator(Builder $q, string $column, string $operator, mixed $value, string $method): void
    {
        if (is_array($value)) {
            $safeVal = array_values(array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : $v, $value), fn ($v) => $v !== null && $v !== ''));
        } else {
            $safeVal = is_string($value) ? trim($value) : $value;
        }

        switch ($operator) {
            case 'contains':
                if (is_array($safeVal)) {
                    $q->{$method}(function (Builder $b) use ($column, $safeVal) {
                        foreach ($safeVal as $v) {
                            $b->orWhere($column, 'like', '%' . $v . '%');
                        }
                    });
                } else {
                    $q->{$method}($column, 'like', '%' . $safeVal . '%');
                }
                break;
            case 'not_contains':
                if (is_array($safeVal)) {
                    $q->{$method}(function (Builder $b) use ($column, $safeVal) {
                        foreach ($safeVal as $v) {
                            $b->where($column, 'not like', '%' . $v . '%');
                        }
                        $b->orWhereNull($column);
                    });
                } else {
                    $q->{$method}(function (Builder $b) use ($column, $safeVal) {
                        $b->where($column, 'not like', '%' . $safeVal . '%')->orWhereNull($column);
                    });
                }
                break;
            case 'starts_with':
                if (is_array($safeVal)) {
                    $q->{$method}(function (Builder $b) use ($column, $safeVal) {
                        foreach ($safeVal as $v) {
                            $b->orWhere($column, 'like', $v . '%');
                        }
                    });
                } else {
                    $q->{$method}($column, 'like', $safeVal . '%');
                }
                break;
            case 'ends_with':
                if (is_array($safeVal)) {
                    $q->{$method}(function (Builder $b) use ($column, $safeVal) {
                        foreach ($safeVal as $v) {
                            $b->orWhere($column, 'like', '%' . $v);
                        }
                    });
                } else {
                    $q->{$method}($column, 'like', '%' . $safeVal);
                }
                break;
            case 'equals':
                if (is_array($safeVal)) {
                    // IN (OR interno por la naturaleza de IN)
                    $inMethod = $method . 'In'; // whereIn / orWhereIn
                    $q->{$inMethod}($column, $safeVal);
                } else {
                    $q->{$method}($column, '=', $safeVal);
                }
                break;
            case 'not_equals':
                if (is_array($safeVal)) {
                    $q->{$method}(function (Builder $b) use ($column, $safeVal) {
                        $b->whereNotIn($column, $safeVal)->orWhereNull($column);
                    });
                } else {
                    $q->{$method}(function (Builder $b) use ($column, $safeVal) {
                        $b->where($column, '!=', $safeVal)->orWhereNull($column);
                    });
                }
                break;
            case 'is_empty':
                $q->{$method}(function (Builder $b) use ($column) {
                    $b->whereNull($column)->orWhere($column, '');
                });
                break;
            case 'is_not_empty':
                $q->{$method}(function (Builder $b) use ($column) {
                    $b->whereNotNull($column)->where($column, '!=', '');
                });
                break;
            default:
                break;
        }
    }

    /**
     * Parsea filtros desde request (filter[0][field], filter[0][operator], filter[0][value] o filter[] JSON).
     */
    public static function parseFromRequest(\Illuminate\Http\Request $request, string $key = 'filters'): array
    {
        $raw = $request->input($key, $request->input('filter', []));

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }

        $specs = [];
        foreach ($raw as $i => $item) {
            if (!is_array($item)) {
                continue;
            }
            $spec = FilterSpec::fromArray($item);
            if ($spec->isValid()) {
                $specs[] = $spec;
            }
        }
        return $specs;
    }

    /**
     * Obtiene lógica AND/OR desde request.
     */
    public static function logicFromRequest(\Illuminate\Http\Request $request, string $key = 'filter_logic'): string
    {
        $v = strtolower((string) $request->input($key, 'and'));
        return $v === 'or' ? 'or' : 'and';
    }
}
