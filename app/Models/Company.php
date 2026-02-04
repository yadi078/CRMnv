<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Modelo Company - Empresas/Prospectos
 * 
 * Gestiona la información de empresas con sistema de semáforo,
 * validación de duplicados y aprobación de registros.
 */
class Company extends Model
{
    use SoftDeletes;

    /**
     * Campos que pueden ser asignados masivamente
     */
    protected $fillable = [
        'nombre_comercial',
        'rfc',
        'sector',
        'municipio',
        'estado',
        'ejecutivo_asignado',
        'datos_fiscales',
        'status_color',
        'approval_status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    /**
     * Casts de atributos
     */
    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Relación: Una empresa tiene muchos contactos
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Relación: Una empresa tiene muchos seguimientos
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
     * Relación: Administrador que aprobó el registro
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Calcula el semáforo automáticamente basado en la última actividad
     * 
     * Verde: Última actividad hace menos de 7 días
     * Amarillo: Última actividad hace entre 7 y 30 días
     * Rojo: Última actividad hace más de 30 días o sin actividad
     * 
     * @return string Color del semáforo
     */
    public function calcularSemáforo(): string
    {
        $ultimoSeguimiento = $this->followUps()
            ->where('completado', true)
            ->latest('completado_at')
            ->first();

        if (!$ultimoSeguimiento) {
            return 'rojo';
        }

        $diasDesdeUltimaActividad = Carbon::now()->diffInDays($ultimoSeguimiento->completado_at);

        if ($diasDesdeUltimaActividad <= 7) {
            return 'verde';
        } elseif ($diasDesdeUltimaActividad <= 30) {
            return 'amarillo';
        } else {
            return 'rojo';
        }
    }

    /**
     * Actualiza el semáforo automáticamente
     */
    public function actualizarSemáforo(): void
    {
        $this->update(['status_color' => $this->calcularSemáforo()]);
    }

    /**
     * Scope: Filtrar por estado de aprobación
     */
    public function scopePendientes($query)
    {
        return $query->where('approval_status', 'pendiente');
    }

    /**
     * Scope: Filtrar por estado de aprobación
     */
    public function scopeAprobados($query)
    {
        return $query->where('approval_status', 'aprobado');
    }

    /**
     * Scope: Filtrar por color de semáforo
     */
    public function scopePorColor($query, string $color)
    {
        return $query->where('status_color', $color);
    }

    /**
     * Verifica si el registro está pendiente de aprobación
     */
    public function estaPendiente(): bool
    {
        return $this->approval_status === 'pendiente';
    }

    /**
     * Aprobar el registro
     */
    public function aprobar(int $userId): void
    {
        $this->update([
            'approval_status' => 'aprobado',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }
}
