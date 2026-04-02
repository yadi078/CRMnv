<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Editar Empresa</h2>
            <p class="page-header-card__subtitle">{{ $company->nombre_comercial }}</p>
        </div>
    </x-slot>

    <div class="space-y-8" x-data="companyEditSectors()">
        <div class="panel-card-dark p-6">
                <form
                    method="POST"
                    action="{{ route('companies.update', $company) }}"
                    @submit="addSector(); if (sectors.length === 0) { $event.preventDefault(); window.alert('Agregue al menos un sector o giro (escriba y pulse Enter).'); }"
                >
                    @csrf
                    @method('PUT')
                    @if(($crmNavReturn = request('return')) && is_string($crmNavReturn) && \App\Support\CrmNavigation::isSafeReturnUrl($crmNavReturn))
                        <input type="hidden" name="return" value="{{ $crmNavReturn }}">
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nombre_comercial" value="Nombre Comercial *" />
                            <x-text-input id="nombre_comercial" name="nombre_comercial" type="text" class="mt-1 block w-full" :value="old('nombre_comercial', $company->nombre_comercial)" required />
                            <x-input-error :messages="$errors->get('nombre_comercial')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="rfc" value="RFC" />
                            <x-text-input id="rfc" name="rfc" type="text" class="mt-1 block w-full uppercase" :value="old('rfc', $company->rfc)" maxlength="13" />
                            <x-input-error :messages="$errors->get('rfc')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            @php
                                $rawSector = old('sector', $company->sector);
                                if (is_array($rawSector)) {
                                    $initialSectors = collect($rawSector)->map(fn ($s) => trim((string) $s))->filter()->values()->all();
                                } else {
                                    $initialSectors = collect(explode(',', (string) $rawSector))
                                        ->map(fn ($s) => trim($s))
                                        ->filter()
                                        ->values()
                                        ->all();
                                }
                            @endphp
                            <x-input-label for="sector_input" value="Sector/Giro * (puede seleccionar varios)" />
                            <div class="mt-1 rounded-md border border-[#E2E8F0] bg-white p-2 focus-within:border-[#FFE600] focus-within:ring-4 focus-within:ring-[#FFE600]/40">
                                <div class="flex flex-wrap gap-2 mb-2" x-show="sectors.length > 0">
                                    <template x-for="(item, idx) in sectors" :key="idx">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-[#1a3d6b] text-white text-xs px-2 py-1">
                                            <span x-text="item"></span>
                                            <button type="button" @click="removeSector(idx)" class="text-[#FFE600] hover:text-white" aria-label="Quitar sector">x</button>
                                        </span>
                                    </template>
                                </div>
                                <input id="sector_input"
                                       type="text"
                                       x-model.trim="sectorInput"
                                       @keydown.enter.prevent="addSector()"
                                       @blur="addSector()"
                                       @paste="pasteSectors($event)"
                                       class="block w-full border-0 p-0 text-[#1F2937] focus:ring-0 select-text"
                                       placeholder="Escriba un giro, Enter, o pegue varios separados por coma" />
                                <template x-for="(item, idx) in sectors" :key="'hidden-' + idx">
                                    <input type="hidden" name="sector[]" :value="item">
                                </template>
                            </div>
                            <p class="mt-1 text-xs text-white/70">Escriba y presione Enter para agregar; puede quitar con la «x» en cada etiqueta.</p>
                            <x-input-error :messages="$errors->get('sector')" class="mt-2" />
                            <x-input-error :messages="$errors->get('sector.*')" class="mt-2" />
                            <script type="application/json" id="edit-sectors-data">{!! json_encode($initialSectors, JSON_UNESCAPED_UNICODE) !!}</script>
                        </div>

                        <div>
                            <x-input-label for="municipio" value="Municipio" />
                            <x-text-input id="municipio" name="municipio" type="text" class="mt-1 block w-full" :value="old('municipio', $company->municipio)" />
                            <x-input-error :messages="$errors->get('municipio')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="estado" value="Entidad federativa" />
                            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $company->estado)" />
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <p class="text-sm font-semibold text-white/95 border-b border-white/15 pb-2 mb-1">Contacto telefónico</p>
                        </div>
                        <div>
                            <x-input-label for="telefono" value="Teléfono" />
                            <x-text-input id="telefono" name="telefono" type="tel" class="mt-1 block w-full" :value="old('telefono', $company->telefono)" maxlength="50" autocomplete="tel" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="celular" value="Celular" />
                            <x-text-input id="celular" name="celular" type="tel" class="mt-1 block w-full" :value="old('celular', $company->celular)" maxlength="50" autocomplete="tel" />
                            <x-input-error :messages="$errors->get('celular')" class="mt-2" />
                        </div>

                        <x-executive-assignment-field
                            :executiveUsers="$executiveUsers"
                            :isAdmin="$isAdmin"
                            :selectedAssignedUserId="$selectedAssignedUserId"
                            :readonlyExecutiveName="$readonlyExecutiveName"
                            inputId="company_ejecutivo"
                            selectClass="mt-1 block w-full rounded-md border-gray-300 bg-white text-[#1F2937] py-2 px-3"
                            readonlyClass="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-900 py-2 px-3"
                            hint="Solo cuentas con rol ejecutivo (usuario)."
                        />

                        <div class="md:col-span-2">
                            <x-input-label for="datos_fiscales" value="Datos Fiscales" />
                            <textarea id="datos_fiscales" name="datos_fiscales" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('datos_fiscales', $company->datos_fiscales) }}</textarea>
                            <x-input-error :messages="$errors->get('datos_fiscales')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $company)) }}" class="btn-panel-dark bg-white/10 text-white border-2 border-[#FFE600] hover:bg-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancelar
                        </a>
                        <button type="submit" class="btn-amber-app">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
    </div>

    @push('scripts')
    <script>
        function companyEditSectors() {
            return {
                sectorInput: '',
                sectors: [],
                init() {
                    const node = document.getElementById('edit-sectors-data');
                    if (!node) return;
                    try {
                        const parsed = JSON.parse(node.textContent || '[]');
                        if (Array.isArray(parsed)) {
                            this.sectors = parsed
                                .map((v) => String(v).trim())
                                .filter((v) => v.length > 0);
                        }
                    } catch (_) {}
                },
                addSector() {
                    const value = (this.sectorInput || '').trim();
                    if (!value) return;
                    if (!this.sectors.some((s) => s.toLowerCase() === value.toLowerCase())) {
                        this.sectors.push(value);
                    }
                    this.sectorInput = '';
                },
                removeSector(index) {
                    this.sectors.splice(index, 1);
                },
                pasteSectors(event) {
                    const cd = event.clipboardData || (typeof window !== 'undefined' ? window.clipboardData : null);
                    if (!cd) return;
                    const text = (cd.getData('text/plain') || '').trim();
                    if (!text) return;
                    event.preventDefault();
                    const parts = text.split(/[,;\n\r\t]+/).map((s) => s.trim()).filter((s) => s.length > 0);
                    parts.forEach((p) => {
                        if (!this.sectors.some((s) => s.toLowerCase() === p.toLowerCase())) {
                            this.sectors.push(p);
                        }
                    });
                    this.sectorInput = '';
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
