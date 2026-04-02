<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Registrar Venta</h2>
            <p class="page-header-card__subtitle">Agregar curso o servicio vendido al historial</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-6 text-base md:text-lg">
                <form method="POST" action="{{ route('user.sales.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Fila 1: Nombre del curso (2/3) + Fecha (1/3) --}}
                        <div class="md:col-span-2">
                            <x-input-label for="nombre_servicio" value="Nombre del curso o servicio *" />
                            <x-text-input id="nombre_servicio" name="nombre_servicio" type="text" class="mt-1 block w-full" :value="old('nombre_servicio')" placeholder="Ej: Capacitación en Ventas, Curso de Liderazgo" required />
                            <x-input-error :messages="$errors->get('nombre_servicio')" class="mt-2" />
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

                        <div class="md:col-span-3">
                            <x-input-label for="tipo_curso" value="Tipo de curso" />
                            <p class="text-xs text-white/60 mb-1">Se muestra en la ficha PDF (ej. diplomado, taller, certificación).</p>
                            <x-text-input id="tipo_curso" name="tipo_curso" type="text" class="mt-1 block w-full" :value="old('tipo_curso')" placeholder="Ej. Diplomado ejecutivo" />
                            <x-input-error :messages="$errors->get('tipo_curso')" class="mt-2" />
                        </div>

                        {{-- Fila 2: Empresa (2/3) + Contacto (1/3) --}}
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
                                    {{-- Opciones generadas por JS --}}
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

                        {{-- Fila 3: Monto, método de pago, participantes --}}
                        <div>
                            <x-input-label for="monto" value="Monto ($)" />
                            <x-text-input id="monto" name="monto" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('monto')" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('monto')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3 md:col-span-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="incluye_iva" value="0">
                                <input type="checkbox" name="incluye_iva" value="1" class="rounded border-gray-300 text-amber-500 focus:ring-amber-500"
                                    {{ old('incluye_iva', true) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-white/90">Incluir IVA en esta venta</span>
                            </label>
                            <span class="text-xs text-white/60">Desmarque si el monto no lleva IVA (ej. factura exenta).</span>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="tipo_pago" value="Método de pago" class="text-base md:text-lg font-semibold text-white" />
                            <p class="text-xs text-white/60 mt-1 mb-2">Describa el método acordado (uno o varios).</p>
                            <textarea
                                id="tipo_pago"
                                name="tipo_pago"
                                rows="3"
                                maxlength="500"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900"
                                placeholder="Ej. SPEI, efectivo en instalaciones, tarjeta a meses…"
                            >{{ old('tipo_pago') }}</textarea>
                            <x-input-error :messages="$errors->get('tipo_pago')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="participantes" value="Participantes" class="text-base md:text-lg font-semibold text-white" />
                            <x-text-input id="participantes" name="participantes" type="number" min="1" class="mt-1 block w-full" :value="old('participantes')" />
                            <x-input-error :messages="$errors->get('participantes')" class="mt-2" />
                        </div>

                        {{-- Nombres y correos de participantes (visible cuando hay más de 1) --}}
                        <div id="participantes-datos-wrap" class="md:col-span-3 hidden flex flex-col items-center">
                            <div class="w-full md:w-4/5 lg:w-3/4">
                                <h3 class="text-lg md:text-xl font-semibold text-[#FFE600] mb-3 text-center">Datos de los participantes</h3>
                                <p class="text-base text-white/80 mb-3 text-center">Indique nombre completo y correo de cada participante.</p>
                                <div id="participantes-datos-list" class="space-y-4 p-4 rounded-xl bg-white/5 border border-white/10"></div>
                            </div>
                        </div>

                        {{-- Fila final: Notas --}}
                        <div class="md:col-span-3">
                            <x-input-label for="notas" value="Notas" class="text-base md:text-lg font-semibold text-white" />
                            <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
                            <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                        </div>
                    </div>

                    {{-- PASO 2: Datos de facturación (se muestra después de continuar) --}}
                    <div id="step-datos-facturacion" class="mt-8 pt-6 border-t border-white/20 hidden">
                        <h3 class="text-xl font-semibold text-[#FFE600] mb-4">Datos de facturación</h3>
                        <p class="text-base text-white/80 mb-4">Estos datos se mostrarán en la ficha final. Se llenan con la empresa y el contacto seleccionados, pero puede ajustar la información manualmente.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-white/5 border border-white/10">
                            <div class="md:col-span-2">
                                <span class="text-sm font-medium text-white/70 uppercase">Razón social</span>
                                <p class="text-white font-semibold mt-0.5">
                                    {{ $contact?->razon_social ?? $company?->nombre_comercial ?? '—' }}
                                </p>
                            </div>
                            <div class="md:col-span-2">
                                <span class="text-sm font-medium text-white/70 uppercase">Calle y número</span>
                                <p class="text-white mt-0.5">
                                    @php
                                        $calleNumero = $contact->calle_numero ?? $company->datos_fiscales ?? null;
                                    @endphp
                                    {{ $calleNumero ? Str::limit($calleNumero, 80) : '—' }}
                                </p>
                            </div>
                            <div>
                                <x-input-label for="colonia_cp" value="Colonia y C.P." />
                                <x-text-input
                                    id="colonia_cp"
                                    name="colonia_cp"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('colonia_cp', $contact->colonia_cp ?? $company->colonia_cp ?? '')"
                                    placeholder="—"
                                />
                                <x-input-error :messages="$errors->get('colonia_cp')" class="mt-2" />
                            </div>
                            <div>
                                <span class="text-sm font-medium text-white/70 uppercase block mt-2 md:mt-0">Ciudad, Estado</span>
                                <p class="text-white mt-0.5">
                                    @php
                                        $ciudad = $contact->municipio ?? $company->municipio ?? '';
                                        $estado = $contact->estado ?? $company->estado ?? '';
                                        $ciudadEstado = trim(($ciudad ? $ciudad : '') . ($estado ? ', ' . $estado : ''), ' ,');
                                    @endphp
                                    {{ $ciudadEstado ?: '—' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-white/70 uppercase block">RFC</span>
                                <p class="text-white mt-0.5">
                                    {{ $contact?->rfc ?? $company?->rfc ?? '—' }}
                                </p>
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
                                    :value="old('regimen_fiscal', $contact->regimen_fiscal ?? $company->regimen_fiscal ?? '')"
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
                                <select id="forma_pago" name="forma_pago" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900">
                                    <option value="">—</option>
                                    @foreach(\App\Models\Sale::FORMA_DE_PAGO_LABELS as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('forma_pago') == $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('forma_pago')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="uso_cfdi" value="Uso de CFDI" />
                                <x-text-input id="uso_cfdi" name="uso_cfdi" type="text" class="mt-1 block w-full" :value="old('uso_cfdi')" placeholder="—" />
                                <x-input-error :messages="$errors->get('uso_cfdi')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="orden_compra" value="Orden de compra" />
                                <select id="orden_compra" name="orden_compra" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900">
                                    <option value="">—</option>
                                    @foreach(\App\Models\Sale::ORDEN_COMPRA_LABELS as $valor => $etiqueta)
                                        <option value="{{ $valor }}" {{ old('orden_compra') == $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('orden_compra')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <span class="text-sm font-medium text-white/70 uppercase block">Correo</span>
                                <p class="text-white mt-0.5">{{ $contact?->email ?? '—' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                            <a href="{{ route('user.sales.index') }}" class="btn-danger-app">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit" class="btn-amber-app">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Guardar venta
                            </button>
                        </div>
                    </div>

                    {{-- Acciones del PASO 1 --}}
                    <div id="acciones-step-1" class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ route('user.sales.index') }}" class="btn-danger-app">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancelar
                        </a>
                        <button type="button" id="btn-continuar-facturacion" class="btn-amber-app">
                            Continuar a datos de facturación
                        </button>
                    </div>
                </form>
            </div>
    </div>

    @php
        $participantesInicialesCreate = [];
        if (old('participantes_nombres')) {
            foreach (old('participantes_nombres') as $i => $n) {
                $participantesInicialesCreate[] = ['nombre' => $n, 'email' => old('participantes_emails')[$i] ?? ''];
            }
        }
    @endphp
    <script>
    (function() {
        var participantesInput = document.getElementById('participantes');
        var wrap = document.getElementById('participantes-datos-wrap');
        var list = document.getElementById('participantes-datos-list');
        var initialData = @json($participantesInicialesCreate ?? []);

        function buildRows(n) {
            list.innerHTML = '';
            for (var i = 0; i < n; i++) {
                var data = initialData[i] || { nombre: '', email: '' };
                var div = document.createElement('div');
                div.className = 'grid grid-cols-1 md:grid-cols-2 gap-3';
                div.innerHTML = '<label class="block text-sm font-medium text-white/90">Participante ' + (i + 1) + '</label>' +
                    '<div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-3">' +
                    '<div><label class="block text-xs text-white/70 mb-1">Nombre completo</label><input type="text" name="participantes_nombres[]" maxlength="100" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\\s]+" title="Solo se permiten letras y espacios, máximo 100 caracteres." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900" value="' + (data.nombre || '').replace(/\"/g, '&quot;') + '" placeholder="Nombre completo"></div>' +
                    '<div><label class="block text-xs text-white/70 mb-1">Correo</label><input type="email" name="participantes_emails[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900" value="' + (data.email || '').replace(/"/g, '&quot;') + '" placeholder="correo@ejemplo.com"></div>' +
                    '</div>';
                list.appendChild(div);

                var nombreInput = div.querySelector('input[name="participantes_nombres[]"]');
                if (nombreInput) {
                    nombreInput.addEventListener('invalid', function () {
                        this.setCustomValidity('');
                        if (this.validity.valueMissing) {
                            this.setCustomValidity('Este campo es obligatorio.');
                        } else if (this.validity.patternMismatch) {
                            this.setCustomValidity('Solo se permiten letras y espacios, máximo 100 caracteres.');
                        }
                    });
                    nombreInput.addEventListener('input', function () {
                        this.setCustomValidity('');
                    });
                }
            }
            initialData = [];
        }

        function updateParticipantesSection() {
            var n = parseInt(participantesInput.value, 10) || 0;
            if (n > 1) {
                wrap.classList.remove('hidden');
                buildRows(n);
            } else {
                wrap.classList.add('hidden');
                list.innerHTML = '';
            }
        }

        if (participantesInput) {
            participantesInput.addEventListener('input', updateParticipantesSection);
            participantesInput.addEventListener('change', updateParticipantesSection);
            updateParticipantesSection();
        }
    })();

    // Flujo en dos pasos: primero datos de la venta, luego datos de facturación
    (function () {
        var btnContinuar = document.getElementById('btn-continuar-facturacion');
        var stepFacturacion = document.getElementById('step-datos-facturacion');
        var accionesStep1 = document.getElementById('acciones-step-1');
        var form = document.querySelector('form[action="{{ route('user.sales.store') }}"]');
        var tipoPagoInput = document.getElementById('tipo_pago');
        var metodoPagoPreview = document.getElementById('metodo-pago-preview');

        if (!btnContinuar || !stepFacturacion || !accionesStep1 || !form) return;

        btnContinuar.addEventListener('click', function () {
            // Validar primero los campos del paso 1 con la validación nativa del navegador
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

    // Mensajes de validación del navegador en español para este formulario
    (function () {
        var form = document.querySelector('form[action="{{ route('user.sales.store') }}"]');
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

    // Autocompletado de empresa (nombre + selección) con lista blanca personalizada
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
                // Usamos mousedown para que se dispare antes del blur del input
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

        // Si ya hay empresa seleccionada al cargar, no mostramos nada
        suggestionsBox.classList.add('hidden');
    })();
    </script>
</x-app-user-layout>
