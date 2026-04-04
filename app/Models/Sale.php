<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        'tipo_curso',
        'fecha_venta',
        'monto',
        'incluye_iva',
        'tipo_pago',
        'participantes',
        'participantes_texto',
        'notas',
        'colonia_cp',
        'regimen_fiscal',
        'forma_pago',
        'uso_cfdi',
        'orden_compra',
        'facturacion_calle_numero',
        'facturacion_rfc',
        'email_facturacion',
        'condiciones_pago',
        'modalidad',
        'sede',
        'fecha_evento',
        'horario_evento',
        'factura_referencia',
        'created_by',
        'nombre_consultor',
    ];

    protected function casts(): array
    {
        return [
            'fecha_venta' => 'date',
            'fecha_evento' => 'date',
            'monto' => 'decimal:2',
            'incluye_iva' => 'boolean',
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
     * Nombre del consultor/ejecutivo para la ficha (guardado al crear la venta; respaldo: usuario creador).
     */
    public function nombreConsultorParaFicha(): string
    {
        $n = trim((string) ($this->nombre_consultor ?? ''));
        if ($n !== '') {
            return $n;
        }
        $fromCreator = trim((string) ($this->creator?->name ?? ''));

        return $fromCreator !== '' ? $fromCreator : '—';
    }

    /**
     * Etiquetas para tipo de pago
     */
    public const TIPO_PAGO_LABELS = [
        'oxxo' => 'OXXO',
        'bancoppel_tarjeta' => 'Bancoppel (tarjeta)',
        'spei' => 'Transferencia SPEI',
        'tarjeta_debito' => 'Nueva tarjeta de débito',
        'tarjeta_credito' => 'Nueva tarjeta de crédito',
        'efectivo_puntos_pago' => 'Efectivo en puntos de pago',
        'efectivo' => 'Efectivo',
        'transferencia' => 'Transferencia',
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

    /**
     * Nombre del curso tal como debe imprimirse en la ficha (no muestra el texto automático de venta desde contacto).
     */
    public function getNombreCursoFichaAttribute(): string
    {
        $n = trim((string) $this->nombre_servicio);
        if ($n === '') {
            return '—';
        }
        if (Str::startsWith($n, 'Venta desde contacto:')) {
            return '—';
        }

        return $n;
    }

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

    /** Calle y número para ficha/PDF: valor guardado en venta o contacto/empresa. */
    public function calleNumeroFacturacionResuelto(): string
    {
        $raw = $this->facturacion_calle_numero;
        if ($raw !== null && trim((string) $raw) !== '') {
            return trim((string) $raw);
        }
        if ($raw !== null && trim((string) $raw) === '') {
            return '—';
        }
        $fb = trim((string) ($this->contact?->calle_numero ?? ''));
        if ($fb === '') {
            $fb = trim((string) ($this->company?->datos_fiscales ?? ''));
        }

        return $fb !== '' ? $fb : '—';
    }

    /** RFC para ficha/PDF. */
    public function rfcFacturacionResuelto(): string
    {
        $raw = $this->facturacion_rfc;
        if ($raw !== null && trim((string) $raw) !== '') {
            return trim((string) $raw);
        }
        if ($raw !== null && trim((string) $raw) === '') {
            return '—';
        }
        $fb = trim((string) ($this->contact?->rfc ?? $this->company?->rfc ?? ''));

        return $fb !== '' ? $fb : '—';
    }

    /** Correo de facturación: guardado o contacto. */
    public function emailFacturacionResuelto(): string
    {
        $raw = $this->email_facturacion;
        if ($raw !== null && trim((string) $raw) !== '') {
            return trim((string) $raw);
        }
        if ($raw !== null && trim((string) $raw) === '') {
            return '—';
        }
        $fb = trim((string) ($this->contact?->email ?? ''));

        return $fb !== '' ? $fb : '—';
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
