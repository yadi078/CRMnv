<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha de Inscripción - {{ $contact->nombre_completo }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #1f2937;
            padding: 18px 22px;
            font-size: 10px;
            line-height: 1.45;
        }
        .header { text-align: center; margin-bottom: 18px; }
        .header-title {
            font-size: 17px;
            font-weight: bold;
            color: #1A3B66;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .header-slogan {
            font-size: 10px;
            color: #6b7280;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 6px;
            letter-spacing: 0.04em;
        }
        .header-rule {
            height: 4px;
            background: #1A3B66;
            width: 100%;
            margin: 12px 0 16px;
        }
        .section-title {
            background: #1A3B66;
            color: #fff;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0;
        }
        table.block { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        table.block td { padding: 6px 8px; border: 1px solid #d1d5db; vertical-align: top; }
        table.block td.lbl {
            width: 38%;
            font-weight: bold;
            color: #1A3B66;
            text-transform: uppercase;
            font-size: 9px;
            background: #f9fafb;
        }
        table.block td.val { color: #111827; font-size: 10px; }
        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            padding-top: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-title">C&amp;CE CONSULTORÍA</div>
        <div class="header-slogan">INVERTIR EN VALOR ¡ATRAE VALOR!</div>
        <div class="header-rule"></div>
    </div>

    <div class="section-title">FICHA DE INSCRIPCIÓN</div>
    <table class="block">
        <tr>
            <td class="lbl">Nombre completo</td>
            <td class="val">{{ $contact->nombre_completo }}</td>
        </tr>
        @if($contact->genero)
        <tr>
            <td class="lbl">Género</td>
            <td class="val">{{ $contact->genero }}</td>
        </tr>
        @endif
        <tr>
            <td class="lbl">Empresa</td>
            <td class="val">{{ $contact->company->nombre_comercial }}</td>
        </tr>
    </table>

    <div class="section-title">DATOS PARA FICHA DE REGISTRO / FACTURACIÓN</div>
    <table class="block">
        <tr>
            <td class="lbl">RAZÓN SOCIAL</td>
            <td class="val">{{ $contact->razon_social ?? $contact->company->nombre_comercial ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Nombre comercial</td>
            <td class="val">{{ $contact->nombre_comercial ?? $contact->company->nombre_comercial ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">CALLE Y NÚMERO</td>
            <td class="val">{{ $contact->calle_numero ?? $contact->company->datos_fiscales ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">COLONIA Y C.P.</td>
            <td class="val">{{ $contact->colonia_cp ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">CIUDAD</td>
            <td class="val">{{ $contact->municipio ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">ESTADO</td>
            <td class="val">{{ $contact->estado ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">RFC</td>
            <td class="val">{{ $contact->rfc ?? $contact->company->rfc ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">TEL</td>
            <td class="val">{{ $contact->celular ?? $contact->telefono ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">RÉGIMEN EN QUE TRIBUTA</td>
            <td class="val">{{ $contact->regimen_fiscal ?? '—' }}</td>
        </tr>
        @if($contact->puesto_de_trabajo)
        <tr>
            <td class="lbl">Puesto</td>
            <td class="val">{{ $contact->puesto_de_trabajo }}</td>
        </tr>
        @endif
        @if($contact->departamento)
        <tr>
            <td class="lbl">Departamento</td>
            <td class="val">{{ $contact->departamento }}</td>
        </tr>
        @endif
        <tr>
            <td class="lbl">Correo electrónico</td>
            <td class="val">{{ $contact->email_activo ? $contact->email : 'Correo desactivado' }}</td>
        </tr>
        @if($contact->telefono)
        <tr>
            <td class="lbl">Teléfono</td>
            <td class="val">{{ $contact->telefono }}</td>
        </tr>
        @endif
        @if($contact->celular)
        <tr>
            <td class="lbl">Celular</td>
            <td class="val">{{ $contact->celular }}</td>
        </tr>
        @endif
        @if($contact->extension)
        <tr>
            <td class="lbl">Extensión</td>
            <td class="val">{{ $contact->extension }}</td>
        </tr>
        @endif
        @if($contact->notas)
        <tr>
            <td class="lbl">Notas</td>
            <td class="val">{{ $contact->notas }}</td>
        </tr>
        @endif
    </table>

    <div class="footer">
        <p style="margin:0 0 4px;"><strong>C&amp;CE Consultoría y Capacitación Empresarial</strong></p>
        <p style="margin:0;">Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
