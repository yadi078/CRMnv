<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg></x-page-header-avatar>
            <div>
                <h2 class="page-header-card__title">Editar Venta</h2>
                @if($sale->nombre_curso_ficha !== '—')
                    <p class="page-header-card__subtitle">{{ $sale->nombre_curso_ficha }}</p>
                @endif
            </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-6 text-base md:text-lg">
                <form method="POST" action="{{ route('user.sales.update', $sale) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Tipo de curso (2/3) + Fecha (1/3); nombre_servicio se sincroniza en el servidor --}}
                        <div class="md:col-span-2">
                            <label for="tipo_curso" class="mb-1 flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                                <span class="text-base md:text-lg font-semibold text-white">Nombre del curso o servicio *</span>
                                <span class="text-xs md:text-sm font-normal text-white/60">Se muestra en la ficha de inscripción.</span>
                            </label>
                            <p class="text-sm text-white/65 mb-1 mt-0.5">Si venía «Venta desde contacto…», sustitúyalo por el curso real.</p>
                            <x-text-input id="tipo_curso" name="tipo_curso" type="text" class="mt-1 block w-full" :value="old('tipo_curso', filled(trim((string) ($sale->tipo_curso ?? ''))) ? $sale->tipo_curso : $sale->nombre_servicio)" placeholder="Ej. Capacitación en ventas, diplomado ejecutivo" required />
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
                                :value="old('fecha_venta', $sale->fecha_venta?->format('Y-m-d'))"
                                min="{{ now()->toDateString() }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('fecha_venta')" class="mt-2" />
                        </div>

                        {{-- Empresa (2/3) + Contacto (1/3) --}}
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

                        <div class="md:col-span-2">
                            <label for="tipo_pago_venta" class="block text-base md:text-lg font-semibold text-white">Método de pago</label>
                            <p class="text-xs text-white/60 mb-2">Escriba el método acordado; puede detallar varios medios o condiciones.</p>
                            <textarea
                                id="tipo_pago_venta"
                                name="tipo_pago"
                                rows="3"
                                maxlength="500"
                                class="mt-1 block w-full rounded-md border-gray-300 text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                placeholder="Ej. Transferencia, OXXO, meses sin intereses…"
                            >{{ old('tipo_pago', $sale->tipo_pago) }}</textarea>
                            <x-input-error :messages="$errors->get('tipo_pago')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="participantes" value="Participantes" class="text-base md:text-lg font-semibold text-white" />
                            <x-text-input id="participantes" name="participantes" type="number" min="1" class="mt-1 block w-full" :value="old('participantes', $sale->participantes)" />
                            <x-input-error :messages="$errors->get('participantes')" class="mt-2" />
                        </div>

                        <div class="md:col-span-3">
                            @php
                                $participantesTextoEdit = old('participantes_texto', $sale->participantes_texto);
                                if ($participantesTextoEdit === null && $sale->saleParticipants->isNotEmpty()) {
                                    $participantesTextoEdit = $sale->saleParticipants
                                        ->map(fn ($p) => trim($p->nombre . ($p->email ? ' — ' . $p->email : '')))
                                        ->filter()
                                        ->join("\n");
                                }
                            @endphp
                            <x-input-label for="participantes_texto" value="Nombre y correo de participantes" class="text-base md:text-lg font-semibold text-white" />
                            <p class="mt-1 mb-2 text-xs md:text-sm text-white/60 leading-relaxed">
                                Una línea por participante. Puede separar nombre y correo con coma o guión. Por ejemplo:<br>
                                <span class="text-white/75">Yadira Suarez, yadi@gmail.com</span><br>
                                <span class="text-white/75">Columba Silva - columba@gmail.com</span>
                            </p>
                            <textarea id="participantes_texto" name="participantes_texto" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900" placeholder="Una línea por participante (nombre y correo).">{{ $participantesTextoEdit }}</textarea>
                        </div>

                        <div class="md:col-span-3">
                            <x-input-label for="notas" value="Notas" class="text-base md:text-lg font-semibold text-white" />
                            <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">{{ old('notas', $sale->notas) }}</textarea>
                            <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                        </div>

                        {{-- DATOS DE FACTURACIÓN (los que aparecen en la ficha final) --}}
                        @php
                            $calleFactEdit = old('facturacion_calle_numero', $sale->facturacion_calle_numero ?? $sale->contact?->calle_numero ?? $sale->company?->datos_fiscales ?? '');
                            $rfcFactEdit = old('facturacion_rfc', $sale->facturacion_rfc ?? $sale->contact?->rfc ?? $sale->company?->rfc ?? '');
                            $emailFactEdit = old('email_facturacion', $sale->email_facturacion ?? $sale->contact?->email ?? '');
                        @endphp
                        <div class="md:col-span-3 mt-2 pt-6 border-t border-white/20">
                            <h3 class="text-lg font-semibold text-[#FFE600] mb-4">Datos de facturación</h3>
                            <p class="text-sm text-white/80 mb-4">Estos datos se mostrarán en la ficha final. Los que vienen de la empresa y contacto seleccionados se pueden completar o ajustar aquí.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-white/5 border border-white/10">
                                <div class="md:col-span-2">
                                    <span class="text-xs font-medium text-white/70 uppercase">Razón social</span>
                                    <p class="text-white font-medium mt-0.5">{{ $sale->contact?->razon_social ?? $sale->company->nombre_comercial ?? '—' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="facturacion_calle_numero" value="Domicilio fiscal (calle y número)" />
                                    <x-text-input id="facturacion_calle_numero" name="facturacion_calle_numero" type="text" class="mt-1 block w-full text-gray-900" :value="$calleFactEdit" placeholder="Calle, número, interior…" />
                                    <x-input-error :messages="$errors->get('facturacion_calle_numero')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="colonia_cp" value="Colonia y C.P." />
                                    <x-text-input id="colonia_cp" name="colonia_cp" type="text" class="mt-1 block w-full" :value="old('colonia_cp', $sale->colonia_cp)" placeholder="—" />
                                    <x-input-error :messages="$errors->get('colonia_cp')" class="mt-2" />
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-white/70 uppercase block mt-2 md:mt-0">Ciudad, Estado</span>
                                    <p class="text-white mt-0.5">
                                        @php
                                            $ciudadEd = $sale->contact?->municipio ?? $sale->company->municipio ?? '';
                                            $estadoEd = $sale->contact?->estado ?? $sale->company->estado ?? '';
                                            $ciudadEstadoEd = trim(($ciudadEd ? $ciudadEd : '') . ($estadoEd ? ', ' . $estadoEd : ''), ' ,');
                                        @endphp
                                        {{ $ciudadEstadoEd ?: '—' }}
                                    </p>
                                </div>
                                <div>
                                    <x-input-label for="facturacion_rfc" value="RFC" />
                                    <x-text-input id="facturacion_rfc" name="facturacion_rfc" type="text" class="mt-1 block w-full text-gray-900" :value="$rfcFactEdit" placeholder="—" />
                                    <x-input-error :messages="$errors->get('facturacion_rfc')" class="mt-2" />
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
                                    <p class="text-white mt-0.5 whitespace-pre-wrap">{{ $sale->tipo_pago_label ?? '—' }}</p>
                                </div>
                                <div>
                                    <x-input-label for="forma_pago" value="Forma de pago" />
                                    <x-text-input id="forma_pago" name="forma_pago" type="text" class="mt-1 block w-full text-gray-900" :value="old('forma_pago', $sale->forma_pago)" placeholder="" />
                                    <x-input-error :messages="$errors->get('forma_pago')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="uso_cfdi" value="Uso de CFDI" />
                                    <x-text-input id="uso_cfdi" name="uso_cfdi" type="text" class="mt-1 block w-full" :value="old('uso_cfdi', $sale->uso_cfdi)" placeholder="—" />
                                    <x-input-error :messages="$errors->get('uso_cfdi')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="condiciones_pago" value="Condiciones de pago" />
                                    <textarea id="condiciones_pago" name="condiciones_pago" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-gray-900">{{ old('condiciones_pago', $sale->condiciones_pago) }}</textarea>
                                    <x-input-error :messages="$errors->get('condiciones_pago')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="modalidad" value="Modalidad" />
                                    <x-text-input id="modalidad" name="modalidad" type="text" class="mt-1 block w-full text-gray-900" :value="old('modalidad', $sale->modalidad)" placeholder="—" />
                                    <x-input-error :messages="$errors->get('modalidad')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="sede" value="Sede" />
                                    <x-text-input id="sede" name="sede" type="text" class="mt-1 block w-full text-gray-900" :value="old('sede', $sale->sede)" placeholder="—" />
                                    <x-input-error :messages="$errors->get('sede')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="fecha_evento" value="Fecha" />
                                    <x-text-input id="fecha_evento" name="fecha_evento" type="date" class="mt-1 block w-full text-gray-900" :value="old('fecha_evento', $sale->fecha_evento?->format('Y-m-d'))" />
                                    <x-input-error :messages="$errors->get('fecha_evento')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="horario_evento" value="Horario" />
                                    <x-text-input id="horario_evento" name="horario_evento" type="text" class="mt-1 block w-full text-gray-900" :value="old('horario_evento', $sale->horario_evento)" placeholder="Ej. 9:00–14:00" />
                                    <x-input-error :messages="$errors->get('horario_evento')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="factura_referencia" value="Factura" />
                                    <x-text-input id="factura_referencia" name="factura_referencia" type="text" class="mt-1 block w-full text-gray-900" :value="old('factura_referencia', $sale->factura_referencia)" placeholder="Referencia o folio" />
                                    <x-input-error :messages="$errors->get('factura_referencia')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="orden_compra" value="Orden de compra" />
                                    <x-text-input id="orden_compra" name="orden_compra" type="text" class="mt-1 block w-full text-gray-900" :value="old('orden_compra', $sale->orden_compra)" placeholder="" />
                                    <x-input-error :messages="$errors->get('orden_compra')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="email_facturacion" value="Correo" />
                                    <x-text-input id="email_facturacion" name="email_facturacion" type="text" class="mt-1 block w-full text-gray-900" :value="$emailFactEdit" placeholder="correo@ejemplo.com; varios separados por ;" />
                                    <x-input-error :messages="$errors->get('email_facturacion')" class="mt-2" />
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

</x-app-user-layout>
