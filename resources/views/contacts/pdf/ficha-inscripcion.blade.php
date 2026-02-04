<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha de Inscripción - {{ $contact->nombre_completo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000836;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #003366;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #003366;
        }
        .slogan {
            font-size: 12px;
            color: #808080;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #003366;
            color: white;
            padding: 8px 15px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
            color: #000836;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #808080;
            border-top: 1px solid #808080;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">C&CE CONSULTORÍA</div>
        <div class="slogan">INVERTIR EN VALOR ¡ATRAE VALOR!</div>
    </div>

    <div class="section">
        <div class="section-title">FICHA DE INSCRIPCIÓN</div>
        
        <div class="info-row">
            <div class="info-label">Nombre Completo:</div>
            <div class="info-value">{{ $contact->nombre_completo }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Empresa:</div>
            <div class="info-value">{{ $contact->company->nombre_comercial }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">RFC:</div>
            <div class="info-value">{{ $contact->company->rfc }}</div>
        </div>
        
        @if($contact->puesto_de_trabajo)
        <div class="info-row">
            <div class="info-label">Puesto:</div>
            <div class="info-value">{{ $contact->puesto_de_trabajo }}</div>
        </div>
        @endif
        
        @if($contact->departamento)
        <div class="info-row">
            <div class="info-label">Departamento:</div>
            <div class="info-value">{{ $contact->departamento }}</div>
        </div>
        @endif
        
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $contact->email }}</div>
        </div>
        
        @if($contact->celular)
        <div class="info-row">
            <div class="info-label">Celular:</div>
            <div class="info-value">{{ $contact->celular }}</div>
        </div>
        @endif
        
        @if($contact->extension)
        <div class="info-row">
            <div class="info-label">Extensión:</div>
            <div class="info-value">{{ $contact->extension }}</div>
        </div>
        @endif
        
        @if($contact->municipio || $contact->estado)
        <div class="info-row">
            <div class="info-label">Ubicación:</div>
            <div class="info-value">{{ $contact->municipio ?? '' }}{{ $contact->municipio && $contact->estado ? ', ' : '' }}{{ $contact->estado ?? '' }}</div>
        </div>
        @endif
        
        @if($contact->notas)
        <div class="info-row">
            <div class="info-label">Notas:</div>
            <div class="info-value">{{ $contact->notas }}</div>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>C&CE Consultoría y Capacitación Empresarial</p>
        <p>Email: info@cactonultoricempresarial.com | Teléfono: (330) 0244-3678</p>
        <p>Horario de atención: Lunes a viernes de 8 am a 6 pm</p>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
