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
     * Estados de prospecto (igual que empresas) para el semáforo.
     */
    public const PROSPECT_STATUS_LABELS = [
        'seguimiento' => 'Seguimiento',
        'interesado' => 'Interesado',
        'si_le_interesa_nos_llaman_o_no_compro' => 'Si le interesa nos llaman o no compro',
        'vendido' => 'Vendido',
        'no_estaba' => 'No estaba',
    ];

    /**
     * Campos que pueden ser asignados masivamente
     */
    protected $fillable = [
        'company_id',
        'nombre_completo',
        'genero',
        'puesto_de_trabajo',
        'departamento',
        'celular',
        'telefono',
        'extension',
        'email',
        'email_activo',
        'municipio',
        'estado',
        'razon_social',
        'nombre_comercial',
        'calle_numero',
        'colonia_cp',
        'rfc',
        'regimen_fiscal',
        'notas',
        'status_color',
        'created_by',
    ];

    /**
     * Casts de atributos
     */
    protected function casts(): array
    {
        return [
            'email_activo' => 'boolean',
        ];
    }

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
     * Relación: Ventas en las que este contacto figura como comprador
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Relación: Usuario que creó el registro
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Etiqueta legible del estado de prospecto
     */
    public function getStatusLabelAttribute(): string
    {
        $status = $this->status_color ?? 'seguimiento';
        return self::PROSPECT_STATUS_LABELS[$status] ?? ucfirst($status);
    }

    /**
     * Scope: filtrar por estado de prospecto (semáforo)
     */
    public function scopePorStatus($query, string $status)
    {
        return $query->where('status_color', $status);
    }
}
