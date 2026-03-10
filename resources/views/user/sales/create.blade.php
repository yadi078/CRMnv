<x-app-user-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Registrar Venta</h2>
            <p class="page-header-card__subtitle">Agregar curso o servicio vendido al historial</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-6">
                <form method="POST" action="{{ route('user.sales.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="company_id" value="Empresa *" />
                            <select id="company_id" name="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900" required onchange="if(this.value) window.location.href='{{ route('user.sales.create') }}?company_id='+this.value">
                                <option value="">Seleccione una empresa</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $companyId ?? null) == $company->id ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                        </div>

                        @if($contacts->isNotEmpty())
                        <div class="md:col-span-2">
                            <x-input-label for="contact_id" value="Contacto que compró (opcional)" />
                            <select id="contact_id" name="contact_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900">
                                <option value="">Ninguno / No especificado</option>
                                @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}" {{ old('contact_id') == $contact->id ? 'selected' : '' }}>{{ $contact->nombre_completo }}{{ $contact->puesto_de_trabajo ? ' — ' . $contact->puesto_de_trabajo : '' }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('contact_id')" class="mt-2" />
                        </div>
                        @endif

                        <div class="md:col-span-2">
                            <x-input-label for="nombre_servicio" value="Nombre del curso o servicio *" />
                            <x-text-input id="nombre_servicio" name="nombre_servicio" type="text" class="mt-1 block w-full" :value="old('nombre_servicio')" placeholder="Ej: Capacitación en Ventas, Curso de Liderazgo" required />
                            <x-input-error :messages="$errors->get('nombre_servicio')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="fecha_venta" value="Fecha de venta *" />
                            <x-text-input
                                id="fecha_venta"
                                name="fecha_venta"
                                type="date"
                                class="mt-1 block w-full text-gray-900"
                                :value="old('fecha_venta', date('Y-m-d'))"
                                required
                            />
                            <x-input-error :messages="$errors->get('fecha_venta')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="monto" value="Monto ($) + IVA" />
                            <x-text-input id="monto" name="monto" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('monto')" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('monto')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tipo_pago" value="Tipo de pago" />
                            <select id="tipo_pago" name="tipo_pago" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                <option value="">Seleccione</option>
                                <option value="efectivo" {{ old('tipo_pago') === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="transferencia" {{ old('tipo_pago') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="tarjeta_credito" {{ old('tipo_pago') === 'tarjeta_credito' ? 'selected' : '' }}>Tarjeta de crédito</option>
                                <option value="tarjeta_debito" {{ old('tipo_pago') === 'tarjeta_debito' ? 'selected' : '' }}>Tarjeta de débito</option>
                                <option value="cheque" {{ old('tipo_pago') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="deposito" {{ old('tipo_pago') === 'deposito' ? 'selected' : '' }}>Depósito</option>
                                <option value="otro" {{ old('tipo_pago') === 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            <x-input-error :messages="$errors->get('tipo_pago')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="participantes" value="Participantes" />
                            <x-text-input id="participantes" name="participantes" type="number" min="1" class="mt-1 block w-full" :value="old('participantes')" />
                            <x-input-error :messages="$errors->get('participantes')" class="mt-2" />
                        </div>

                        {{-- Nombres y correos de participantes (visible cuando hay más de 1) --}}
                        <div id="participantes-datos-wrap" class="md:col-span-2 hidden">
                            <h3 class="text-base font-semibold text-[#FFE600] mb-3">Datos de los participantes</h3>
                            <p class="text-sm text-white/80 mb-3">Indique nombre completo y correo de cada participante.</p>
                            <div id="participantes-datos-list" class="space-y-4 p-4 rounded-xl bg-white/5 border border-white/10"></div>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="notas" value="Notas" />
                            <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
                            <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                        </div>

                        {{-- DATOS DE FACTURACIÓN (los que aparecen en la ficha final) --}}
                        <div class="md:col-span-2 mt-2 pt-6 border-t border-white/20">
                            <h3 class="text-lg font-semibold text-[#FFE600] mb-4">Datos de facturación</h3>
                            <p class="text-sm text-white/80 mb-4">Estos datos se mostrarán en la ficha final. Seleccione empresa y contacto para ver la vista previa.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-white/5 border border-white/10">
                                <div class="md:col-span-2">
                                    <span class="text-xs font-medium text-white/70 uppercase">Razón social</span>
                                    <p class="text-white font-medium mt-0.5">{{ $company->nombre_comercial ?? '—' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <span class="text-xs font-medium text-white/70 uppercase">Calle y número</span>
                                    <p class="text-white mt-0.5">{{ $company ? Str::limit($company->datos_fiscales ?? '—', 80) : '—' }}</p>
                                </div>
                                <div>
                                    <x-input-label for="colonia_cp" value="Colonia y C.P." />
                                    <x-text-input id="colonia_cp" name="colonia_cp" type="text" class="mt-1 block w-full" :value="old('colonia_cp')" placeholder="—" />
                                    <x-input-error :messages="$errors->get('colonia_cp')" class="mt-2" />
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-white/70 uppercase block mt-2 md:mt-0">Ciudad, Estado</span>
                                    <p class="text-white mt-0.5">{{ $company ? (trim(($company->municipio ?? '') . ', ' . ($company->estado ?? ''), ' ,') ?: '—') : '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-white/70 uppercase block">RFC</span>
                                    <p class="text-white mt-0.5">{{ $company?->rfc ?? '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-white/70 uppercase block">TEL</span>
                                    <p class="text-white mt-0.5">{{ $contact?->celular ?? $contact?->telefono ?? '—' }}</p>
                                </div>
                                <div>
                                    <x-input-label for="regimen_fiscal" value="Régimen en que tributa" />
                                    <x-text-input id="regimen_fiscal" name="regimen_fiscal" type="text" class="mt-1 block w-full" :value="old('regimen_fiscal')" placeholder="—" />
                                    <x-input-error :messages="$errors->get('regimen_fiscal')" class="mt-2" />
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-white/70 uppercase block">Método de pago</span>
                                    <p class="text-white mt-0.5">Se muestra según el tipo de pago elegido arriba</p>
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
                                    <span class="text-xs font-medium text-white/70 uppercase block">Correo</span>
                                    <p class="text-white mt-0.5">{{ $contact?->email ?? '—' }}</p>
                                </div>
                            </div>
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
                            Guardar
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
                    '<div><label class="block text-xs text-white/70 mb-1">Nombre completo</label><input type="text" name="participantes_nombres[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900" value="' + (data.nombre || '').replace(/"/g, '&quot;') + '" placeholder="Nombre completo"></div>' +
                    '<div><label class="block text-xs text-white/70 mb-1">Correo</label><input type="email" name="participantes_emails[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900" value="' + (data.email || '').replace(/"/g, '&quot;') + '" placeholder="correo@ejemplo.com"></div>' +
                    '</div>';
                list.appendChild(div);
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
