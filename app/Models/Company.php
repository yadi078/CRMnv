<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'motivo_rechazo',
        'deletion_pending',
        'deletion_requested_by',
        'deletion_requested_at',
        'deletion_resolution',
        'deletion_resolution_note',
        'deletion_resolved_at',
        'deletion_resolved_by',
        'deletion_decision_user_id',
    ];

    /**
     * Casts de atributos
     */
    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'deletion_pending' => 'boolean',
            'deletion_requested_at' => 'datetime',
            'deletion_resolved_at' => 'datetime',
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
     * Relación: Una empresa tiene muchas ventas (historial de ventas)
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Relación: Una empresa tiene muchos usuarios
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
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
     * Usuario que solicitó eliminar la empresa (pendiente de decisión del admin).
     */
    public function deletionRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deletion_requested_by');
    }

    /**
     * Estados de prospecto disponibles
     */
    public const PROSPECT_STATUSES = [
        'seguimiento',
        'interesado',
        'si_le_interesa_nos_llaman_o_no_compro',
        'vendido',
        'no_estaba',
    ];

    /**
     * Etiquetas legibles para cada estado de prospecto
     */
    public const PROSPECT_STATUS_LABELS = [
        'seguimiento' => 'Seguimiento',
        'interesado' => 'Interesado',
        'si_le_interesa_nos_llaman_o_no_compro' => 'Si le interesa nos llaman o no compro',
        'vendido' => 'Vendido',
        'no_estaba' => 'No estaba',
    ];

    /**
     * Obtiene la etiqueta legible del estado de prospecto
     */
    public function getStatusLabelAttribute(): string
    {
        return self::PROSPECT_STATUS_LABELS[$this->status_color] ?? ucfirst($this->status_color ?? '');
    }

    /**
     * Scope: Filtrar por estado de aprobación
     */
    public function scopePendientes($query)
    {
        return $query->where('approval_status', 'pendiente');
    }

    /**
     * Cola de solicitudes para el panel de aprobaciones: altas pendientes o bajas solicitadas.
     */
    public function scopePendientesAprobacion($query)
    {
        return $query->where(function ($q) {
            $q->where('approval_status', 'pendiente')
                ->orWhere('deletion_pending', true);
        });
    }

    /**
     * Scope: Filtrar por estado de aprobación
     */
    public function scopeAprobados($query)
    {
        return $query->where('approval_status', 'aprobado');
    }

    /**
     * Scope: Empresas aprobadas ordenadas por nombre comercial
     */
    public function scopeAprobadosOrdenados($query)
    {
        return $query->aprobados()->orderBy('nombre_comercial');
    }

    /**
     * Alcance para ejecutivos: empresas que registraron o todas las aprobadas en el CRM.
     * Los administradores no se filtran aquí.
     */
    public function scopeAccessibleForExecutive(Builder $query, User $user): Builder
    {
        if ($user->esAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
                ->orWhere('approval_status', 'aprobado');
        });
    }

    /**
     * Empresas disponibles al crear/editar un contacto (ejecutivo):
     * las que él registró (cualquier estado de aprobación) + todas las empresas ya aprobadas en el CRM.
     */
    public static function forExecutiveContactForm(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->esAdmin()) {
            return static::aprobadosOrdenados()->get();
        }

        return static::query()
            ->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhere('approval_status', 'aprobado');
            })
            ->orderBy('nombre_comercial')
            ->get();
    }

    /**
     * Empresas para seguimientos y ventas: propias o aprobadas con contacto propio.
     */
    public static function forExecutiveFollowUpAndSales(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->esAdmin()) {
            return static::aprobadosOrdenados()->get();
        }

        return static::query()
            ->accessibleForExecutive($user)
            ->orderBy('nombre_comercial')
            ->get();
    }

    /**
     * Scope: Filtrar por estado de prospecto (status_color)
     */
    public function scopePorStatus($query, string $status)
    {
        return $query->where('status_color', $status);
    }

    /**
     * Scope: Filtrar por color/estado (alias para compatibilidad)
     */
    public function scopePorColor($query, string $status)
    {
        return $query->where('status_color', $status);
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

    /**
     * Denegar la solicitud de la empresa
     */
    public function denegar(int $userId, ?string $motivo = null): void
    {
        $this->update([
            'approval_status' => 'rechazado',
            'approved_by' => $userId,
            'approved_at' => now(),
            'motivo_rechazo' => $motivo,
        ]);
    }

    /**
     * Si el ejecutivo puede ver/usar esta empresa: las que registró (aunque pendientes)
     * o cualquier empresa ya aprobada en el CRM (p. ej. para agregar contactos o ver ficha).
     */
    public function isAccessibleByExecutive(User $user): bool
    {
        if ($user->esAdmin()) {
            return true;
        }
        if ((int) $this->created_by === (int) $user->id) {
            return true;
        }

        return $this->approval_status === 'aprobado';
    }
}
