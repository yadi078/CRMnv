<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo FollowUp - Seguimientos y Alertas
 * 
 * Gestiona la bitácora de notas y sistema de alarmas programadas
 * para llamadas, reuniones y cierres.
 */
class FollowUp extends Model
{
    use SoftDeletes;

    /**
     * Campos que pueden ser asignados masivamente
     */
    protected $fillable = [
        'company_id',
        'contact_id',
        'tipo_accion',
        'fecha_alarma',
        'bitacora_notas',
        'completado',
        'completado_at',
        'created_by',
        'asignado_a',
    ];

    /**
     * Casts de atributos
     */
    protected function casts(): array
    {
        return [
            'fecha_alarma' => 'datetime',
            'completado' => 'boolean',
            'completado_at' => 'datetime',
        ];
    }

    /**
     * Relación: Un seguimiento pertenece a una empresa (opcional)
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación: Un seguimiento pertenece a un contacto (opcional)
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Relación: Usuario que creó el seguimiento
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación: Usuario asignado para realizar la acción
     */
    public function asignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    /**
     * Scope: Filtrar seguimientos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('completado', false)
            ->where('fecha_alarma', '<=', now());
    }

    /**
     * Scope: Filtrar seguimientos completados
     */
    public function scopeCompletados($query)
    {
        return $query->where('completado', true);
    }

    /**
     * Marcar seguimiento como completado
     */
    public function completar(): void
    {
        $this->update([
            'completado' => true,
            'completado_at' => now(),
        ]);

        // Actualizar semáforo de la empresa si existe
        if ($this->company) {
            $this->company->actualizarSemáforo();
        }
    }

    /**
     * Verifica si el seguimiento está vencido
     */
    public function estaVencido(): bool
    {
        return !$this->completado && $this->fecha_alarma < now();
    }
}
