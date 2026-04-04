<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha de Inscripción{{ $sale->nombre_curso_ficha !== '—' ? ' - '.$sale->nombre_curso_ficha : '' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 6mm 8mm 5mm 8mm;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #111827;
            font-size: 9px;
            line-height: 1.32;
        }

        /* Una sola columna de flujo: evita saltos raros entre bloques en DomPDF */
        table.pdf-flow {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }
        table.pdf-flow > tbody > tr > td {
            padding: 0;
            vertical-align: top;
            border: 0;
        }

        .page-shell {
            border: 2px solid #b0b0b0;
            padding: 8px 10px 6px;
        }

        .header-top { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .header-top td { vertical-align: top; padding: 0; }
        .header-logo-cell { width: 26%; padding-right: 10px !important; }
        /* Tamaño fijo en px: imágenes PNG grandes no deben “romper” el layout */
        .header-logo-img {
            width: 82px;
            height: 82px;
            max-width: 82px;
            max-height: 82px;
            object-fit: contain;
            display: block;
        }
        .logo-placeholder {
            width: 82px;
            height: 82px;
            border-radius: 50%;
            border: 2.5px solid #0a1744;
            color: #0a1744;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
            line-height: 76px;
            letter-spacing: -0.02em;
        }
        .header-right { text-align: right; padding-top: 2px; }
        .slogan-header {
            color: #003399;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            line-height: 1.28;
            margin-bottom: 8px;
        }
        .meta-right { font-size: 9px; color: #000; line-height: 1.38; }
        .meta-right div { margin-bottom: 2px; }
        .meta-right strong { font-weight: bold; }

        .title-ficha {
            text-align: center;
            color: #000;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.12em;
            margin: 8px 0 10px;
            text-transform: uppercase;
            line-height: 1.2;
        }

        /* Curso | Participantes — alturas en px (DomPDF suele manejar mejor que mm) */
        .bloque-curso {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 2px solid #0a1744;
            table-layout: fixed;
        }
        .bloque-curso td { vertical-align: top; }
        .bar-curso {
            background: #0a1744;
            color: #fff;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            padding: 6px 10px;
            text-align: left;
            width: 50%;
            border-right: 2px solid #0a1744;
            line-height: 1.22;
        }
        .bar-part {
            background: #ffdc00;
            color: #000;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            padding: 6px 10px;
            text-align: center;
            width: 50%;
            line-height: 1.22;
        }
        .cell-curso-body {
            background: #fff;
            padding: 8px 10px;
            min-height: 108px;
            height: 108px;
            width: 50%;
            font-size: 9px;
            color: #111;
            border-right: 2px solid #0a1744;
            vertical-align: top;
        }
        .cell-curso-body strong { color: #0a1744; font-size: 9px; text-transform: uppercase; }
        .cell-part-body {
            background: #0a1744;
            padding: 0;
            width: 50%;
            font-size: 9px;
            min-height: 108px;
            height: 108px;
            vertical-align: top;
        }
        .part-inner { width: 100%; border-collapse: collapse; height: 100%; }
        .part-inner tr { height: 20%; }
        .part-inner td {
            border: 1px solid #1e3a6e;
            padding: 4px 7px;
            color: #fff;
            vertical-align: middle;
            font-size: 8.5px;
            line-height: 1.22;
        }
        .part-inner td.num-cell {
            background: #071a3d;
            width: 26px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }
        .part-inner td.part-name { background: #0a1744; }
        .part-texto-libre {
            padding: 8px 10px;
            color: #fff;
            font-size: 8.5px;
            line-height: 1.3;
            white-space: pre-wrap;
            min-height: 100px;
            max-height: 108px;
            overflow: hidden;
        }

        /* DATOS DE FACTURACIÓN — una columna etiqueta / valor (diseño original) */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #9ca3af;
        }
        table.data td {
            border: 1px solid #9ca3af;
            padding: 4px 8px;
            text-align: left;
            font-size: 8.5px;
            line-height: 1.28;
            vertical-align: middle;
        }
        table.data td.fact-head {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 7px 10px;
            background: #fff;
            color: #000;
        }
        table.data td.lbl {
            width: 34%;
            font-weight: bold;
            color: #111;
            text-transform: uppercase;
            background: #f9fafb;
        }
        table.data td.val { background: #fff; color: #1f2937; word-wrap: break-word; }

        .yellow-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            background: #ffdc00;
            border: 2px solid #c9a900;
            table-layout: fixed;
        }
        .yellow-grid td {
            width: 50%;
            vertical-align: top;
            padding: 8px 10px;
            font-size: 8.5px;
            border: 1px solid #c9a900;
            color: #111;
            line-height: 1.32;
        }
        .yellow-grid .row { margin-bottom: 5px; }
        .yellow-grid strong { font-weight: bold; text-transform: uppercase; font-size: 8.5px; }

        .aviso {
            background: #0a1744;
            color: #fff;
            padding: 8px 10px;
            text-align: center;
            margin-bottom: 4px;
            font-size: 7px;
            line-height: 1.38;
            text-transform: uppercase;
        }
        .aviso .amarillo { color: #ffdc00; font-weight: bold; }
        .aviso-line2 {
            margin-top: 5px;
            color: #ffdc00;
            font-weight: bold;
            display: block;
            font-size: 7.5px;
        }

        /* Pie: ancho completo, proporción natural (no altura fija) */
        .footer-wrap {
            width: 100%;
            margin: 0;
            padding: 0;
            line-height: 0;
        }
        .footer-banner {
            width: 100%;
            height: auto;
            max-width: 100%;
            display: block;
            margin: 0;
            padding: 0;
        }
        .footer-fallback {
            margin-top: 6px;
            padding: 10px 10px 8px;
            background: #050d22;
            color: #e5e7eb;
            font-size: 7.5px;
            line-height: 1.45;
        }
        .footer-fallback .footer-grid { width: 100%; border-collapse: collapse; }
        .footer-fallback .footer-grid td { vertical-align: top; padding: 3px 6px; }
        .footer-fallback .glow { color: #93c5fd; font-weight: 600; }
    </style>
</head>
<body>
    @php
        $sale->loadMissing('creator', 'saleParticipants', 'contact', 'company');
        $embedImage = static function (string $rel): ?string {
            $path = public_path($rel);
            if (! is_readable($path)) {
                return null;
            }
            $lower = strtolower($path);
            $mime = str_ends_with($lower, '.png') ? 'image/png' : (str_ends_with($lower, '.jpg') || str_ends_with($lower, '.jpeg') ? 'image/jpeg' : 'image/png');

            return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
        };
        $logoSrc = null;
        foreach (['images/logo-ficha-ce.png', 'images/logo-ficha-ce.jpg', 'images/logo-ficha-ce.jpeg'] as $logoRel) {
            $logoSrc = $embedImage($logoRel);
            if ($logoSrc) {
                break;
            }
        }
        $footerSrc = null;
        $footerPathForSize = null;
        foreach (['images/ficha-pie-pagina.png', 'images/ficha-pie-pagina.jpg', 'images/ficha-pie-pagina.jpeg'] as $footRel) {
            $fp = public_path($footRel);
            if (! is_readable($fp)) {
                continue;
            }
            $footerSrc = $embedImage($footRel);
            $footerPathForSize = $fp;
            break;
        }
        $footerImgW = $footerImgH = null;
        if ($footerPathForSize) {
            $info = @getimagesize($footerPathForSize);
            if (is_array($info) && ($info[0] ?? 0) > 0 && ($info[1] ?? 0) > 0) {
                $maxW = 538;
                $scale = min(1.0, $maxW / $info[0]);
                $footerImgW = (int) round($info[0] * $scale);
                $footerImgH = (int) round($info[1] * $scale);
            }
        }
    @endphp

    <table class="pdf-flow"><tr><td>
    <div class="page-shell">
        <table class="header-top">
            <tr>
                <td class="header-logo-cell">
                    @if ($logoSrc)
                        <img class="header-logo-img" src="{{ $logoSrc }}" alt="" width="82" height="82" style="width:82px;height:82px;max-width:82px;max-height:82px;">
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

        <div class="title-ficha">FICHA DE INSCRIPCIÓN</div>

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
                                                <br><span style="font-size: 7.5px; opacity: 0.95;">{{ $part->email }}</span>
                                            @endif
                                        @else
                                            <span style="color:#64748b;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                </td>
            </tr>
        </table>

        <table class="data">
            <tr>
                <td colspan="2" class="fact-head">DATOS DE FACTURACIÓN</td>
            </tr>
            <tr><td class="lbl">RAZÓN SOCIAL:</td><td class="val">{{ $sale->contact?->razon_social ?? $sale->company->nombre_comercial ?? '—' }}</td></tr>
            <tr><td class="lbl">CALLE Y NÚMERO:</td><td class="val">{{ Str::limit($sale->calleNumeroFacturacionResuelto(), 160) }}</td></tr>
            <tr><td class="lbl">COLONIA Y C.P.:</td><td class="val">{{ $sale->colonia_cp ?? '—' }}</td></tr>
            <tr><td class="lbl">CIUDAD, ESTADO:</td><td class="val">{{ trim(($sale->contact?->municipio ?? $sale->company->municipio ?? '') . ', ' . ($sale->contact?->estado ?? $sale->company->estado ?? ''), ' ,') ?: '—' }}</td></tr>
            <tr><td class="lbl">RFC:</td><td class="val">{{ $sale->rfcFacturacionResuelto() }}</td></tr>
            <tr><td class="lbl">TEL:</td><td class="val">{{ $sale->contact?->celular ?? $sale->contact?->telefono ?? '—' }}</td></tr>
            <tr><td class="lbl">RÉGIMEN EN QUE TRIBUTA:</td><td class="val">{{ $sale->regimen_fiscal ?? '—' }}</td></tr>
            <tr><td class="lbl">MÉTODO PAGO:</td><td class="val" style="white-space: pre-wrap;">{{ $sale->tipo_pago_label ?? '—' }}</td></tr>
            <tr><td class="lbl">FORMA DE PAGO:</td><td class="val">{{ $sale->forma_pago_label ?? '—' }}</td></tr>
            <tr><td class="lbl">USO DE CFDI:</td><td class="val">{{ $sale->uso_cfdi ?? '—' }}</td></tr>
            <tr><td class="lbl">ORDEN DE COMPRA:</td><td class="val">{{ $sale->orden_compra_label ?? '—' }}</td></tr>
            <tr><td class="lbl">CORREO:</td><td class="val">{{ $sale->emailFacturacionResuelto() }}</td></tr>
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
                    <div class="row"><strong>CONDICIONES DE PAGO:</strong> @if (filled($sale->condiciones_pago)) {!! nl2br(e(Str::limit($sale->condiciones_pago, 380))) !!} @else — @endif</div>
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

        @if ($footerSrc)
            <div class="footer-wrap">
                <img class="footer-banner" src="{{ $footerSrc }}" alt="">
            </div>
        @else
            <div class="footer-fallback">
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
                            &nbsp;|&nbsp; CE Consultoría Empresarial
                        </td>
                    </tr>
                </table>
            </div>
        @endif
    </div>
    </td></tr></table>
</body>
</html>
