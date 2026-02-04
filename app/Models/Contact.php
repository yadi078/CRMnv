<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Contact - Contactos de empresas
 * 
 * Gestiona la información de contactos vinculados a empresas.
 * Permite generar PDF de Ficha de Inscripción.
 */
class Contact extends Model
{
    use SoftDeletes;

    /**
     * Campos que pueden ser asignados masivamente
     */
    protected $fillable = [
        'company_id',
        'nombre_completo',
        'puesto_de_trabajo',
        'departamento',
        'celular',
        'extension',
        'email',
        'municipio',
        'estado',
        'notas',
        'created_by',
    ];

    /**
     * Relación: Un contacto pertenece a una empresa
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación: Un contacto puede tener muchos seguimientos
     */
    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Relación: Usuario que creó el registro
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtiene el contacto principal de una empresa
     */
    public function esContactoPrincipal(): bool
    {
        return $this->company->contacts()->first()->id === $this->id;
    }
}
