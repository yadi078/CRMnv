<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha de Inscripción@if($sale->nombre_curso_ficha !== '—') - {{ $sale->nombre_curso_ficha }}@endif</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #1a1a1a;
            padding: 16px 20px 20px;
            font-size: 10px;
            line-height: 1.35;
        }
        /* Cabecera tipo plantilla: solo CONSULTOR y FECHA (sin logo, sin eslogan) */
        .header-meta-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .header-meta-table td { vertical-align: top; padding: 0; }
        .meta-right { text-align: right; font-size: 10px; color: #000; }
        .meta-right div { margin-bottom: 4px; }
        .meta-right strong { font-weight: bold; }
        .hr-top { border: none; border-top: 2px solid #1A3B66; margin: 10px 0 14px; height: 0; }

        .title-ficha {
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.18em;
            margin: 0 0 16px;
            text-transform: uppercase;
        }

        /* CURSO: barra ancha + PARTICIPANTES + cuerpo */
        .bloque-curso { width: 100%; border-collapse: collapse; margin-bottom: 18px; border: 2px solid #1A3B66; }
        .bloque-curso td { vertical-align: top; }
        .bar-curso {
            background: #1A3B66;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            padding: 9px 12px;
            text-align: left;
        }
        .bar-part {
            background: #FFEB3B;
            color: #1A3B66;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            padding: 9px 12px;
            text-align: center;
            border-top: 1px solid #1A3B66;
            border-bottom: 1px solid #1A3B66;
        }
        .cell-curso-body {
            background: #e8eef5;
            padding: 11px;
            min-height: 130px;
            width: 50%;
            font-size: 10px;
            color: #1A3B66;
            border-right: 2px solid #1A3B66;
        }
        .cell-curso-body strong { color: #111; }
        .cell-part-body { background: #153a66; padding: 0; width: 50%; font-size: 10px; }
        .part-inner { width: 100%; border-collapse: collapse; }
        .part-inner td { border: 1px solid #2d5590; padding: 6px 8px; color: #fff; vertical-align: middle; }
        .part-inner td.num-cell {
            background: #1A3B66;
            width: 30px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }
        .part-inner td.part-name { background: #153a66; }

        /* DATOS DE FACTURACIÓN — barra azul como plantilla */
        .fact-title {
            background: #1A3B66;
            color: #fff;
            text-align: center;
            font-weight: bold;
            padding: 8px;
            font-size: 11px;
            border: 2px solid #1A3B66;
            border-bottom: none;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.data th, table.data td {
            border: 1px solid #9ca3af;
            padding: 6px 9px;
            text-align: left;
            font-size: 9.5px;
        }
        table.data th {
            background: #fff;
            font-weight: bold;
            width: 36%;
            color: #111;
            text-transform: uppercase;
        }
        table.data td { background: #fff; color: #1f2937; }

        .yellow-grid { width: 100%; border-collapse: collapse; margin-bottom: 12px; background: #FFEB3B; border: 2px solid #d4a012; }
        .yellow-grid td {
            width: 50%;
            vertical-align: top;
            padding: 10px 12px;
            font-size: 9.5px;
            border: 1px solid #d4a012;
            color: #111;
        }
        .yellow-grid .row { margin-bottom: 5px; }
        .yellow-grid strong { font-weight: bold; text-transform: uppercase; }

        .aviso {
            background: #1A3B66;
            color: #fff;
            padding: 12px 10px;
            text-align: center;
            margin-top: 8px;
            font-size: 8px;
            line-height: 1.45;
            text-transform: uppercase;
        }
        .aviso .amarillo { color: #FFEB3B; font-weight: bold; }
        .aviso-line2 { margin-top: 6px; color: #FFEB3B; font-weight: bold; display: block; }

        .footer {
            margin-top: 10px;
            padding: 12px 8px;
            background: #0a1628;
            color: #e5e7eb;
            font-size: 7.5px;
            line-height: 1.5;
        }
        .footer-grid { width: 100%; border-collapse: collapse; }
        .footer-grid td { vertical-align: top; padding: 3px 6px; }
        .footer .glow { color: #93c5fd; font-weight: 600; }
        .footer-muted { opacity: 0.8; font-size: 7px; margin-top: 6px; text-align: center; }
    </style>
</head>
<body>
    @php
        $sale->loadMissing('creator', 'saleParticipants', 'contact', 'company');
    @endphp

    {{-- Igual a plantilla PDF: CONSULTOR / FECHA arriba a la derecha; sin logo --}}
    <table class="header-meta-table">
        <tr>
            <td style="width: 52%;">&nbsp;</td>
            <td class="meta-right" style="width: 48%;">
                <div><strong>CONSULTOR:</strong> {{ $sale->creator?->name ?? '—' }}</div>
                <div><strong>FECHA:</strong> {{ $sale->fecha_venta?->format('d/m/Y') ?? '—' }}</div>
            </td>
        </tr>
    </table>
    <hr class="hr-top" />

    <div class="title-ficha">FICHA DE INSCRIPCIÓN</div>

    @php
        $listaPart = $sale->saleParticipants->values();
        if ($listaPart->isEmpty() && $sale->contact) {
            $em = trim(explode(',', (string) ($sale->contact->email ?? ''))[0] ?? '');
            $listaPart = collect([(object) ['nombre' => $sale->contact->nombre_completo, 'email' => $em]]);
        }
        $slotsPdf = [];
        for ($si = 0; $si < 5; $si++) {
            $slotsPdf[$si] = $listaPart[$si] ?? null;
        }
    @endphp

    <table class="bloque-curso">
        <tr>
            <td colspan="2" class="bar-curso">CURSO</td>
        </tr>
        <tr>
            <td colspan="2" class="bar-part">PARTICIPANTES</td>
        </tr>
        <tr>
            <td class="cell-curso-body">
                <div style="margin-bottom:8px;">
                    <strong>TIPO DE CURSO:</strong><br>
                    {{ filled(trim((string) ($sale->tipo_curso ?? ''))) ? $sale->tipo_curso : '—' }}
                </div>
                <div>
                    <strong>CURSO:</strong><br>
                    {{ $sale->nombre_curso_ficha }}
                </div>
            </td>
            <td class="cell-part-body">
                <table class="part-inner">
                    @foreach($slotsPdf as $idx => $part)
                    <tr>
                        <td class="num-cell">{{ $idx + 1 }}</td>
                        <td class="part-name">
                            @if($part)
                                <strong>{{ $part->nombre }}</strong>
                                @if(!empty($part->email))<br><span style="font-size: 8px;">{{ $part->email }}</span>@endif
                            @else
                                <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    <div class="fact-title">DATOS DE FACTURACIÓN</div>
    <table class="data">
        <tr><th>RAZÓN SOCIAL:</th><td>{{ $sale->company->nombre_comercial }}</td></tr>
        <tr><th>CALLE Y NÚMERO:</th><td>{{ Str::limit($sale->company->datos_fiscales, 120) ?: '—' }}</td></tr>
        <tr><th>COLONIA Y C.P.:</th><td>{{ $sale->colonia_cp ?? '—' }}</td></tr>
        <tr><th>CIUDAD, ESTADO:</th><td>{{ trim(($sale->company->municipio ?? '') . ', ' . ($sale->company->estado ?? ''), ' ,') ?: '—' }}</td></tr>
        <tr><th>RFC:</th><td>{{ $sale->company->rfc ?? '—' }}</td></tr>
        <tr><th>TEL:</th><td>{{ $sale->contact?->celular ?? $sale->contact?->telefono ?? '—' }}</td></tr>
        <tr><th>REGIMEN EN QUE TRIBUTA:</th><td>{{ $sale->regimen_fiscal ?? '—' }}</td></tr>
        <tr><th>MÉTODO PAGO:</th><td style="white-space: pre-wrap;">{{ $sale->tipo_pago_label ?? '—' }}</td></tr>
        <tr><th>FORMA DE PAGO:</th><td>{{ $sale->forma_pago_label ?? '—' }}</td></tr>
        <tr><th>USO DE CFDI</th><td>{{ $sale->uso_cfdi ?? '—' }}</td></tr>
        <tr><th>ORDEN DE COMPRA</th><td>{{ $sale->orden_compra_label ?? '—' }}</td></tr>
        <tr><th>CORREO:</th><td>{{ $sale->contact?->email ?? '—' }}</td></tr>
    </table>

    @php
        $participantes = (int) ($sale->participantes ?? 1);
        $precioUnitario = $sale->monto ? (float) $sale->monto : 0;
        $subtotal = $participantes > 0 ? $precioUnitario * $participantes : $precioUnitario;
        $incluyeIva = $sale->incluye_iva ?? true;
        $iva = $incluyeIva ? round($subtotal * 0.16, 2) : 0;
        $total = $subtotal + $iva;
        $fechaEventoPdf = $sale->fecha_evento ?? $sale->fecha_venta;
    @endphp
    <table class="yellow-grid">
        <tr>
            <td>
                {{-- Textos alineados a la plantilla de referencia --}}
                <div class="row"><strong>NUMERO DE PARTICIPANTES:</strong> {{ $participantes }}</div>
                <div class="row"><strong>PRECIO UNITARIO:</strong> $ {{ number_format($precioUnitario, 2, '.', ',') }}</div>
                <div class="row"><strong>SUB-TOTAL:</strong> $ {{ number_format($subtotal, 2, '.', ',') }}</div>
                @if($incluyeIva)
                <div class="row"><strong>IVA:</strong> $ {{ number_format($iva, 2, '.', ',') }}</div>
                @endif
                <div class="row"><strong>TOTAL:</strong> $ {{ number_format($total, 2, '.', ',') }}</div>
            </td>
            <td>
                <div class="row"><strong>CONDICIONES DE PAGO:</strong> @if(filled($sale->condiciones_pago)){!! nl2br(e($sale->condiciones_pago)) !!}@else — @endif</div>
                <div class="row"><strong>MODALIDAD:</strong> {{ filled($sale->modalidad) ? $sale->modalidad : '—' }}</div>
                <div class="row"><strong>SEDE:</strong> {{ filled($sale->sede) ? $sale->sede : '—' }}</div>
                <div class="row"><strong>FECHA:</strong> {{ $fechaEventoPdf ? $fechaEventoPdf->format('d/m/Y') : '—' }}</div>
                <div class="row"><strong>HORARIO:</strong> {{ filled($sale->horario_evento) ? $sale->horario_evento : '—' }}</div>
                <div class="row"><strong>FACTURA:</strong> {{ filled($sale->factura_referencia) ? $sale->factura_referencia : '—' }}</div>
            </td>
        </tr>
    </table>

    <div class="aviso">
        <span class="amarillo">C&amp;CE CONSULTORIA Y CAPACITACIÓN EMPRESARIAL</span>
        <span> SE RESERVA EL DERECHO DE ABRIR, CANCELAR O CAMBIAR DE FECHA EL INICIO CUALQUIER PROGRAMA DE CAPACITACIÓN DE ACUERDO AL NÚMERO DE PARTICIPANTES ESTO POR RAZONES LOGÍSTICAS Y PEDAGÓGICAS.</span>
        <span class="aviso-line2">EN CASO DE CANCELACION SE DEBERA NOTIFICAR 4 DIAS ANTES DEL EVENTO</span>
    </div>

    <div class="footer">
        <table class="footer-grid">
            <tr>
                <td style="width:50%;">
                    <span class="glow">AV. AYUNTAMIENTO 413, INT. 103</span><br>
                    FRACC. CEDROS – C.P. 20270<br>
                    AGUASCALIENTES, AGS.
                </td>
                <td style="width:50%; text-align:right;">
                    <strong class="glow">CONTÁCTANOS:</strong><br>
                    449 664 52 90 · 449 425 41 54
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center; padding-top:8px; border-top:1px solid #1e3a5f;">
                    <span class="glow">www.ceconsultoriaempresarial.com</span>
                    &nbsp;·&nbsp; CE Consultoria Empresarial
                </td>
            </tr>
        </table>
        <p class="footer-muted">Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
