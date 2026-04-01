<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class WorkArea extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Nombres para datalist en altas/edición de contactos.
     * Colección vacía si la tabla no existe (evita 500 en hosting con migración pendiente).
     *
     * @return Collection<int, string>
     */
    public static function namesForContactForms(): Collection
    {
        if (! Schema::hasTable((new static())->getTable())) {
            return collect();
        }

        return static::query()->orderBy('name')->pluck('name');
    }

    /**
     * Listado para el catálogo en perfil (admin).
     *
     * @return Collection<int, WorkArea>
     */
    public static function allOrderedForProfile(): Collection
    {
        if (! Schema::hasTable((new static())->getTable())) {
            return collect();
        }

        return static::query()->orderBy('name')->get();
    }

    /**
     * Reglas para el campo departamento: sin exists si no hay tabla work_areas.
     *
     * @return list<string|\Illuminate\Validation\Rules\Exists>
     */
    public static function validationRulesForDepartamentoField(): array
    {
        $base = ['nullable', 'string', 'max:255'];
        if (! Schema::hasTable((new static())->getTable())) {
            return $base;
        }

        return array_merge($base, [Rule::exists('work_areas', 'name')]);
    }
}
