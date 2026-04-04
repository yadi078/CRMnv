<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha de Inscripción{{ $sale->nombre_curso_ficha !== '—' ? ' - '.$sale->nombre_curso_ficha : '' }}</title>
    <style>
        @page {
            size: letter portrait;
            margin: 0.28in 0.4in 0.32in 0.4in;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #1a1a1a;
            font-size: 9.5px;
            line-height: 1.28;
        }

        /* Cabecera tipo Word: logo izq. | eslogan + CONSULTOR/FECHA der. */
        .header-top { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .header-top td { vertical-align: top; padding: 0; }
        .header-logo-cell { width: 22%; padding-right: 8px !important; }
        .header-logo-img {
            width: 68px;
            height: 68px;
            object-fit: contain;
            display: block;
        }
        .logo-placeholder {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            border: 2.5px solid #1A3B66;
            color: #1A3B66;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
            line-height: 64px;
            letter-spacing: -0.02em;
        }
        .header-right { text-align: right; padding-top: 2px; }
        .slogan-header {
            color: #1A3B66;
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            line-height: 1.25;
            margin-bottom: 8px;
        }
        .meta-right { font-size: 9px; color: #000; line-height: 1.35; }
        .meta-right div { margin-bottom: 2px; }
        .meta-right strong { font-weight: bold; }

        .title-wrap { margin: 10px 0 12px; }
        .title-hr {
            border: none;
            border-top: 1px solid #1A3B66;
            margin: 0;
            height: 0;
        }
        .title-ficha {
            text-align: center;
            color: #1A3B66;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.12em;
            margin: 6px 0;
            text-transform: uppercase;
            line-height: 1.15;
        }

        /* Una sola fila: CURSO (azul) | PARTICIPANTES (amarillo), como plantilla Word */
        .bloque-curso {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 2px solid #1A3B66;
            page-break-inside: avoid;
        }
        .bloque-curso td { vertical-align: top; }
        .bar-curso {
            background: #1A3B66;
            color: #fff;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            padding: 7px 10px;
            text-align: left;
            width: 50%;
            border-right: 2px solid #1A3B66;
            line-height: 1.2;
        }
        .bar-part {
            background: #FFEB3B;
            color: #1A3B66;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            padding: 7px 10px;
            text-align: center;
            width: 50%;
            line-height: 1.2;
        }
        .cell-curso-body {
            background: #e8eef5;
            padding: 8px 10px;
            min-height: 125px;
            width: 50%;
            font-size: 9px;
            color: #1A3B66;
            border-right: 2px solid #1A3B66;
            vertical-align: top;
        }
        .cell-curso-body strong { color: #111; font-size: 9px; }
        .cell-part-body {
            background: #153a66;
            padding: 0;
            width: 50%;
            font-size: 9px;
            min-height: 125px;
            vertical-align: top;
        }
        .part-inner { width: 100%; border-collapse: collapse; }
        .part-inner td {
            border: 1px solid #2d5590;
            padding: 4px 7px;
            color: #fff;
            vertical-align: middle;
            font-size: 8.5px;
            line-height: 1.2;
        }
        .part-inner td.num-cell {
            background: #1A3B66;
            width: 26px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }
        .part-inner td.part-name { background: #153a66; }
        .part-texto-libre {
            padding: 8px 10px;
            color: #fff;
            font-size: 8.5px;
            line-height: 1.3;
            white-space: pre-wrap;
            min-height: 118px;
            max-height: 200px;
            overflow: hidden;
        }

        .fact-title {
            background: #1A3B66;
            color: #fff;
            text-align: center;
            font-weight: bold;
            padding: 6px 8px;
            font-size: 10px;
            border: 2px solid #1A3B66;
            border-bottom: none;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            line-height: 1.2;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        table.data th, table.data td {
            border: 1px solid #9ca3af;
            padding: 4px 8px;
            text-align: left;
            font-size: 8.5px;
            line-height: 1.25;
        }
        table.data th {
            background: #fff;
            font-weight: bold;
            width: 34%;
            color: #111;
            text-transform: uppercase;
        }
        table.data td { background: #fff; color: #1f2937; word-wrap: break-word; }

        .yellow-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            background: #FFEB3B;
            border: 2px solid #d4a012;
            page-break-inside: avoid;
        }
        .yellow-grid td {
            width: 50%;
            vertical-align: top;
            padding: 9px 11px;
            font-size: 8.5px;
            border: 1px solid #d4a012;
            color: #111;
            line-height: 1.3;
        }
        .yellow-grid .row { margin-bottom: 5px; }
        .yellow-grid strong { font-weight: bold; text-transform: uppercase; font-size: 8.5px; }

        .aviso {
            background: #1A3B66;
            color: #fff;
            padding: 7px 8px;
            text-align: center;
            margin-top: 0;
            font-size: 7px;
            line-height: 1.35;
            text-transform: uppercase;
            page-break-inside: avoid;
        }
        .aviso .amarillo { color: #FFEB3B; font-weight: bold; }
        .aviso-line2 { margin-top: 4px; color: #FFEB3B; font-weight: bold; display: block; }

        .footer {
            margin-top: 6px;
            padding: 10px 10px 8px;
            background: #0a1628;
            color: #e5e7eb;
            font-size: 7.5px;
            line-height: 1.45;
            page-break-inside: avoid;
        }
        .footer-grid { width: 100%; border-collapse: collapse; }
        .footer-grid td { vertical-align: top; padding: 3px 6px; }
        .footer .glow { color: #93c5fd; font-weight: 600; }
    </style>
</head>
<body>
    @php
        $sale->loadMissing('creator', 'saleParticipants', 'contact', 'company');
        $logoSrc = null;
        foreach (['images/logo-ficha-ce.png', 'images/logo-ficha-ce.jpg', 'images/logo-ficha-ce.jpeg'] as $logoRel) {
            $logoPath = public_path($logoRel);
            if (! is_readable($logoPath)) {
                continue;
            }
            $mime = str_ends_with(strtolower($logoPath), '.png') ? 'image/png' : 'image/jpeg';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($logoPath));
            break;
        }
    @endphp

    <table class="header-top">
        <tr>
            <td class="header-logo-cell">
                @if ($logoSrc)
                    <img class="header-logo-img" src="{{ $logoSrc }}" alt="">
                @else
                    <div class="logo-placeholder">C&amp;CE</div>
                @endif
            </td>
            <td class="header-right">
                <div class="slogan-header">INVERTIR EN VALOR ¡ATRAE VALOR!</div>
                <div class="meta-right">
                    <div><strong>CONSULTOR:</strong> {{ $sale->creator?->name ?? '—' }}</div>
                    <div><strong>FECHA:</strong> {{ $sale->fecha_venta?->format('d/m/Y') ?? '—' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="title-wrap">
        <hr class="title-hr">
        <div class="title-ficha">FICHA DE INSCRIPCIÓN</div>
        <hr class="title-hr">
    </div>

    @php
        $participantesTextoPdf = filled(trim((string) ($sale->participantes_texto ?? '')));
        $listaPart = $sale->saleParticipants->values();
        if (! $participantesTextoPdf && $listaPart->isEmpty() && $sale->contact) {
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
            <td class="bar-curso">CURSO</td>
            <td class="bar-part">PARTICIPANTES</td>
        </tr>
        <tr>
            <td class="cell-curso-body">
                <div style="margin-bottom:6px;">
                    <strong>NOMBRE DEL CURSO O SERVICIO:</strong><br>
                    {{ filled(trim((string) ($sale->tipo_curso ?? ''))) ? $sale->tipo_curso : '—' }}
                </div>
                <div>
                    <strong>CURSO:</strong><br>
                    {{ $sale->nombre_curso_ficha }}
                </div>
            </td>
            <td class="cell-part-body">
                @if ($participantesTextoPdf)
                    <div class="part-texto-libre">{{ trim((string) $sale->participantes_texto) }}</div>
                @else
                    <table class="part-inner">
                        @foreach ($slotsPdf as $idx => $part)
                            <tr>
                                <td class="num-cell">{{ $idx + 1 }}</td>
                                <td class="part-name">
                                    @if ($part)
                                        <strong>{{ $part->nombre }}</strong>
                                        @if (! empty($part->email))
                                            <br><span style="font-size: 7.5px;">{{ $part->email }}</span>
                                        @endif
                                    @else
                                        <span style="color:#94a3b8;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </td>
        </tr>
    </table>

    <div class="fact-title">DATOS DE FACTURACIÓN</div>
    <table class="data">
        <tr><th>RAZÓN SOCIAL:</th><td>{{ $sale->contact?->razon_social ?? $sale->company->nombre_comercial ?? '—' }}</td></tr>
        <tr><th>CALLE Y NÚMERO:</th><td>{{ Str::limit($sale->calleNumeroFacturacionResuelto(), 160) }}</td></tr>
        <tr><th>COLONIA Y C.P.:</th><td>{{ $sale->colonia_cp ?? '—' }}</td></tr>
        <tr><th>CIUDAD, ESTADO:</th><td>{{ trim(($sale->contact?->municipio ?? $sale->company->municipio ?? '') . ', ' . ($sale->contact?->estado ?? $sale->company->estado ?? ''), ' ,') ?: '—' }}</td></tr>
        <tr><th>RFC:</th><td>{{ $sale->rfcFacturacionResuelto() }}</td></tr>
        <tr><th>TEL:</th><td>{{ $sale->contact?->celular ?? $sale->contact?->telefono ?? '—' }}</td></tr>
        <tr><th>RÉGIMEN EN QUE TRIBUTA:</th><td>{{ $sale->regimen_fiscal ?? '—' }}</td></tr>
        <tr><th>MÉTODO PAGO:</th><td style="white-space: pre-wrap;">{{ $sale->tipo_pago_label ?? '—' }}</td></tr>
        <tr><th>FORMA DE PAGO:</th><td>{{ $sale->forma_pago_label ?? '—' }}</td></tr>
        <tr><th>USO DE CFDI</th><td>{{ $sale->uso_cfdi ?? '—' }}</td></tr>
        <tr><th>ORDEN DE COMPRA</th><td>{{ $sale->orden_compra_label ?? '—' }}</td></tr>
        <tr><th>CORREO:</th><td>{{ $sale->emailFacturacionResuelto() }}</td></tr>
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
                <div class="row"><strong>NÚMERO DE PARTICIPANTES:</strong> {{ $participantes }}</div>
                <div class="row"><strong>PRECIO UNITARIO:</strong> $ {{ number_format($precioUnitario, 2, '.', ',') }}</div>
                <div class="row"><strong>SUB-TOTAL:</strong> $ {{ number_format($subtotal, 2, '.', ',') }}</div>
                @if ($incluyeIva)
                    <div class="row"><strong>IVA:</strong> $ {{ number_format($iva, 2, '.', ',') }}</div>
                @endif
                <div class="row"><strong>TOTAL:</strong> $ {{ number_format($total, 2, '.', ',') }}</div>
            </td>
            <td>
                <div class="row"><strong>CONDICIONES DE PAGO:</strong> @if (filled($sale->condiciones_pago)) {!! nl2br(e(Str::limit($sale->condiciones_pago, 450))) !!} @else — @endif</div>
                <div class="row"><strong>MODALIDAD:</strong> {{ filled($sale->modalidad) ? $sale->modalidad : '—' }}</div>
                <div class="row"><strong>SEDE:</strong> {{ filled($sale->sede) ? $sale->sede : '—' }}</div>
                <div class="row"><strong>FECHA:</strong> {{ $fechaEventoPdf ? $fechaEventoPdf->format('d/m/Y') : '—' }}</div>
                <div class="row"><strong>HORARIO:</strong> {{ filled($sale->horario_evento) ? $sale->horario_evento : '—' }}</div>
                <div class="row"><strong>FACTURA:</strong> {{ filled($sale->factura_referencia) ? $sale->factura_referencia : '—' }}</div>
            </td>
        </tr>
    </table>

    <div class="aviso">
        <span class="amarillo">C&amp;CE CONSULTORÍA Y CAPACITACIÓN EMPRESARIAL</span>
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
                    &nbsp;·&nbsp; CE Consultoría Empresarial · Facebook: CE Consultoría Empresarial
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
