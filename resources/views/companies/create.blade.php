<x-app-layout>
    <x-slot name="header">
        <div class="view-header">
            <div class="view-header__icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h2 class="view-header__title">Nueva Empresa</h2>
                <p class="view-header__subtitle">Registrar nueva empresa</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="view-card p-6">
                <form method="POST" action="{{ route('companies.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nombre_comercial" value="Nombre Comercial *" />
                            <x-text-input id="nombre_comercial" name="nombre_comercial" type="text" class="mt-1 block w-full" :value="old('nombre_comercial')" required />
                            <x-input-error :messages="$errors->get('nombre_comercial')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="rfc" value="RFC *" />
                            <x-text-input id="rfc" name="rfc" type="text" class="mt-1 block w-full uppercase" :value="old('rfc')" maxlength="12" required />
                            <x-input-error :messages="$errors->get('rfc')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="sector" value="Sector/Giro" />
                            <x-text-input id="sector" name="sector" type="text" class="mt-1 block w-full" :value="old('sector')" />
                            <x-input-error :messages="$errors->get('sector')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="municipio" value="Municipio" />
                            <x-text-input id="municipio" name="municipio" type="text" class="mt-1 block w-full" :value="old('municipio')" />
                            <x-input-error :messages="$errors->get('municipio')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="estado" value="Estado" />
                            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado')" />
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ejecutivo_asignado" value="Ejecutivo Asignado" />
                            <x-text-input id="ejecutivo_asignado" name="ejecutivo_asignado" type="text" class="mt-1 block w-full" :value="old('ejecutivo_asignado')" />
                            <x-input-error :messages="$errors->get('ejecutivo_asignado')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status_color" value="Estado Inicial" />
                            <select id="status_color" name="status_color" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="amarillo" {{ old('status_color', 'amarillo') === 'amarillo' ? 'selected' : '' }}>Amarillo</option>
                                <option value="verde" {{ old('status_color') === 'verde' ? 'selected' : '' }}>Verde</option>
                                <option value="rojo" {{ old('status_color') === 'rojo' ? 'selected' : '' }}>Rojo</option>
                            </select>
                            <x-input-error :messages="$errors->get('status_color')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="datos_fiscales" value="Datos Fiscales" />
                            <textarea id="datos_fiscales" name="datos_fiscales" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('datos_fiscales') }}</textarea>
                            <x-input-error :messages="$errors->get('datos_fiscales')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ route('companies.index') }}" class="btn-icon-text text-gray-600 hover:text-gray-800 px-4 py-2 rounded-xl border border-gray-300 hover:bg-gray-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancelar
                        </a>
                        <button type="submit" class="btn-amber-app">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Aceptar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
