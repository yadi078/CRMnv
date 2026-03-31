<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Ficha de venta</h2>
            @if(!Str::startsWith($sale->nombre_servicio ?? '', 'Venta desde contacto:'))
                <p class="page-header-card__subtitle">{{ $sale->nombre_servicio }}</p>
            @endif
        </div>
        <div class="flex gap-2 ml-auto">
            @can('sales.edit')
            <a href="{{ route('user.sales.edit', $sale) }}" class="btn-amber-app">Editar</a>
            @endcan
            <a href="{{ route('user.sales.ficha-pdf', $sale) }}" target="_blank" class="btn-amber-app inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Descargar PDF
            </a>
            <a href="{{ route('user.sales.ficha-word', $sale) }}" target="_blank" class="btn-panel-dark bg-white text-[#071A3D] border border-[#071A3D] hover:bg-gray-100 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16v16H4z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 8l2 8 3-6 3 6 2-8" /></svg>
                Word
            </a>
        </div>
    </x-slot>

    @if(session('sale_created'))
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/60">
            <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-lg w-full text-center">
                <h2 class="text-2xl font-extrabold text-[#071A3D] mb-4">Registro de venta exitoso</h2>
                <p class="text-base text-gray-700 mb-6">La venta se guardó correctamente. Ahora puedes descargar la ficha de inscripción o finalizar.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('user.sales.index') }}" class="px-5 py-3 rounded-xl border border-gray-300 text-gray-800 font-semibold hover:bg-gray-50">
                        Finalizar
                    </a>
                    <a href="{{ route('user.sales.ficha-pdf', $sale) }}" target="_blank" class="px-5 py-3 rounded-xl bg-[#FFE600] text-[#071A3D] font-semibold shadow hover:bg-yellow-300">
                        Descargar ficha de inscripción (PDF)
                    </a>
                    <a href="{{ route('user.sales.ficha-word', $sale) }}" target="_blank" class="px-5 py-3 rounded-xl bg-white text-[#071A3D] font-semibold shadow border border-gray-300 hover:bg-gray-50">
                        Descargar ficha de inscripción (Word)
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden print:shadow-none print:rounded-none" id="ficha-venta">
        {{-- Encabezado --}}
        <div class="flex flex-wrap items-start justify-between gap-4 p-6 pb-4 border-b-2 border-[#071A3D] print:break-inside-avoid">
            <div class="flex items-center gap-3">
                <img src="{{ asset('img/logo.png') }}" alt="CE" class="h-16 w-16 object-contain print:h-14 print:w-14">
                <div>
                    <p class="text-xs text-gray-600 font-medium uppercase tracking-wide">CE Consultoría</p>
                    <p class="text-[10px] text-gray-500">CONSULTORÍA Y CAPACITACIÓN EMPRESARIAL</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-[#071A3D] uppercase">INVERTIR EN VALOR ¡ATRAE VALOR!</p>
                <p class="text-xs text-gray-700 mt-2"><span class="font-semibold">CONSULTOR:</span> {{ $sale->creator?->name ?? '—' }}</p>
                <p class="text-xs text-gray-700"><span class="font-semibold">FECHA:</span> {{ $sale->fecha_venta->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- FICHA DE INSCRIPCIÓN (no se muestra cuando es "Venta desde contacto") --}}
        @if(!Str::startsWith($sale->nombre_servicio ?? '', 'Venta desde contacto:'))
        <div class="p-6 print:break-inside-avoid">
            <h2 class="text-center font-bold text-lg text-[#071A3D] mb-4">FICHA DE INSCRIPCIÓN</h2>
            <table class="w-full border border-gray-300 text-sm">
                <thead>
                    <tr>
                        <th class="bg-[#071A3D] text-white px-4 py-2 text-left font-semibold">CURSO</th>
                        <th class="bg-[#FFE600] text-[#071A3D] px-4 py-2 text-left font-semibold w-1/2">PARTICIPANTES</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 px-4 py-3 align-top bg-[#071A3D]/5">
                            <span class="font-medium text-[#071A3D]">1.</span> {{ $sale->nombre_servicio }}
                        </td>
                        <td class="border border-gray-300 px-4 py-3 align-top bg-gray-50 min-h-[80px] text-gray-800">
                            @if($sale->saleParticipants->isNotEmpty())
                                @foreach($sale->saleParticipants as $p)
                                    <div class="mb-1">
                                        <span class="font-medium text-gray-900">{{ $p->nombre }}</span>
                                        @if($p->email)
                                            <br><span class="text-xs text-gray-600">{{ $p->email }}</span>
                                        @endif
                                    </div>
                                @endforeach
                                @if($sale->participantes && $sale->participantes > $sale->saleParticipants->count())
                                    <span class="text-xs text-gray-500">({{ $sale->participantes }} participantes)</span>
                                @endif
                            @elseif($sale->contact)
                                {{ $sale->contact->nombre_completo }}
                                @if($sale->contact->puesto_de_trabajo)
                                    <br><span class="text-gray-600 text-xs">{{ $sale->contact->puesto_de_trabajo }}</span>
                                @endif
                                @if($sale->participantes && $sale->participantes > 1)
                                    <br><span class="text-xs text-gray-500">({{ $sale->participantes }} participantes)</span>
                                @endif
                            @else
                                {{ $sale->participantes ? $sale->participantes . ' participante(s)' : '—' }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        {{-- DATOS DE FACTURACIÓN --}}
        <div class="p-6 pt-0 print:break-inside-avoid">
            <h2 class="text-center font-bold text-lg text-[#071A3D] mb-4 border border-gray-300 rounded-t-lg px-4 py-2 bg-gray-50">DATOS DE FACTURACIÓN</h2>
            <table class="w-full border border-gray-300 text-sm">
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700 w-48">RAZÓN SOCIAL:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ $sale->company->nombre_comercial }}</td></tr>
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700">CALLE Y NÚMERO:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ Str::limit($sale->company->datos_fiscales, 80) ?: '—' }}</td></tr>
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700">COLONIA Y C.P.:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ $sale->colonia_cp ?? '—' }}</td></tr>
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700">CIUDAD, ESTADO:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ trim(($sale->company->municipio ?? '') . ', ' . ($sale->company->estado ?? ''), ' ,') ?: '—' }}</td></tr>
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700">RFC:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ $sale->company->rfc ?? '—' }}</td></tr>
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700">TEL:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ $sale->contact?->celular ?? $sale->contact?->telefono ?? '—' }}</td></tr>
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700">REGIMEN EN QUE TRIBUTA:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ $sale->regimen_fiscal ?? '—' }}</td></tr>
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700">MÉTODO PAGO:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ $sale->tipo_pago_label ?? '—' }}</td></tr>
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700">FORMA DE PAGO:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ $sale->forma_pago_label ?? '—' }}</td></tr>
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700">USO DE CFDI:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ $sale->uso_cfdi ?? '—' }}</td></tr>
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700">ORDEN DE COMPRA:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ $sale->orden_compra_label ?? '—' }}</td></tr>
                <tr><td class="border border-gray-300 px-4 py-2 bg-gray-50 font-semibold text-gray-700">CORREO:</td><td class="border border-gray-300 px-4 py-2 text-gray-900">{{ $sale->contact?->email ?? '—' }}</td></tr>
            </table>
        </div>

        @php
            $participantes = (int) ($sale->participantes ?? 1);
            $precioUnitario = $sale->monto ? (float) $sale->monto : 0;
            $subtotal = $participantes > 0 ? $precioUnitario * $participantes : $precioUnitario;
            $incluyeIva = $sale->incluye_iva ?? true;
            $iva = $incluyeIva ? round($subtotal * 0.16, 2) : 0;
            $total = $subtotal + $iva;
        @endphp

        {{-- Resumen y condiciones (fondo amarillo) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-0 border-t-2 border-[#071A3D] print:break-inside-avoid">
            <div class="bg-[#FFE600]/30 p-6 border-b md:border-b-0 md:border-r border-gray-300">
                <p class="text-sm text-gray-900"><span class="font-semibold">NÚMERO DE PARTICIPANTES:</span> <span class="font-bold text-[#071A3D]">{{ $participantes }}</span></p>
                <p class="text-sm text-gray-900 mt-2"><span class="font-semibold">PRECIO UNITARIO:</span> <span class="font-bold text-[#071A3D]">$ {{ number_format($precioUnitario, 2, '.', ',') }}</span></p>
                <p class="text-sm text-gray-900 mt-1"><span class="font-semibold">SUB-TOTAL:</span> <span class="font-bold text-[#071A3D]">$ {{ number_format($subtotal, 2, '.', ',') }}</span></p>
                @if($incluyeIva)
                    <p class="text-sm text-gray-900 mt-1"><span class="font-semibold">IVA (16%):</span> <span class="font-bold text-[#071A3D]">$ {{ number_format($iva, 2, '.', ',') }}</span></p>
                @endif
                <p class="text-sm text-gray-900 mt-2 font-bold"><span class="font-semibold">TOTAL:</span> <span class="text-[#071A3D] text-lg">$ {{ number_format($total, 2, '.', ',') }}</span></p>
            </div>
            <div class="bg-[#FFE600]/30 p-6">
                <p class="text-sm text-gray-900"><span class="font-semibold">CONDICIONES DE PAGO:</span> <span class="text-[#071A3D]">—</span></p>
                <p class="text-sm text-gray-900 mt-2"><span class="font-semibold">MODALIDAD:</span> <span class="text-[#071A3D]">—</span></p>
                <p class="text-sm text-gray-900 mt-2"><span class="font-semibold">SEDE:</span> <span class="text-[#071A3D]">—</span></p>
                <p class="text-sm text-gray-900 mt-2"><span class="font-semibold">FECHA:</span> <span class="text-[#071A3D] font-medium">{{ $sale->fecha_venta->format('d/m/Y') }}</span></p>
                <p class="text-sm text-gray-900 mt-2"><span class="font-semibold">HORARIO:</span> <span class="text-[#071A3D]">—</span></p>
                <p class="text-sm text-gray-900 mt-2"><span class="font-semibold">FACTURA:</span> <span class="text-[#071A3D]">—</span></p>
            </div>
        </div>

        {{-- Aviso (fondo azul) --}}
        <div class="bg-[#071A3D] p-6 text-center print:break-inside-avoid">
            <p class="text-[#FFE600] font-bold text-sm uppercase">CE CONSULTORIA Y CAPACITACIÓN EMPRESARIAL</p>
            <p class="text-white text-xs mt-2 leading-relaxed">SE RESERVA EL DERECHO DE ABRIR, CANCELAR O CAMBIAR DE FECHA EL INICIO CUALQUIER PROGRAMA DE CAPACITACIÓN DE ACUERDO AL NÚMERO DE PARTICIPANTES ESTO POR RAZONES LOGÍSTICAS Y PEDAGÓGICAS.</p>
            <p class="text-[#FFE600] font-semibold text-xs mt-3">EN CASO DE CANCELACION SE DEBERA NOTIFICAR 4 DIAS ANTES DEL EVENTO</p>
        </div>

        {{-- Pie --}}
        <div class="bg-[#071A3D] px-6 py-4 flex flex-wrap items-center justify-between gap-4 text-white text-xs print:break-inside-avoid">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#FFE600] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span>AV. AYUNTAMIENTO 413, INT. 103 FRACC. CEDROS - C.P. 20270 AGUASCALIENTES, AGS.</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#FFE600] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9a9 9 0 009 9m-9-9a9 9 0 009-9" /></svg>
                <a href="https://www.ceconsultoriaempresarial.com" target="_blank" rel="noopener" class="text-[#FFE600] hover:underline">www.ceconsultoriaempresarial.com</a>
            </div>
            <div class="flex items-center gap-3">
                <span class="font-semibold text-[#FFE600]">CONTÁCTANOS:</span>
                <span class="text-gray-200">449 664 52 90</span>
                <span class="text-gray-200">449 425 41 54</span>
                <span class="text-gray-200">CE Consultoria Empresarial</span>
            </div>
        </div>

        {{-- Acciones solo en pantalla (no imprimir) --}}
        @can('sales.delete')
        <div class="p-6 border-t border-gray-200 print:hidden">
            <form action="{{ route('user.sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro de venta?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">Eliminar registro de venta</button>
            </form>
        </div>
        @endcan
    </div>

    <style>
        @media print {
            body { background: #fff; }
            .print\:break-inside-avoid { break-inside: avoid; }
            #ficha-venta { box-shadow: none; max-width: 100%; }
        }
    </style>
</x-app-user-layout>
