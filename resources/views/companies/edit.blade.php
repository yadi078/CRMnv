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

    <div class="space-y-8">
        <div class="panel-card-dark p-6">
                <form method="POST" action="{{ route('companies.update', $company) }}">
                    @csrf
                    @method('PUT')

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
                            <x-input-label for="sector" value="Sector/Giro * (puede seleccionar varios)" />
                            <select id="sector" name="sector[]" multiple
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-4 focus:ring-[#FFE600]/40">
                                @php
                                    $currentSectors = collect(old('sector', $company->sector ? explode(',', $company->sector) : []))
                                        ->map(fn($s) => trim((string)$s))
                                        ->filter()
                                        ->values();
                                @endphp
                                @foreach($currentSectors as $s)
                                    <option value="{{ $s }}" selected>{{ $s }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-white/70">Puede asignar múltiples sectores/giro a la empresa.</p>
                            <x-input-error :messages="$errors->get('sector')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="municipio" value="Municipio" />
                            <x-text-input id="municipio" name="municipio" type="text" class="mt-1 block w-full" :value="old('municipio', $company->municipio)" />
                            <x-input-error :messages="$errors->get('municipio')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="estado" value="Estado" />
                            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $company->estado)" />
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ejecutivo_asignado" value="Ejecutivo Asignado" />
                            <x-text-input id="ejecutivo_asignado" name="ejecutivo_asignado" type="text" class="mt-1 block w-full" :value="old('ejecutivo_asignado', $company->ejecutivo_asignado)" />
                            <x-input-error :messages="$errors->get('ejecutivo_asignado')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="datos_fiscales" value="Datos Fiscales" />
                            <textarea id="datos_fiscales" name="datos_fiscales" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('datos_fiscales', $company->datos_fiscales) }}</textarea>
                            <x-input-error :messages="$errors->get('datos_fiscales')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ route('companies.show', $company) }}" class="btn-panel-dark bg-white/10 text-white border-2 border-[#FFE600] hover:bg-white/20">
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
    </div>
</x-app-layout>
