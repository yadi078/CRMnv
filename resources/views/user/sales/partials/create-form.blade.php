{{-- Formulario compartido: misma vista admin (sales.create) y usuario (user.sales.create) --}}
<div class="panel-card-dark p-6 text-base md:text-lg">
    <form id="form-registrar-venta" method="POST" action="{{ route('user.sales.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Fila 1: Tipo de curso (2/3) + Fecha (1/3); nombre_servicio se deriva en el servidor --}}
            <div class="md:col-span-2">
                <label for="tipo_curso" class="mb-1 flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                    <span class="text-base md:text-lg font-semibold text-white">Nombre del curso o servicio *</span>
                    <span class="text-xs md:text-sm font-normal text-white/60">Se muestra en la ficha de inscripción.</span>
                </label>
                <x-text-input id="tipo_curso" name="tipo_curso" type="text" class="mt-1 block w-full" :value="old('tipo_curso')" placeholder="Ej. Capacitación en ventas, diplomado ejecutivo" required />
                <x-input-error :messages="$errors->get('tipo_curso')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="fecha_venta" value="Fecha de venta *" class="text-base md:text-lg font-semibold text-white" />
                <x-text-input
                    id="fecha_venta"
                    name="fecha_venta"
                    type="date"
                    class="mt-1 block w-full text-black"
                    style="background-color:#ffffff;color:#000000;"
                    :value="old('fecha_venta', date('Y-m-d'))"
                    min="{{ date('Y-m-d') }}"
                    required
                />
                <x-input-error :messages="$errors->get('fecha_venta')" class="mt-2" />
            </div>

            {{-- Empresa + Contacto --}}
            <div class="md:col-span-2">
                <x-input-label for="company_name" value="Empresa *" class="text-base md:text-lg font-semibold text-white" />
                @php
                    $selectedCompanyId = old('company_id', $companyId ?? null);
                    $selectedCompany = $companies->firstWhere('id', $selectedCompanyId);
                @endphp
                <div class="relative">
                    <input
                        id="company_name"
                        name="company_name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900"
                        value="{{ old('company_name', $selectedCompany->nombre_comercial ?? '') }}"
                        placeholder="Empieza a escribir el nombre de la empresa"
                        autocomplete="off"
                        required
                    />
                    <div
                        id="company_suggestions"
                        class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto text-sm hidden"
                    >
                    </div>
                </div>
                <input type="hidden" id="company_id" name="company_id" value="{{ $selectedCompanyId }}">
                <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contact_id" value="Contacto" class="text-base md:text-lg font-semibold text-white" />
                <select id="contact_id" name="contact_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900">
                    <option value="">Ninguno / No especificado</option>
                    @foreach($contacts as $c)
                    <option value="{{ $c->id }}" {{ (string) old('contact_id', $prefillContactId ?? '') === (string) $c->id ? 'selected' : '' }}>{{ $c->nombre_completo }}{{ $c->puesto_de_trabajo ? ' — ' . $c->puesto_de_trabajo : '' }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('contact_id')" class="mt-2" />
            </div>

            {{-- Misma fila desde sm: flex evita depender de lg (con sidebar el ancho útil suele ser <1024px) --}}
            <div class="md:col-span-3 flex w-full min-w-0 flex-col gap-5 sm:flex-row sm:items-end sm:gap-4 md:gap-5">
                <div class="min-w-0 w-full sm:flex-[11] sm:basis-0">
                    <x-input-label for="monto" value="Monto ($)" class="text-base md:text-lg font-semibold text-white" />
                    <x-text-input id="monto" name="monto" type="number" step="0.01" min="0" class="mt-1 block w-full py-2.5 text-base" :value="old('monto')" placeholder="0.00" />
                    <x-input-error :messages="$errors->get('monto')" class="mt-2" />
                </div>
                <div class="min-w-0 w-full sm:flex-[9] sm:basis-0">
                    <x-input-label for="tipo_pago" value="Método de pago" class="text-base md:text-lg font-semibold text-white" />
                    <textarea
                        id="tipo_pago"
                        name="tipo_pago"
                        rows="2"
                        maxlength="500"
                        class="mt-1 block w-full min-h-[3.25rem] rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900 text-sm py-2 resize-y"
                        placeholder="Ej. SPEI, efectivo…"
                    >{{ old('tipo_pago') }}</textarea>
                    <x-input-error :messages="$errors->get('tipo_pago')" class="mt-2" />
                </div>
                <div class="min-w-0 w-full sm:w-[9.5rem] sm:flex-shrink-0 sm:flex-grow-0">
                    <x-input-label for="participantes" value="Participantes" class="text-sm font-medium text-white md:text-base" />
                    <x-text-input id="participantes" name="participantes" type="number" min="1" class="mt-1 block w-full py-2.5 text-base" :value="old('participantes')" placeholder="—" />
                    <x-input-error :messages="$errors->get('participantes')" class="mt-2" />
                </div>
            </div>

            {{-- IVA debajo: checkbox y textos más grandes --}}
            <div class="md:col-span-3">
                <span class="block text-base md:text-lg font-semibold text-white mb-2">IVA</span>
                <label class="flex flex-col gap-2 rounded-xl border border-white/20 bg-white/[0.06] px-4 py-3.5 cursor-pointer w-full max-w-2xl">
                    <span class="inline-flex items-center gap-3">
                        <input type="hidden" name="incluye_iva" value="0">
                        <input type="checkbox" name="incluye_iva" value="1" class="rounded-md border-2 border-gray-300 text-amber-500 focus:ring-amber-500 focus:ring-offset-0 h-6 w-6 shrink-0"
                            {{ old('incluye_iva', true) ? 'checked' : '' }}>
                        <span class="text-base md:text-lg font-semibold text-white leading-snug">Incluir IVA en esta venta</span>
                    </span>
                    <span class="text-sm md:text-base text-white/70 leading-snug pl-9">Desmarque si el monto no lleva IVA (ej. factura exenta).</span>
                </label>
            </div>

            <div class="md:col-span-3">
                <x-input-label for="participantes_texto" value="Nombre y correo de participantes" class="text-base md:text-lg font-semibold text-white" />
                <p class="mt-1 mb-2 text-xs md:text-sm text-white/60 leading-relaxed">
                    Una línea por participante. Puede separar nombre y correo con coma o guión. Por ejemplo:<br>
                    <span class="text-white/75">Yadira Suarez, yadi@gmail.com</span><br>
                    <span class="text-white/75">Columba Silva - columba@gmail.com</span>
                </p>
                <textarea id="participantes_texto" name="participantes_texto" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900" placeholder="Una línea por participante (nombre y correo).">{{ old('participantes_texto') }}</textarea>
            </div>

            <div class="md:col-span-3">
                <x-input-label for="notas" value="Notas" class="text-base md:text-lg font-semibold text-white" />
                <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
                <x-input-error :messages="$errors->get('notas')" class="mt-2" />
            </div>
        </div>

        {{-- PASO 2: Datos de facturación --}}
        <div id="step-datos-facturacion" class="mt-8 pt-6 border-t border-white/20 hidden">
            <h3 class="text-xl font-semibold text-[#FFE600] mb-4">Datos de facturación</h3>
            <p class="text-base text-white/80 mb-4">Estos datos se mostrarán en la ficha final. Se llenan con la empresa y el contacto seleccionados, pero puede ajustar la información manualmente.</p>
            @php
                $calleFactCreate = old('facturacion_calle_numero', $contact?->calle_numero ?? $company?->datos_fiscales ?? '');
                $rfcFactCreate = old('facturacion_rfc', $contact?->rfc ?? $company?->rfc ?? '');
                $emailFactCreate = old('email_facturacion', $contact?->email ?? '');
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-white/5 border border-white/10">
                <div class="md:col-span-2">
                    <span class="text-sm font-medium text-white/70 uppercase">Razón social</span>
                    <p class="text-white font-semibold mt-0.5">
                        {{ $contact?->razon_social ?? $company?->nombre_comercial ?? '—' }}
                    </p>
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="facturacion_calle_numero" value="Domicilio fiscal (calle y número)" />
                    <x-text-input
                        id="facturacion_calle_numero"
                        name="facturacion_calle_numero"
                        type="text"
                        class="mt-1 block w-full text-gray-900"
                        :value="$calleFactCreate"
                        placeholder="Calle, número, interior…"
                    />
                    <x-input-error :messages="$errors->get('facturacion_calle_numero')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="colonia_cp" value="Colonia y C.P." />
                    <x-text-input
                        id="colonia_cp"
                        name="colonia_cp"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('colonia_cp', $contact?->colonia_cp ?? $company?->colonia_cp ?? '')"
                        placeholder="—"
                    />
                    <x-input-error :messages="$errors->get('colonia_cp')" class="mt-2" />
                </div>
                <div>
                    <span class="text-sm font-medium text-white/70 uppercase block mt-2 md:mt-0">Ciudad, Estado</span>
                    <p class="text-white mt-0.5">
                        @php
                            $ciudad = $contact?->municipio ?? $company?->municipio ?? '';
                            $estado = $contact?->estado ?? $company?->estado ?? '';
                            $ciudadEstado = trim(($ciudad ? $ciudad : '') . ($estado ? ', ' . $estado : ''), ' ,');
                        @endphp
                        {{ $ciudadEstado ?: '—' }}
                    </p>
                </div>
                <div>
                    <x-input-label for="facturacion_rfc" value="RFC" />
                    <x-text-input
                        id="facturacion_rfc"
                        name="facturacion_rfc"
                        type="text"
                        class="mt-1 block w-full text-gray-900"
                        :value="$rfcFactCreate"
                        placeholder="—"
                    />
                    <x-input-error :messages="$errors->get('facturacion_rfc')" class="mt-2" />
                </div>
                <div>
                    <span class="text-sm font-medium text-white/70 uppercase block">TEL</span>
                    <p class="text-white mt-0.5">{{ $contact?->celular ?? $contact?->telefono ?? '—' }}</p>
                </div>
                <div>
                    <x-input-label for="regimen_fiscal" value="Régimen en que tributa" />
                    <x-text-input
                        id="regimen_fiscal"
                        name="regimen_fiscal"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('regimen_fiscal', $contact?->regimen_fiscal ?? $company?->regimen_fiscal ?? '')"
                        placeholder="—"
                    />
                    <x-input-error :messages="$errors->get('regimen_fiscal')" class="mt-2" />
                </div>
                <div>
                    <span class="text-sm font-medium text-white/70 uppercase block">Método de pago</span>
                    <p class="text-white mt-0.5 font-semibold" id="metodo-pago-preview">—</p>
                </div>
                <div>
                    <x-input-label for="forma_pago" value="Forma de pago" />
                    <x-text-input id="forma_pago" name="forma_pago" type="text" class="mt-1 block w-full text-gray-900" :value="old('forma_pago')" placeholder="" />
                    <x-input-error :messages="$errors->get('forma_pago')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="uso_cfdi" value="Uso de CFDI" />
                    <x-text-input id="uso_cfdi" name="uso_cfdi" type="text" class="mt-1 block w-full" :value="old('uso_cfdi')" placeholder="—" />
                    <x-input-error :messages="$errors->get('uso_cfdi')" class="mt-2" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="condiciones_pago" value="Condiciones de pago" />
                    <textarea id="condiciones_pago" name="condiciones_pago" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900">{{ old('condiciones_pago') }}</textarea>
                    <x-input-error :messages="$errors->get('condiciones_pago')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="modalidad" value="Modalidad" />
                    <x-text-input id="modalidad" name="modalidad" type="text" class="mt-1 block w-full text-gray-900" :value="old('modalidad')" placeholder="—" />
                    <x-input-error :messages="$errors->get('modalidad')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="sede" value="Sede" />
                    <x-text-input id="sede" name="sede" type="text" class="mt-1 block w-full text-gray-900" :value="old('sede')" placeholder="—" />
                    <x-input-error :messages="$errors->get('sede')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="fecha_evento" value="Fecha" />
                    <x-text-input id="fecha_evento" name="fecha_evento" type="date" class="mt-1 block w-full text-gray-900" :value="old('fecha_evento')" />
                    <x-input-error :messages="$errors->get('fecha_evento')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="horario_evento" value="Horario" />
                    <x-text-input id="horario_evento" name="horario_evento" type="text" class="mt-1 block w-full text-gray-900" :value="old('horario_evento')" placeholder="Ej. 9:00–14:00" />
                    <x-input-error :messages="$errors->get('horario_evento')" class="mt-2" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="factura_referencia" value="Factura" />
                    <x-text-input id="factura_referencia" name="factura_referencia" type="text" class="mt-1 block w-full text-gray-900" :value="old('factura_referencia')" placeholder="Referencia o folio" />
                    <x-input-error :messages="$errors->get('factura_referencia')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="orden_compra" value="Orden de compra" />
                    <x-text-input id="orden_compra" name="orden_compra" type="text" class="mt-1 block w-full text-gray-900" :value="old('orden_compra')" placeholder="" />
                    <x-input-error :messages="$errors->get('orden_compra')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email_facturacion" value="Correo" />
                    <x-text-input
                        id="email_facturacion"
                        name="email_facturacion"
                        type="text"
                        class="mt-1 block w-full text-gray-900"
                        :value="$emailFactCreate"
                        placeholder="correo@ejemplo.com; varios separados por ;"
                    />
                    <x-input-error :messages="$errors->get('email_facturacion')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-6 gap-2 sm:gap-3 flex-wrap">
                <a href="{{ route('user.sales.index') }}" class="btn-danger-app">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancelar
                </a>
                <button type="submit" name="post_action" value="ficha" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-[#FFE600]/70 text-[#FFE600] text-sm font-semibold hover:bg-[#FFE600]/10 transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Generar ficha de inscripción
                </button>
                <button type="submit" name="post_action" value="guardar" class="btn-amber-app">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar venta
                </button>
            </div>
        </div>

        <div id="acciones-step-1" class="flex items-center justify-end mt-6 gap-2 sm:gap-3 flex-wrap">
            <a href="{{ route('user.sales.index') }}" class="btn-danger-app">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancelar
            </a>
            <button type="submit" name="post_action" value="ficha" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-[#FFE600]/70 text-[#FFE600] text-sm font-semibold hover:bg-[#FFE600]/10 transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Generar ficha de inscripción
            </button>
            <button type="submit" name="post_action" value="guardar" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/40 bg-white/10 text-white text-sm font-semibold hover:bg-white/15 transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Registrar nueva venta
            </button>
            <button type="button" id="btn-continuar-facturacion" class="btn-amber-app">
                Continuar a datos de facturación
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    var btnContinuar = document.getElementById('btn-continuar-facturacion');
    var stepFacturacion = document.getElementById('step-datos-facturacion');
    var accionesStep1 = document.getElementById('acciones-step-1');
    var form = document.getElementById('form-registrar-venta');
    var tipoPagoInput = document.getElementById('tipo_pago');
    var metodoPagoPreview = document.getElementById('metodo-pago-preview');

    if (!btnContinuar || !stepFacturacion || !accionesStep1 || !form) return;

    btnContinuar.addEventListener('click', function () {
        if (!form.reportValidity()) {
            return;
        }

        if (tipoPagoInput && metodoPagoPreview) {
            var t = (tipoPagoInput.value || '').trim();
            metodoPagoPreview.textContent = t ? t : '—';
        }

        stepFacturacion.classList.remove('hidden');
        accionesStep1.classList.add('hidden');
        stepFacturacion.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();

(function () {
    var form = document.getElementById('form-registrar-venta');
    if (!form) return;

    form.querySelectorAll('input, select, textarea').forEach(function (field) {
        field.addEventListener('invalid', function (e) {
            this.setCustomValidity('');

            if (this.validity.valueMissing) {
                this.setCustomValidity('Este campo es obligatorio.');
            } else if (this.validity.typeMismatch && this.type === 'email') {
                this.setCustomValidity('Ingrese un correo electrónico válido.');
            } else if (this.validity.typeMismatch && this.type === 'url') {
                this.setCustomValidity('Ingrese una URL válida.');
            } else if (this.validity.patternMismatch) {
                this.setCustomValidity('El formato del valor no es válido.');
            } else if (this.validity.rangeUnderflow || this.validity.rangeOverflow) {
                this.setCustomValidity('El valor está fuera del rango permitido.');
            } else if (this.validity.stepMismatch) {
                this.setCustomValidity('El valor no es válido para este campo.');
            }
        }, true);

        field.addEventListener('input', function () {
            this.setCustomValidity('');
        });
    });
})();

(function () {
    var companyInput = document.getElementById('company_name');
    var companyHidden = document.getElementById('company_id');
    var suggestionsBox = document.getElementById('company_suggestions');

    if (!companyInput || !companyHidden || !suggestionsBox) return;

    var companies = @json($companies->map(fn($c) => [
        'id' => $c->id,
        'nombre' => $c->nombre_comercial,
    ]));

    function filtrarEmpresas(texto) {
        texto = texto.toLowerCase();
        if (!texto) {
            return companies.slice(0, 20);
        }
        return companies.filter(function (c) {
            return c.nombre.toLowerCase().indexOf(texto) !== -1;
        }).slice(0, 20);
    }

    function renderSuggestions(lista) {
        suggestionsBox.innerHTML = '';
        if (!lista.length) {
            suggestionsBox.classList.add('hidden');
            return;
        }

        lista.forEach(function (c, index) {
            var item = document.createElement('button');
            item.type = 'button';
            item.className = 'w-full text-left px-3 py-2 bg-white hover:bg-yellow-300 focus:bg-yellow-300 focus:outline-none text-gray-900 border-b border-gray-200';
            if (index === lista.length - 1) {
                item.className += ' last:border-b-0';
            }
            item.textContent = c.nombre;
            item.dataset.id = c.id;
            item.addEventListener('mousedown', function (e) {
                e.preventDefault();
                companyInput.value = c.nombre;
                companyHidden.value = c.id;
                suggestionsBox.classList.add('hidden');
                window.location.href = '{{ route('user.sales.create') }}?company_id=' + encodeURIComponent(c.id);
            });
            suggestionsBox.appendChild(item);
        });

        suggestionsBox.classList.remove('hidden');
    }

    function onInput() {
        var valor = companyInput.value.trim();
        var lista = filtrarEmpresas(valor);
        renderSuggestions(lista);
    }

    companyInput.addEventListener('input', onInput);

    companyInput.addEventListener('focus', function () {
        onInput();
    });

    companyInput.addEventListener('blur', function () {
        setTimeout(function () {
            suggestionsBox.classList.add('hidden');
        }, 150);
    });

    suggestionsBox.classList.add('hidden');
})();
</script>
