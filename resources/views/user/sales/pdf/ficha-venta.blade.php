<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha de Inscripción{{ $sale->nombre_curso_ficha !== '—' ? ' - '.$sale->nombre_curso_ficha : '' }}</title>
    <style>
        @page {
            size: letter portrait;
            margin: 0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html {
            margin: 0;
            width: 100%;
        }
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #111827;
            font-size: 9px;
            line-height: 1.35;
            background: #fff;
        }
        /* Tabla envolvente: padding lateral idéntico (DomPDF centra mal body+padding+width:100%). */
        table.pdf-layout {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            table-layout: fixed;
        }
        td.pdf-layout__main {
            padding: 0 28mm;
            vertical-align: top;
        }

        /* ── HEADER ── */
        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .header td { vertical-align: top; padding: 0; }
        /* DomPDF suele dibujar el canal alpha del PNG como negro; fondo blanco = mismo efecto que la hoja. */
        .logo-cell {
            width: 78px;
            padding-right: 10px !important;
            background: #ffffff;
        }
        .logo-img {
            width: 76px;
            height: 76px;
            max-width: 76px;
            max-height: 76px;
            object-fit: contain;
            object-position: center center;
            display: block;
            background-color: #ffffff;
        }
        .logo-placeholder {
            width: 76px; height: 76px;
            border-radius: 50%;
            border: 2px solid #1A2B56;
            color: #1A2B56; font-weight: bold;
            font-size: 9px; text-align: center;
            line-height: 72px;
        }
        .header-right { text-align: right; }
        .slogan-block {
            display: inline-block;
            border-bottom: 3px solid #1A2B56;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .slogan {
            color: #1A2B56; font-weight: bold;
            font-size: 14px; text-transform: uppercase;
            letter-spacing: 0.04em; line-height: 1.15;
        }
        .meta { font-size: 9px; color: #374151; text-align: right; line-height: 1.55; }
        .meta strong { color: #111; font-weight: bold; }

        /* ── TÍTULO ── */
        .titulo {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            margin: 6px 0 10px;
        }

        /* Contenedor solo para centrar la tabla curso/participantes (el resto del PDF sigue a ancho útil) */
        .wrap-bloque-centro {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .wrap-bloque-centro td {
            vertical-align: top;
            padding: 0;
            border: 0;
        }

        /* Tabla de 3 filas × 1 columna (sin columnas internas) */
        .bloque-curso {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            border: 2px solid #1A2B56;
            margin: 0 auto;
            table-layout: fixed;
            text-align: left;
        }
        .bloque-curso td {
            padding: 0;
            vertical-align: top;
            width: 100%;
        }

        .td-fila-curso-servicio {
            background: #1A2B56;
            color: #fff;
            font-size: 9px;
            padding: 10px 14px;
            line-height: 1.4;
            text-align: left !important;
        }
        .td-fila-curso-servicio .lbl-curso {
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .td-fila-participantes-titulo {
            background: #FFFF00;
            color: #000;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            text-align: center !important;
            padding: 6px 12px;
            border-top: 1px solid #1A2B56;
        }

        .td-fila-participantes-cuerpo {
            background: #1A2B56;
            color: #fff;
            font-size: 9px;
            padding: 10px 14px;
            line-height: 1.45;
            text-align: left !important;
        }
        .part-libre {
            white-space: pre-wrap;
            margin: 0;
        }
        .part-line {
            margin-bottom: 6px;
        }
        .part-line:last-child { margin-bottom: 0; }
        .part-mail {
            font-size: 7.5px;
            opacity: 0.92;
            display: block;
            margin-top: 2px;
            padding-left: 12px;
        }
        .part-vacio { color: #94a3b8; }

        /* ── DATOS DE FACTURACIÓN ── */
        .fact-wrap {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #b0b0b0;
            margin-bottom: 10px;
        }
        .fact-table {
            width: 100%; border-collapse: collapse;
        }
        .fact-table td {
            border: 1px solid #c0c0c0;
            padding: 7px 10px;
            font-size: 9px;
            vertical-align: middle;
            line-height: 1.35;
        }
        .fact-head {
            background: #e5e7eb;
            text-align: center;
            font-weight: bold;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 8px 12px !important;
            border-bottom: 1px solid #b0b0b0;
        }
        .fact-lbl {
            width: 32%;
            font-weight: bold;
            text-transform: uppercase;
            color: #111;
            background: #fff;
        }
        .fact-val { background: #fff; color: #1f2937; word-break: break-word; }

        /* ── GRID AMARILLO ── */
        .yellow-grid {
            width: 100%;
            border-collapse: collapse;
            background: #FFFF00;
            border: 2px solid #d4d400;
            margin-bottom: 10px;
            table-layout: fixed;
        }
        .yellow-grid td {
            width: 50%;
            padding: 10px 12px;
            font-size: 9px;
            border: 1px solid #d4d400;
            vertical-align: top;
            line-height: 1.45;
            color: #111;
        }
        .yellow-grid .row { margin-bottom: 5px; }
        .yellow-grid .row:last-child { margin-bottom: 0; }
        .yellow-grid strong {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
        }

        /* ── AVISO ── */
        .aviso {
            background: #1A2B56;
            color: #fff;
            padding: 10px 14px;
            text-align: center;
            font-size: 7.5px;
            text-transform: uppercase;
            line-height: 1.45;
            margin-bottom: 0;
        }
        .aviso .amarillo { color: #FFFF00; font-weight: bold; font-size: 8px; }
        .aviso .linea2 { color: #FFFF00; font-weight: bold; display: block; margin-top: 5px; font-size: 8px; }
    </style>
</head>
<body>
<table class="pdf-layout" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td class="pdf-layout__main" style="padding: 0 28mm; vertical-align: top;">
@php
    $sale->loadMissing('creator', 'saleParticipants', 'contact', 'company');

    $embedImage = static function (string $rel): ?string {
        $path = public_path($rel);
        if (! is_readable($path)) {
            return null;
        }
        $lower = strtolower($path);
        $mime = str_ends_with($lower, '.png') ? 'image/png'
            : (str_ends_with($lower, '.jpg') || str_ends_with($lower, '.jpeg') ? 'image/jpeg' : 'image/png');

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
    };

    $logoSrc = null;
    foreach ([
        'images/logo-ficha-ce.png',
        'images/logo-ficha-ce.jpg',
        'images/logo-ficha-ce.jpeg',
        'img/logo.png',
        'img/logo.jpg',
        'img/logo.jpeg',
    ] as $r) {
        if ($logoSrc = $embedImage($r)) {
            break;
        }
    }

    $participantesTextoPdf = filled(trim((string)($sale->participantes_texto ?? '')));
    $listaPart = $sale->saleParticipants->values();
    if (!$participantesTextoPdf && $listaPart->isEmpty() && $sale->contact) {
        $em = trim(explode(',', (string)($sale->contact->email ?? ''))[0] ?? '');
        $listaPart = collect([(object)['nombre' => $sale->contact->nombre_completo, 'email' => $em]]);
    }
    $slotsPdf = [];
    for ($si = 0; $si < 5; $si++) $slotsPdf[$si] = $listaPart[$si] ?? null;

    $tipoCursoTxt = filled(trim((string)($sale->tipo_curso ?? ''))) ? $sale->tipo_curso : '—';

    $tCurso = trim($tipoCursoTxt) === '—' ? '' : trim($tipoCursoTxt);
    $nFicha = trim((string)($sale->nombre_curso_ficha ?? ''));
    if ($nFicha === '—') {
        $nFicha = '';
    }
    if ($tCurso !== '' && $nFicha !== '' && strcasecmp($tCurso, $nFicha) === 0) {
        $cursoServicioUnaLinea = $tCurso;
    } elseif ($tCurso !== '' && $nFicha !== '') {
        $cursoServicioUnaLinea = $tCurso.' — '.$nFicha;
    } elseif ($nFicha !== '') {
        $cursoServicioUnaLinea = $nFicha;
    } else {
        $cursoServicioUnaLinea = $tCurso !== '' ? $tCurso : '—';
    }

    $participantesHtmlLista = '';
    if (!$participantesTextoPdf) {
        for ($idx = 0; $idx < 5; $idx++) {
            $p = $slotsPdf[$idx] ?? null;
            $n = $idx + 1;
            if ($p) {
                $participantesHtmlLista .= '<div class="part-line"><strong>'.$n.'.</strong> '.e($p->nombre);
                if (! empty($p->email)) {
                    $participantesHtmlLista .= '<br><span class="part-mail">'.e($p->email).'</span>';
                }
                $participantesHtmlLista .= '</div>';
            } else {
                $participantesHtmlLista .= '<div class="part-line"><strong>'.$n.'.</strong> <span class="part-vacio">—</span></div>';
            }
        }
    }

    $participantes   = (int)($sale->participantes ?? 1);
    $precioUnitario  = $sale->monto ? (float)$sale->monto : 0;
    $subtotal        = $participantes > 0 ? $precioUnitario * $participantes : $precioUnitario;
    $incluyeIva      = $sale->incluye_iva ?? true;
    $iva             = $incluyeIva ? round($subtotal * 0.16, 2) : 0;
    $total           = $subtotal + $iva;
    $fechaEventoPdf  = $sale->fecha_evento ?? $sale->fecha_venta;
@endphp

<table class="header">
    <tr>
        <td class="logo-cell">
            @if($logoSrc)
                <img class="logo-img" src="{{ $logoSrc }}" alt="" width="76" height="76">
            @else
                <div class="logo-placeholder">C&amp;CE</div>
            @endif
        </td>
        <td class="header-right">
            <div class="slogan-block">
                <div class="slogan">INVERTIR EN VALOR ¡ATRAE VALOR!</div>
            </div>
            <div class="meta">
                <div><strong>CONSULTOR:</strong> {{ $sale->nombreConsultorParaFicha() }}</div>
                <div><strong>FECHA:</strong> {{ $sale->fecha_venta?->format('d/m/Y') ?? '—' }}</div>
            </div>
        </td>
    </tr>
</table>

<div class="titulo">FICHA DE INSCRIPCIÓN</div>

<table class="wrap-bloque-centro">
    <tr>
        <td>
            <table class="bloque-curso">
                <tr>
                    <td class="td-fila-curso-servicio">
                        <span class="lbl-curso">Curso o servicio:</span> {{ $cursoServicioUnaLinea }}
                    </td>
                </tr>
                <tr>
                    <td class="td-fila-participantes-titulo">PARTICIPANTES</td>
                </tr>
                <tr>
                    <td class="td-fila-participantes-cuerpo">
                        @if($participantesTextoPdf)
                            <div class="part-libre">{{ trim((string) $sale->participantes_texto) }}</div>
                        @else
                            {!! $participantesHtmlLista !!}
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="fact-wrap">
    <tr><td style="padding:0;">
        <table class="fact-table">
            <tr><td colspan="2" class="fact-head">DATOS DE FACTURACIÓN</td></tr>
            <tr><td class="fact-lbl">RAZÓN SOCIAL:</td><td class="fact-val">{{ $sale->contact?->razon_social ?? $sale->company->nombre_comercial ?? '—' }}</td></tr>
            <tr><td class="fact-lbl">CALLE Y NÚMERO:</td><td class="fact-val">{{ Str::limit($sale->calleNumeroFacturacionResuelto(), 160) }}</td></tr>
            <tr><td class="fact-lbl">COLONIA Y C.P.:</td><td class="fact-val">{{ $sale->colonia_cp ?? '—' }}</td></tr>
            <tr><td class="fact-lbl">CIUDAD, ESTADO:</td><td class="fact-val">{{ trim(($sale->contact?->municipio ?? $sale->company->municipio ?? '') . ', ' . ($sale->contact?->estado ?? $sale->company->estado ?? ''), ' ,') ?: '—' }}</td></tr>
            <tr><td class="fact-lbl">RFC:</td><td class="fact-val">{{ $sale->rfcFacturacionResuelto() }}</td></tr>
            <tr><td class="fact-lbl">TEL:</td><td class="fact-val">{{ $sale->contact?->celular ?? $sale->contact?->telefono ?? '—' }}</td></tr>
            <tr><td class="fact-lbl">REGIMEN EN QUE TRIBUTA:</td><td class="fact-val">{{ $sale->regimen_fiscal ?? '—' }}</td></tr>
            <tr><td class="fact-lbl">MÉTODO PAGO:</td><td class="fact-val" style="white-space:pre-wrap;">{{ $sale->tipo_pago_label ?? '—' }}</td></tr>
            <tr><td class="fact-lbl">FORMA DE PAGO:</td><td class="fact-val">{{ $sale->forma_pago_label ?? '—' }}</td></tr>
            <tr><td class="fact-lbl">USO DE CFDI:</td><td class="fact-val">{{ $sale->uso_cfdi ?? '—' }}</td></tr>
            <tr><td class="fact-lbl">ORDEN DE COMPRA:</td><td class="fact-val">{{ $sale->orden_compra_label ?? '—' }}</td></tr>
            <tr><td class="fact-lbl">CORREO:</td><td class="fact-val">{{ $sale->emailFacturacionResuelto() }}</td></tr>
        </table>
    </td></tr>
</table>

<table class="yellow-grid">
    <tr>
        <td>
            <div class="row"><strong>NÚMERO DE PARTICIPANTES:</strong> {{ $participantes }}</div>
            <div class="row"><strong>PRECIO UNITARIO:</strong> ${{ number_format($precioUnitario, 2, '.', ',') }}</div>
            <div class="row"><strong>SUB-TOTAL:</strong> ${{ number_format($subtotal, 2, '.', ',') }}</div>
            @if($incluyeIva)
            <div class="row"><strong>IVA:</strong> ${{ number_format($iva, 2, '.', ',') }}</div>
            @endif
            <div class="row"><strong>TOTAL:</strong> ${{ number_format($total, 2, '.', ',') }}</div>
        </td>
        <td>
            <div class="row"><strong>CONDICIONES DE PAGO:</strong> @if(filled($sale->condiciones_pago)){!! nl2br(e(Str::limit($sale->condiciones_pago, 260))) !!}@else —@endif</div>
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
    SE RESERVA EL DERECHO DE ABRIR, CANCELAR O CAMBIAR DE FECHA EL INICIO CUALQUIER PROGRAMA DE CAPACITACIÓN DE ACUERDO AL NÚMERO DE PARTICIPANTES ESTO POR RAZONES LOGÍSTICAS Y PEDAGÓGICAS.
    <span class="linea2">EN CASO DE CANCELACIÓN SE DEBERÁ NOTIFICAR 4 DÍAS ANTES DEL EVENTO</span>
</div>

</td>
</tr>
</table>

</body>
</html>
