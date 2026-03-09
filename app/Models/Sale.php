<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'contact_id',
        'nombre_servicio',
        'fecha_venta',
        'monto',
        'tipo_pago',
        'participantes',
        'notas',
        'colonia_cp',
        'regimen_fiscal',
        'forma_pago',
        'uso_cfdi',
        'orden_compra',
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
     * Relación: Contacto que compró (opcional)
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Relación: Participantes de la venta (nombres y correos)
     */
    public function saleParticipants(): HasMany
    {
        return $this->hasMany(SaleParticipant::class)->orderBy('orden');
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

    /** Opciones para forma de pago (desplegable en ficha) */
    public const FORMA_DE_PAGO_LABELS = [
        'efectivo' => 'Efectivo',
        'transferencia' => 'Transferencia electrónica',
        'tarjeta_credito' => 'Tarjeta de crédito',
        'tarjeta_debito' => 'Tarjeta de débito',
        'cheque' => 'Cheque',
        'deposito' => 'Depósito',
        'monedero_electronico' => 'Monedero electrónico',
        'otro' => 'Otro',
    ];

    /** Opciones para orden de compra (desplegable) */
    public const ORDEN_COMPRA_LABELS = [
        'con_orden' => 'Con orden de compra',
        'sin_orden' => 'Sin orden de compra',
        'pendiente' => 'Pendiente',
        'na' => 'N/A',
        'otro' => 'Otro',
    ];

    public function getTipoPagoLabelAttribute(): ?string
    {
        return $this->tipo_pago ? (self::TIPO_PAGO_LABELS[$this->tipo_pago] ?? $this->tipo_pago) : null;
    }

    /** Etiqueta legible de forma de pago para la ficha */
    public function getFormaPagoLabelAttribute(): ?string
    {
        if (empty($this->forma_pago)) {
            return null;
        }
        return self::FORMA_DE_PAGO_LABELS[$this->forma_pago] ?? $this->forma_pago;
    }

    /** Etiqueta legible de orden de compra para la ficha */
    public function getOrdenCompraLabelAttribute(): ?string
    {
        if (empty($this->orden_compra)) {
            return null;
        }
        return self::ORDEN_COMPRA_LABELS[$this->orden_compra] ?? $this->orden_compra;
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
