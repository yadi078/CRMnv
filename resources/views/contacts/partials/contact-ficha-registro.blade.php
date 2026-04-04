{{-- Bloque venta/facturación para ficha PDF. Visible cuando el padre abre «Crear ficha de inscripción». Requiere: $contact, $sale (nullable) --}}
<div class="md:col-span-2 mt-4 pt-6 border-t border-white/20 space-y-8">
        @if(!$sale)
            <div class="rounded-xl border border-amber-400/40 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
                Aún no hay una venta vinculada. La venta se crea automáticamente cuando el contacto pasa a estado <strong class="text-white">Vendido</strong>.
            </div>
        @else
            <fieldset class="min-w-0 border-0 p-0 m-0 space-y-8">
                <input type="hidden" name="sale_id" value="{{ $sale->id }}">

                <div>
                    <h3 class="text-lg font-semibold text-[#FFE600] mb-1">Curso / servicio y venta</h3>
                    <p class="text-sm text-white/75 mb-4">Estos datos alimentan la ficha de inscripción en PDF.</p>
                    <div class="mb-4 flex flex-wrap justify-end gap-x-10 gap-y-3 rounded-xl border border-white/15 bg-white/[0.06] px-4 py-3 text-right">
                        <div>
                            <span class="block text-[11px] font-bold uppercase tracking-wide text-white/55">Consultor</span>
                            <span class="text-sm font-semibold text-white">{{ $sale->creator?->name ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold uppercase tracking-wide text-white/55">Fecha</span>
                            <span class="text-sm font-semibold text-white">{{ $sale->fecha_venta?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-white/55 mb-3">La <strong class="text-white/80">fecha de venta</strong> (abajo) es la que se imprime como FECHA en el PDF.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="tipo_curso" value="Tipo de curso" />
                            <p class="text-xs text-white/55 mb-1">Ej. Diplomado, taller, curso cerrado, certificación, seminario.</p>
                            <x-text-input id="tipo_curso" name="tipo_curso" type="text" class="mt-1 block w-full" value="{{ old('tipo_curso', $sale->tipo_curso) }}" placeholder="Ej. Taller presencial" />
                            <x-input-error :messages="$errors->get('tipo_curso')" class="mt-2" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="nombre_servicio" value="Nombre del curso (como en la ficha PDF)" />
                            <p class="text-xs text-white/55 mb-1">Sustituya el texto automático por el nombre real del curso que compra el cliente.</p>
                            <x-text-input id="nombre_servicio" name="nombre_servicio" type="text" class="mt-1 block w-full" value="{{ old('nombre_servicio', $sale->nombre_servicio) }}" placeholder="Ej. Liderazgo efectivo para equipos" />
                            <x-input-error :messages="$errors->get('nombre_servicio')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="fecha_venta" value="Fecha de venta" />
                            <x-text-input id="fecha_venta" name="fecha_venta" type="date" class="mt-1 block w-full text-gray-900" value="{{ old('fecha_venta', $sale->fecha_venta?->format('Y-m-d')) }}" />
                            <x-input-error :messages="$errors->get('fecha_venta')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="monto" value="Monto ($)" />
                            <x-text-input id="monto" name="monto" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('monto', $sale->monto) }}" />
                        </div>
                        <div class="md:col-span-2 flex items-center gap-3">
                            <input type="hidden" name="incluye_iva" value="0">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="incluye_iva" value="1" class="rounded border-gray-300 text-amber-500 focus:ring-amber-500"
                                    {{ old('incluye_iva', $sale->incluye_iva ?? true) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-white/90">Incluir IVA en esta venta</span>
                            </label>
                        </div>
                    </div>
                </div>

                @php
                    $contactEmailPrimero = trim(explode(',', (string) ($contact->email ?? ''))[0] ?? '');
                @endphp

                <div class="rounded-xl border border-[#ca8a04]/70 bg-[#FFEB3B]/[0.12] p-4 sm:p-5">
                    <h3 class="text-lg font-semibold text-[#FFE600] mb-1">Participantes</h3>
                    <p class="text-xs text-white/75 mb-4">Si solo inscribe al contacto, verá una fila con su nombre y correo. Use <strong class="text-white">+ Agregar participante</strong> para sumar filas o suba el número.</p>
                    <div class="flex flex-wrap items-end gap-3 mb-4">
                        <div class="w-full sm:w-40">
                            <x-input-label for="participantes" value="Número de participantes" />
                            <x-text-input id="participantes" name="participantes" type="number" min="1" max="50" class="mt-1 block w-full" value="{{ old('participantes', $sale->participantes ?? 1) }}" />
                            <x-input-error :messages="$errors->get('participantes')" class="mt-2" />
                        </div>
                        <button type="button" id="btn-add-participante" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 border border-white/35 text-white text-sm font-medium hover:bg-white/25">
                            <span class="text-lg leading-none font-bold text-[#FFE600]" aria-hidden="true">+</span>
                            Agregar participante
                        </button>
                    </div>
                    <div id="participantes-datos-wrap">
                        <p class="text-xs text-white/60 mb-2">Nombre completo y correo de cada participante</p>
                        <div id="participantes-datos-list" class="space-y-4"></div>
                    </div>
                </div>

                <div class="rounded-xl border-2 border-[#ca8a04] bg-[#FFEB3B]/[0.18] p-4 sm:p-5">
                    <h3 class="text-sm font-bold text-[#071A3D] mb-3 uppercase tracking-wide">Condiciones y logística del curso</h3>
                    <p class="text-xs text-[#071A3D]/80 mb-4">Estos datos aparecen en el bloque amarillo del PDF (condiciones de pago, modalidad, sede, fecha del evento, horario, factura).</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="condiciones_pago" class="block text-sm font-medium text-white/90 mb-1">Condiciones de pago</label>
                            <textarea id="condiciones_pago" name="condiciones_pago" rows="2" class="mt-1 block w-full rounded-xl border border-white/25 bg-white text-gray-900 py-2 px-3 text-sm">{{ old('condiciones_pago', $sale->condiciones_pago) }}</textarea>
                        </div>
                        <div>
                            <label for="modalidad" class="block text-sm font-medium text-white/90 mb-1">Modalidad</label>
                            <x-text-input id="modalidad" name="modalidad" type="text" class="mt-1 block w-full" value="{{ old('modalidad', $sale->modalidad) }}" placeholder="Ej. Presencial, En línea" />
                        </div>
                        <div>
                            <label for="sede" class="block text-sm font-medium text-white/90 mb-1">Sede</label>
                            <x-text-input id="sede" name="sede" type="text" class="mt-1 block w-full" value="{{ old('sede', $sale->sede) }}" />
                        </div>
                        <div>
                            <label for="fecha_evento" class="block text-sm font-medium text-white/90 mb-1">Fecha del curso / evento</label>
                            <x-text-input id="fecha_evento" name="fecha_evento" type="date" class="mt-1 block w-full text-gray-900" value="{{ old('fecha_evento', $sale->fecha_evento?->format('Y-m-d')) }}" />
                        </div>
                        <div>
                            <label for="horario_evento" class="block text-sm font-medium text-white/90 mb-1">Horario</label>
                            <x-text-input id="horario_evento" name="horario_evento" type="text" class="mt-1 block w-full" value="{{ old('horario_evento', $sale->horario_evento) }}" placeholder="Ej. 9:00 - 14:00" />
                        </div>
                        <div class="md:col-span-2">
                            <label for="factura_referencia" class="block text-sm font-medium text-white/90 mb-1">Factura</label>
                            <x-text-input id="factura_referencia" name="factura_referencia" type="text" class="mt-1 block w-full" value="{{ old('factura_referencia', $sale->factura_referencia) }}" placeholder="Referencia o indicaciones de facturación" />
                        </div>
                    </div>
                </div>

                <div>
                    <label for="tipo_pago_ficha" class="block text-sm font-medium text-white/90 mb-1">Método de pago</label>
                    <p class="text-xs text-white/60 mb-2">Escriba el método acordado (puede combinar varios: transferencia, tarjeta, plazos, etc.).</p>
                    <textarea
                        id="tipo_pago_ficha"
                        name="tipo_pago"
                        rows="3"
                        maxlength="500"
                        class="mt-1 block w-full rounded-xl border border-white/25 bg-white text-gray-900 py-2 px-3 text-sm"
                        placeholder="Ej. 50% transferencia SPEI y 50% en efectivo al inicio del curso"
                    >{{ old('tipo_pago', $sale->tipo_pago) }}</textarea>
                    <x-input-error :messages="$errors->get('tipo_pago')" class="mt-2" />
                </div>

                <div class="mt-2 pt-6 border-t border-white/20">
                    <h3 class="text-lg font-semibold text-[#FFE600] mb-2">Datos para ficha de registro del cliente</h3>
                    <p class="text-sm text-white/80 mb-4">Razón social, domicilio y datos fiscales. El TEL en PDF se toma del teléfono o celular del contacto.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="razon_social" value="RAZÓN SOCIAL" />
                            <x-text-input id="razon_social" name="razon_social" type="text" class="mt-1 block w-full" value="{{ old('razon_social', $contact->razon_social) }}" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="nombre_comercial" value="Nombre comercial" />
                            <x-text-input id="nombre_comercial" name="nombre_comercial" type="text" class="mt-1 block w-full" value="{{ old('nombre_comercial', $contact->nombre_comercial) }}" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="calle_numero" value="CALLE Y NÚMERO" />
                            <x-text-input id="calle_numero" name="calle_numero" type="text" class="mt-1 block w-full" value="{{ old('calle_numero', $contact->calle_numero) }}" />
                        </div>
                        <div>
                            <x-input-label for="colonia_cp" value="COLONIA Y C.P. (contacto)" />
                            <x-text-input id="colonia_cp" name="colonia_cp" type="text" class="mt-1 block w-full" value="{{ old('colonia_cp', $contact->colonia_cp) }}" />
                        </div>
                        <div>
                            <x-input-label for="sale_colonia_cp" value="COLONIA Y C.P. (facturación en venta)" />
                            <x-text-input id="sale_colonia_cp" name="sale_colonia_cp" type="text" class="mt-1 block w-full" value="{{ old('sale_colonia_cp', $sale->colonia_cp) }}" />
                        </div>
                        <div>
                            <x-input-label for="rfc" value="RFC" />
                            <x-text-input id="rfc" name="rfc" type="text" class="mt-1 block w-full" value="{{ old('rfc', $contact->rfc) }}" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="regimen_fiscal" value="RÉGIMEN EN QUE TRIBUTA" />
                            <x-text-input id="regimen_fiscal" name="regimen_fiscal" type="text" class="mt-1 block w-full" value="{{ old('regimen_fiscal', $contact->regimen_fiscal) }}" />
                        </div>
                        <div>
                            <x-input-label for="uso_cfdi" value="Uso de CFDI" />
                            <x-text-input id="uso_cfdi" name="uso_cfdi" type="text" class="mt-1 block w-full" value="{{ old('uso_cfdi', $sale->uso_cfdi) }}" />
                        </div>
                        <div>
                            <x-input-label for="orden_compra" value="Orden de compra" />
                            <select id="orden_compra" name="orden_compra" class="mt-1 block w-full rounded-xl border border-white/20 bg-white text-gray-900 py-2.5 px-3 [&>option]:bg-white [&>option]:text-gray-900">
                                <option value="">—</option>
                                @foreach(\App\Models\Sale::ORDEN_COMPRA_LABELS as $valor => $etiqueta)
                                    <option value="{{ $valor }}" {{ old('orden_compra', $sale->orden_compra) == $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="sale_notas" value="Notas de la venta" />
                            <textarea id="sale_notas" name="sale_notas" rows="3" class="mt-1 block w-full rounded-xl border border-white/20 bg-white text-gray-900 py-2 px-3">{{ old('sale_notas', $sale->notas) }}</textarea>
                        </div>
                    </div>
                </div>
            </fieldset>
        @endif
</div>

@if($sale)
@php
    $participantesIniciales = [];
    if (old('participantes_nombres')) {
        foreach (old('participantes_nombres') as $i => $n) {
            $participantesIniciales[] = ['nombre' => $n, 'email' => old('participantes_emails')[$i] ?? ''];
        }
    } else {
        $participantesIniciales = $sale->saleParticipants->map(fn ($p) => ['nombre' => $p->nombre, 'email' => $p->email ?? ''])->values()->all();
        if (count($participantesIniciales) === 0) {
            $participantesIniciales = [['nombre' => $contact->nombre_completo, 'email' => $contactEmailPrimero]];
        }
    }
@endphp
<script>
(function() {
    var participantesInput = document.getElementById('participantes');
    var list = document.getElementById('participantes-datos-list');
    var btnAdd = document.getElementById('btn-add-participante');
    var serverInitial = @json($participantesIniciales);
    var defaultContact = { nombre: @json($contact->nombre_completo), email: @json($contactEmailPrimero) };

    function collectCurrentRows() {
        if (!list) return [];
        var names = list.querySelectorAll('input[name="participantes_nombres[]"]');
        var emails = list.querySelectorAll('input[name="participantes_emails[]"]');
        var out = [];
        for (var i = 0; i < names.length; i++) {
            out.push({ nombre: names[i].value, email: emails[i] ? emails[i].value : '' });
        }
        return out;
    }

    function mergeData(n) {
        var preserved = collectCurrentRows();
        var merged = [];
        for (var i = 0; i < n; i++) {
            if (preserved[i]) {
                merged[i] = preserved[i];
            } else if (serverInitial[i]) {
                merged[i] = serverInitial[i];
            } else if (i === 0) {
                merged[i] = defaultContact;
            } else {
                merged[i] = { nombre: '', email: '' };
            }
        }
        serverInitial = merged;
        return merged;
    }

    function buildRows(n) {
        if (!list) return;
        if (n < 1) n = 1;
        if (n > 50) n = 50;
        var rows = mergeData(n);
        list.innerHTML = '';
        for (var i = 0; i < n; i++) {
            var data = rows[i] || { nombre: '', email: '' };
            var div = document.createElement('div');
            div.className = 'rounded-lg border border-white/15 bg-white/5 p-3 grid grid-cols-1 md:grid-cols-2 gap-3';
            div.innerHTML = '<p class="md:col-span-2 text-sm font-medium text-[#FFE600]">Participante ' + (i + 1) + '</p>' +
                '<div><label class="block text-xs text-white/70 mb-1">Nombre completo</label><input type="text" name="participantes_nombres[]" maxlength="100" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\\s]+" title="Solo letras y espacios." class="mt-1 block w-full rounded-md border-gray-300 text-gray-900 py-2 px-2" value="' + String(data.nombre || '').replace(/"/g, '&quot;') + '"></div>' +
                '<div><label class="block text-xs text-white/70 mb-1">Correo</label><input type="email" name="participantes_emails[]" class="mt-1 block w-full rounded-md border-gray-300 text-gray-900 py-2 px-2" value="' + String(data.email || '').replace(/"/g, '&quot;') + '"></div>';
            list.appendChild(div);
        }
    }

    function updateParticipantesSection() {
        if (!participantesInput) return;
        var n = parseInt(participantesInput.value, 10) || 1;
        if (n < 1) n = 1;
        if (n > 50) n = 50;
        participantesInput.value = n;
        buildRows(n);
    }

    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            var n = (parseInt(participantesInput.value, 10) || 1) + 1;
            if (n > 50) n = 50;
            participantesInput.value = n;
            updateParticipantesSection();
        });
    }

    if (participantesInput) {
        participantesInput.addEventListener('input', updateParticipantesSection);
        participantesInput.addEventListener('change', updateParticipantesSection);
        updateParticipantesSection();
    }
})();
</script>
@endif
