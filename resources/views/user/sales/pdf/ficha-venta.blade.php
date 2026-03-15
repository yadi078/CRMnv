<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha de Inscripción{{ Str::startsWith($sale->nombre_servicio ?? '', 'Venta desde contacto:') ? '' : ' - ' . $sale->nombre_servicio }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #071A3D; padding: 20px; font-size: 12px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 2px solid #071A3D; padding-bottom: 15px; }
        .logo-text { font-size: 18px; font-weight: bold; color: #071A3D; }
        .slogan { font-size: 11px; color: #071A3D; font-weight: bold; margin-top: 4px; }
        .consultor { font-size: 10px; margin-top: 8px; }
        .section-title { background: #071A3D; color: white; padding: 8px 12px; font-weight: bold; text-align: center; margin-bottom: 0; }
        .section-title.gray { background: #e5e7eb; color: #374151; border: 1px solid #d1d5db; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data th, table.data td { border: 1px solid #9ca3af; padding: 8px 10px; text-align: left; }
        table.data th { background: #f3f4f6; font-weight: bold; width: 180px; }
        table.data td { color: #111; }
        .curso-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .curso-table th, .curso-table td { border: 1px solid #9ca3af; padding: 10px; }
        .curso-table th { background: #071A3D; color: white; }
        .curso-table .participantes { background: #fef9c3; }
        .yellow-box { background: #fef9c3; padding: 15px; border: 1px solid #e5e7eb; margin-bottom: 15px; }
        .yellow-box .row { margin-bottom: 6px; }
        .aviso { background: #071A3D; color: white; padding: 12px; text-align: center; margin-top: 20px; font-size: 10px; }
        .aviso .amarillo { color: #FFE600; font-weight: bold; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #9ca3af; font-size: 9px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="logo-text">CE CONSULTORÍA</div>
            <div class="slogan">CONSULTORÍA Y CAPACITACIÓN EMPRESARIAL</div>
        </div>
        <div style="text-align: right;">
            <div class="slogan">INVERTIR EN VALOR ¡ATRAE VALOR!</div>
            <div class="consultor"><strong>CONSULTOR:</strong> {{ $sale->creator?->name ?? '—' }}</div>
            <div class="consultor"><strong>FECHA:</strong> {{ $sale->fecha_venta->format('d/m/Y') }}</div>
        </div>
    </div>

    @if(!Str::startsWith($sale->nombre_servicio ?? '', 'Venta desde contacto:'))
    <h2 class="section-title">FICHA DE INSCRIPCIÓN</h2>
    <table class="curso-table">
        <tr>
            <th style="width: 50%;">CURSO</th>
            <th class="participantes" style="width: 50%;">PARTICIPANTES</th>
        </tr>
        <tr>
            <td style="background: #f8fafc;">1. {{ $sale->nombre_servicio }}</td>
            <td class="participantes">
                @if($sale->saleParticipants->isNotEmpty())
                    @foreach($sale->saleParticipants as $p)
                        <div style="margin-bottom: 6px;">
                            <strong>{{ $p->nombre }}</strong>
                            @if($p->email)<br><span style="font-size: 10px; color: #555;">{{ $p->email }}</span>@endif
                        </div>
                    @endforeach
                @elseif($sale->contact)
                    {{ $sale->contact->nombre_completo }}
                    @if($sale->contact->puesto_de_trabajo)<br><span style="font-size: 10px; color: #555;">{{ $sale->contact->puesto_de_trabajo }}</span>@endif
                    @if($sale->participantes && $sale->participantes > 1)<br><span style="font-size: 10px;">({{ $sale->participantes }} participantes)</span>@endif
                @else
                    {{ $sale->participantes ? $sale->participantes . ' participante(s)' : '—' }}
                @endif
            </td>
        </tr>
    </table>
    @endif

    <h2 class="section-title gray">DATOS DE FACTURACIÓN</h2>
    <table class="data">
        <tr><th>RAZÓN SOCIAL:</th><td>{{ $sale->company->nombre_comercial }}</td></tr>
        <tr><th>CALLE Y NÚMERO:</th><td>{{ Str::limit($sale->company->datos_fiscales, 100) ?: '—' }}</td></tr>
        <tr><th>COLONIA Y C.P.:</th><td>{{ $sale->colonia_cp ?? '—' }}</td></tr>
        <tr><th>CIUDAD, ESTADO:</th><td>{{ trim(($sale->company->municipio ?? '') . ', ' . ($sale->company->estado ?? ''), ' ,') ?: '—' }}</td></tr>
        <tr><th>RFC:</th><td>{{ $sale->company->rfc ?? '—' }}</td></tr>
        <tr><th>TEL:</th><td>{{ $sale->contact?->celular ?? $sale->contact?->telefono ?? '—' }}</td></tr>
        <tr><th>REGIMEN EN QUE TRIBUTA:</th><td>{{ $sale->regimen_fiscal ?? '—' }}</td></tr>
        <tr><th>MÉTODO PAGO:</th><td>{{ $sale->tipo_pago_label ?? '—' }}</td></tr>
        <tr><th>FORMA DE PAGO:</th><td>{{ $sale->forma_pago_label ?? '—' }}</td></tr>
        <tr><th>USO DE CFDI:</th><td>{{ $sale->uso_cfdi ?? '—' }}</td></tr>
        <tr><th>ORDEN DE COMPRA:</th><td>{{ $sale->orden_compra_label ?? '—' }}</td></tr>
        <tr><th>CORREO:</th><td>{{ $sale->contact?->email ?? '—' }}</td></tr>
    </table>

    @php
        $participantes = (int) ($sale->participantes ?? 1);
        $precioUnitario = $sale->monto ? (float) $sale->monto : 0;
        $subtotal = $participantes > 0 ? $precioUnitario * $participantes : $precioUnitario;
        $incluyeIva = $sale->incluye_iva ?? true;
        $iva = $incluyeIva ? round($subtotal * 0.16, 2) : 0;
        $total = $subtotal + $iva;
    @endphp
    <div class="yellow-box">
        <div class="row"><strong>NÚMERO DE PARTICIPANTES:</strong> {{ $participantes }}</div>
        <div class="row"><strong>PRECIO UNITARIO:</strong> $ {{ number_format($precioUnitario, 2, '.', ',') }}</div>
        <div class="row"><strong>SUB-TOTAL:</strong> $ {{ number_format($subtotal, 2, '.', ',') }}</div>
        @if($incluyeIva)
        <div class="row"><strong>IVA (16%):</strong> $ {{ number_format($iva, 2, '.', ',') }}</div>
        @endif
        <div class="row"><strong>TOTAL:</strong> $ {{ number_format($total, 2, '.', ',') }}</div>
    </div>
    <div class="yellow-box">
        <div class="row"><strong>CONDICIONES DE PAGO:</strong> —</div>
        <div class="row"><strong>MODALIDAD:</strong> —</div>
        <div class="row"><strong>SEDE:</strong> —</div>
        <div class="row"><strong>FECHA:</strong> {{ $sale->fecha_venta->format('d/m/Y') }}</div>
        <div class="row"><strong>HORARIO:</strong> —</div>
        <div class="row"><strong>FACTURA:</strong> —</div>
    </div>

    <div class="aviso">
        <div class="amarillo">CE CONSULTORIA Y CAPACITACIÓN EMPRESARIAL</div>
        <p style="margin: 8px 0;">SE RESERVA EL DERECHO DE ABRIR, CANCELAR O CAMBIAR DE FECHA EL INICIO CUALQUIER PROGRAMA DE CAPACITACIÓN DE ACUERDO AL NÚMERO DE PARTICIPANTES ESTO POR RAZONES LOGÍSTICAS Y PEDAGÓGICAS.</p>
        <div class="amarillo">EN CASO DE CANCELACION SE DEBERA NOTIFICAR 4 DIAS ANTES DEL EVENTO</div>
    </div>

    <div class="footer">
        <p>AV. AYUNTAMIENTO 413, INT. 103 FRACC. CEDROS - C.P. 20270 AGUASCALIENTES, AGS. | www.ceconsultoriaempresarial.com</p>
        <p>CONTÁCTANOS: 449 664 52 90 | 449 425 41 54 | CE Consultoria Empresarial</p>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
