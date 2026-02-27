<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo Sale - Historial de Ventas
 *
 * Registro de cursos y servicios vendidos por empresa.
 * Permite mantener el historial comercial actualizado.
 */
class Sale extends Model
{
    protected $fillable = [
        'company_id',
        'nombre_servicio',
        'fecha_venta',
        'monto',
        'tipo_pago',
        'participantes',
        'notas',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'fecha_venta' => 'date',
            'monto' => 'decimal:2',
        ];
    }

    /**
     * Relación: Una venta pertenece a una empresa
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación: Usuario que registró la venta
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Etiquetas para tipo de pago
     */
    public const TIPO_PAGO_LABELS = [
        'efectivo' => 'Efectivo',
        'transferencia' => 'Transferencia',
        'tarjeta_credito' => 'Tarjeta de crédito',
        'tarjeta_debito' => 'Tarjeta de débito',
        'cheque' => 'Cheque',
        'deposito' => 'Depósito',
        'otro' => 'Otro',
    ];

    public function getTipoPagoLabelAttribute(): ?string
    {
        return $this->tipo_pago ? (self::TIPO_PAGO_LABELS[$this->tipo_pago] ?? $this->tipo_pago) : null;
    }

    /**
     * Formatea el monto para mostrar
     */
    public function getMontoFormateadoAttribute(): string
    {
        if ($this->monto === null) {
            return '—';
        }
        return '$ ' . number_format((float) $this->monto, 2, '.', ',');
    }
}
