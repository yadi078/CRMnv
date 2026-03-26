<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg></x-page-header-avatar>
            <div>
                <h2 class="page-header-card__title">Editar Venta</h2>
                @if(!Str::startsWith($sale->nombre_servicio ?? '', 'Venta desde contacto:'))
                    <p class="page-header-card__subtitle">{{ $sale->nombre_servicio }}</p>
                @endif
            </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-6 text-base md:text-lg">
                <form method="POST" action="{{ route('user.sales.update', $sale) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Fila 1: Nombre del curso (2/3) + Fecha (1/3) --}}
                        <div class="md:col-span-2">
                            <x-input-label for="nombre_servicio" value="Nombre del curso o servicio *" />
                            <x-text-input id="nombre_servicio" name="nombre_servicio" type="text" class="mt-1 block w-full" :value="old('nombre_servicio', $sale->nombre_servicio)" placeholder="Ej: Capacitación en Ventas, Curso de Liderazgo" required />
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
                                :value="old('fecha_venta', $sale->fecha_venta?->format('Y-m-d'))"
                                min="{{ now()->toDateString() }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('fecha_venta')" class="mt-2" />
                        </div>

                        {{-- Fila 2: Empresa (2/3) + Contacto (1/3) --}}
                        <div class="md:col-span-2">
                            <x-input-label for="company_id" value="Empresa *" class="text-base md:text-lg font-semibold text-white" />
                            <select id="company_id" name="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900" required>
                                <option value="">Seleccione una empresa</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id', $sale->company_id) == $company->id ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contact_id" value="Contacto que compró (opcional)" class="text-base md:text-lg font-semibold text-white" />
                            <select id="contact_id" name="contact_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900">
                                <option value="">Ninguno / No especificado</option>
                                @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}" {{ old('contact_id', $sale->contact_id) == $contact->id ? 'selected' : '' }}>{{ $contact->nombre_completo }}{{ $contact->puesto_de_trabajo ? ' — ' . $contact->puesto_de_trabajo : '' }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('contact_id')" class="mt-2" />
                        </div>

                        {{-- Fila 3: Monto, Tipo de pago, Participantes --}}
                        <div>
                            <x-input-label for="fecha_venta" value="Fecha de venta *" />
                            <x-text-input
                                id="fecha_venta"
                                name="fecha_venta"
                                type="date"
                                class="mt-1 block w-full text-gray-900"
                                :value="old('fecha_venta', $sale->fecha_venta?->format('Y-m-d'))"
                                required
                            />
                            <x-input-error :messages="$errors->get('fecha_venta')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="monto" value="Monto ($)" />
                            <x-text-input id="monto" name="monto" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('monto', $sale->monto)" />
                            <x-input-error :messages="$errors->get('monto')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3 md:col-span-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="incluye_iva" value="0">
                                <input type="checkbox" name="incluye_iva" value="1" class="rounded border-gray-300 text-amber-500 focus:ring-amber-500"
                                    {{ old('incluye_iva', $sale->incluye_iva ?? true) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-white/90">Incluir IVA en esta venta</span>
                            </label>
                            <span class="text-xs text-white/60">Desmarque si el monto no lleva IVA (ej. factura exenta).</span>
                        </div>

                        <div>
                            <x-input-label for="tipo_pago" value="Tipo de pago" class="text-base md:text-lg font-semibold text-white" />
                            <select id="tipo_pago" name="tipo_pago" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                <option value="">Seleccione</option>
                                <option value="efectivo" {{ old('tipo_pago', $sale->tipo_pago) === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="transferencia" {{ old('tipo_pago', $sale->tipo_pago) === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="tarjeta_credito" {{ old('tipo_pago', $sale->tipo_pago) === 'tarjeta_credito' ? 'selected' : '' }}>Tarjeta de crédito</option>
                                <option value="tarjeta_debito" {{ old('tipo_pago', $sale->tipo_pago) === 'tarjeta_debito' ? 'selected' : '' }}>Tarjeta de débito</option>
                                <option value="cheque" {{ old('tipo_pago', $sale->tipo_pago) === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="deposito" {{ old('tipo_pago', $sale->tipo_pago) === 'deposito' ? 'selected' : '' }}>Depósito</option>
                                <option value="otro" {{ old('tipo_pago', $sale->tipo_pago) === 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            <x-input-error :messages="$errors->get('tipo_pago')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="participantes" value="Participantes" class="text-base md:text-lg font-semibold text-white" />
                            <x-text-input id="participantes" name="participantes" type="number" min="1" class="mt-1 block w-full" :value="old('participantes', $sale->participantes)" />
                            <x-input-error :messages="$errors->get('participantes')" class="mt-2" />
                        </div>

                        {{-- Nombres y correos de participantes (visible cuando hay más de 1) --}}
                        <div id="participantes-datos-wrap" class="md:col-span-2 hidden flex flex-col items-center">
                            <div class="w-full md:w-4/5 lg:w-3/4">
                                <h3 class="text-lg md:text-xl font-semibold text-[#FFE600] mb-3 text-center">Datos de los participantes</h3>
                                <p class="text-base text-white/80 mb-3 text-center">Indique nombre completo y correo de cada participante.</p>
                                <div id="participantes-datos-list" class="space-y-4 p-4 rounded-xl bg-white/5 border border-white/10"></div>
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <x-input-label for="notas" value="Notas" class="text-base md:text-lg font-semibold text-white" />
                            <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">{{ old('notas', $sale->notas) }}</textarea>
                            <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                        </div>

                        {{-- DATOS DE FACTURACIÓN (los que aparecen en la ficha final) --}}
                        {{-- DATOS DE FACTURACIÓN (los que aparecen en la ficha final) --}}
                        <div class="md:col-span-3 mt-2 pt-6 border-t border-white/20">
                            <h3 class="text-lg font-semibold text-[#FFE600] mb-4">Datos de facturación</h3>
                            <p class="text-sm text-white/80 mb-4">Estos datos se mostrarán en la ficha final. Los que vienen de la empresa y contacto seleccionados se actualizan automáticamente.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-white/5 border border-white/10">
                                <div class="md:col-span-2">
                                    <span class="text-xs font-medium text-white/70 uppercase">Razón social</span>
                                    <p class="text-white font-medium mt-0.5">{{ $sale->company->nombre_comercial ?? '—' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <span class="text-xs font-medium text-white/70 uppercase">Calle y número</span>
                                    <p class="text-white mt-0.5">{{ Str::limit($sale->company->datos_fiscales ?? '—', 80) }}</p>
                                </div>
                                <div>
                                    <x-input-label for="colonia_cp" value="Colonia y C.P." />
                                    <x-text-input id="colonia_cp" name="colonia_cp" type="text" class="mt-1 block w-full" :value="old('colonia_cp', $sale->colonia_cp)" placeholder="—" />
                                    <x-input-error :messages="$errors->get('colonia_cp')" class="mt-2" />
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-white/70 uppercase block mt-2 md:mt-0">Ciudad, Estado</span>
                                    <p class="text-white mt-0.5">{{ trim(($sale->company->municipio ?? '') . ', ' . ($sale->company->estado ?? ''), ' ,') ?: '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-white/70 uppercase block">RFC</span>
                                    <p class="text-white mt-0.5">{{ $sale->company->rfc ?? '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-white/70 uppercase block">TEL</span>
                                    <p class="text-white mt-0.5">{{ $sale->contact?->celular ?? $sale->contact?->telefono ?? '—' }}</p>
                                </div>
                                <div>
                                    <x-input-label for="regimen_fiscal" value="Régimen en que tributa" />
                                    <x-text-input id="regimen_fiscal" name="regimen_fiscal" type="text" class="mt-1 block w-full" :value="old('regimen_fiscal', $sale->regimen_fiscal)" placeholder="—" />
                                    <x-input-error :messages="$errors->get('regimen_fiscal')" class="mt-2" />
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-white/70 uppercase block">Método de pago</span>
                                    <p class="text-white mt-0.5">{{ $sale->tipo_pago_label ?? '—' }}</p>
                                </div>
                                <div>
                                    <x-input-label for="forma_pago" value="Forma de pago" />
                                    <select id="forma_pago" name="forma_pago" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900">
                                        <option value="">—</option>
                                        @foreach(\App\Models\Sale::FORMA_DE_PAGO_LABELS as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ old('forma_pago', $sale->forma_pago) == $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('forma_pago')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="uso_cfdi" value="Uso de CFDI" />
                                    <x-text-input id="uso_cfdi" name="uso_cfdi" type="text" class="mt-1 block w-full" :value="old('uso_cfdi', $sale->uso_cfdi)" placeholder="—" />
                                    <x-input-error :messages="$errors->get('uso_cfdi')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="orden_compra" value="Orden de compra" />
                                    <select id="orden_compra" name="orden_compra" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900">
                                        <option value="">—</option>
                                        @foreach(\App\Models\Sale::ORDEN_COMPRA_LABELS as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ old('orden_compra', $sale->orden_compra) == $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('orden_compra')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <span class="text-xs font-medium text-white/70 uppercase block">Correo</span>
                                    <p class="text-white mt-0.5">{{ $sale->contact?->email ?? '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ route('user.sales.index') }}" class="btn-icon-text text-gray-600 hover:text-gray-800 px-4 py-2 rounded-xl border border-gray-300 hover:bg-gray-50">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-amber-app">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @php
        $participantesIniciales = [];
        if (old('participantes_nombres')) {
            foreach (old('participantes_nombres') as $i => $n) {
                $participantesIniciales[] = ['nombre' => $n, 'email' => old('participantes_emails')[$i] ?? ''];
            }
        } else {
            $participantesIniciales = $sale->saleParticipants->map(fn($p) => ['nombre' => $p->nombre, 'email' => $p->email ?? ''])->values()->all();
        }
    @endphp
    <script>
    (function() {
        var participantesInput = document.getElementById('participantes');
        var wrap = document.getElementById('participantes-datos-wrap');
        var list = document.getElementById('participantes-datos-list');
        var initialData = @json($participantesIniciales);

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
    </script>
</x-app-user-layout>
