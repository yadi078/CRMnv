<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
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
     * Etiquetas breves para tablas (evita desbordes en columnas estrechas).
     */
    public const PROSPECT_STATUS_SHORT_LABELS = [
        'seguimiento' => 'Seguimiento',
        'interesado' => 'Interesado',
        'si_le_interesa_nos_llaman_o_no_compro' => 'Llama / no compró',
        'vendido' => 'Vendido',
        'no_estaba' => 'No estaba',
    ];

    /**
     * Campos que pueden ser asignados masivamente
     */
    protected $fillable = [
        'company_id',
        'assigned_user_id',
        'nombre_completo',
        'genero',
        'puesto_de_trabajo',
        'departamento',
        'celular',
        'telefono',
        'extension',
        'email',
        'email_activo',
        'fecha_cumpleanos',
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
        'approval_status',
        'approved_by',
        'approved_at',
        'motivo_rechazo',
        'deletion_pending',
        'deletion_requested_by',
        'deletion_requested_at',
        'deletion_reason',
        'deletion_resolution',
        'deletion_resolution_note',
        'deletion_resolved_at',
        'deletion_resolved_by',
        'deletion_decision_user_id',
        'created_by',
    ];

    /**
     * Casts de atributos
     */
    protected function casts(): array
    {
        return [
            'email_activo' => 'boolean',
            'fecha_cumpleanos' => 'date',
            'approved_at' => 'datetime',
            'deletion_pending' => 'boolean',
            'deletion_requested_at' => 'datetime',
            'deletion_resolved_at' => 'datetime',
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
     * Ejecutivo asignado al contacto (gestión admin).
     */
    public function assignedExecutive(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
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
     * Relación: Administrador que aprobó el contacto
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relación: Usuario que solicitó la eliminación del contacto.
     */
    public function deletionRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deletion_requested_by');
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
     * Etiqueta corta de estado para listados (tooltip puede usar status_label completo).
     */
    public function getStatusLabelShortAttribute(): string
    {
        $status = $this->status_color ?? 'seguimiento';
        if (isset(self::PROSPECT_STATUS_SHORT_LABELS[$status])) {
            return self::PROSPECT_STATUS_SHORT_LABELS[$status];
        }
        return Str::limit($this->status_label, 28);
    }

    /**
     * Scope: filtrar por estado de prospecto (semáforo)
     */
    public function scopePorStatus($query, string $status)
    {
        return $query->where('status_color', $status);
    }

    /**
     * Scope: contactos pendientes de aprobación
     */
    public function scopePendientes($query)
    {
        return $query->where('approval_status', 'pendiente');
    }

    /**
     * Scope: contactos pendientes por alta o eliminación solicitada.
     */
    public function scopePendientesAprobacion($query)
    {
        return $query->where(function ($q) {
            $q->where('approval_status', 'pendiente')
                ->orWhere('deletion_pending', true);
        });
    }

    /**
     * Scope: contactos aprobados
     */
    public function scopeAprobados($query)
    {
        return $query->where('approval_status', 'aprobado');
    }

    /**
     * Alcance para ejecutivos: contactos asignados por administración o registrados por el usuario.
     */
    public function scopeAccessibleForExecutive(Builder $query, User $user): Builder
    {
        if ($user->esAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('assigned_user_id', $user->id)
                ->orWhere('created_by', $user->id);
        });
    }

    /**
     * Aprobar contacto
     */
    public function aprobar(int $userId): void
    {
        $this->update([
            'approval_status' => 'aprobado',
            'approved_by' => $userId,
            'approved_at' => now(),
            'motivo_rechazo' => null,
            'deletion_pending' => false,
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
            'deletion_reason' => null,
        ]);
    }

    /**
     * Denegar contacto
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
}

