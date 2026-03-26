<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Nueva Empresa</h2>
            <p class="page-header-card__subtitle">Registrar nueva empresa (quedará Pendiente hasta aprobación)</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark rounded-lg border-2 border-[#FFE600]/50 p-4">
            <p class="text-sm text-white/90">La empresa se guardará con estatus <strong class="text-[#FFE600]">Pendiente</strong>. Un administrador deberá aprobarla para que se refleje en el sistema global.</p>
        </div>
        <div class="panel-card-dark p-6">
                <form method="POST" action="{{ route('companies.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nombre_comercial" value="Nombre Comercial *" class="text-white" />
                            <x-text-input id="nombre_comercial" name="nombre_comercial" type="text" class="mt-1 block w-full bg-white text-[#1F2937] border-[#E2E8F0] focus:border-[#FFE600] focus:ring-4 focus:ring-[#FFE600]/40" :value="old('nombre_comercial')" required />
                            <x-input-error :messages="$errors->get('nombre_comercial')" class="mt-2 text-red-300" />
                        </div>
                        <div>
                            <x-input-label for="rfc" value="RFC" class="text-white" />
                            <x-text-input id="rfc" name="rfc" type="text" class="mt-1 block w-full uppercase bg-white text-[#1F2937]" :value="old('rfc')" maxlength="13" />
                            <x-input-error :messages="$errors->get('rfc')" class="mt-2 text-red-300" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="sector" value="Sector/Giro *" class="text-white" />
                            <x-text-input id="sector" name="sector" type="text" class="mt-1 block w-full bg-white text-[#1F2937]" :value="old('sector')" required />
                            <x-input-error :messages="$errors->get('sector')" class="mt-2 text-red-300" />
                        </div>
                        <div>
                            <x-input-label for="municipio" value="Municipio" class="text-white" />
                            <x-text-input id="municipio" name="municipio" type="text" class="mt-1 block w-full bg-white text-[#1F2937]" :value="old('municipio')" />
                        </div>
                        <div>
                            <x-input-label for="estado" value="Estado" class="text-white" />
                            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full bg-white text-[#1F2937]" :value="old('estado')" />
                        </div>
                        <div>
                            <x-input-label for="ejecutivo_asignado" value="Ejecutivo Asignado" class="text-white" />
                            <x-text-input id="ejecutivo_asignado" name="ejecutivo_asignado" type="text" class="mt-1 block w-full bg-white text-[#1F2937]" :value="old('ejecutivo_asignado')" />
                        </div>
                        <div>
                            <x-input-label for="status_color" value="Estado de prospecto" class="text-white" />
                            <select id="status_color" name="status_color" class="mt-1 block w-full rounded-md border-[#E2E8F0] bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-4 focus:ring-[#FFE600]/40">
                                @foreach(\App\Models\Company::PROSPECT_STATUS_LABELS as $value => $label)
                                    <option value="{{ $value }}" {{ old('status_color', 'seguimiento') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="datos_fiscales" value="Datos Fiscales" class="text-white" />
                            <textarea id="datos_fiscales" name="datos_fiscales" rows="4" class="mt-1 block w-full rounded-md border-[#E2E8F0] bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-4 focus:ring-[#FFE600]/40">{{ old('datos_fiscales') }}</textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ route('companies.index') }}" class="btn-danger-app">Cancelar</a>
                        <button type="submit" class="btn-amber-app">Guardar (Pendiente de aprobación)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-user-layout>
