<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-azul-fuerte leading-tight">
            Editar Empresa: {{ $company->nombre_comercial }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md p-6">
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
                            <x-input-label for="rfc" value="RFC *" />
                            <x-text-input id="rfc" name="rfc" type="text" class="mt-1 block w-full uppercase" :value="old('rfc', $company->rfc)" maxlength="12" required />
                            <x-input-error :messages="$errors->get('rfc')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="sector" value="Sector/Giro" />
                            <x-text-input id="sector" name="sector" type="text" class="mt-1 block w-full" :value="old('sector', $company->sector)" />
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

                        <div>
                            <x-input-label for="status_color" value="Estado" />
                            <select id="status_color" name="status_color" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="verde" {{ old('status_color', $company->status_color) === 'verde' ? 'selected' : '' }}>Verde</option>
                                <option value="amarillo" {{ old('status_color', $company->status_color) === 'amarillo' ? 'selected' : '' }}>Amarillo</option>
                                <option value="rojo" {{ old('status_color', $company->status_color) === 'rojo' ? 'selected' : '' }}>Rojo</option>
                            </select>
                            <x-input-error :messages="$errors->get('status_color')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="datos_fiscales" value="Datos Fiscales" />
                            <textarea id="datos_fiscales" name="datos_fiscales" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('datos_fiscales', $company->datos_fiscales) }}</textarea>
                            <x-input-error :messages="$errors->get('datos_fiscales')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 space-x-4">
                        <a href="{{ route('companies.show', $company) }}" class="text-gray-600 hover:text-gray-800">Cancelar</a>
                        <x-primary-button class="bg-amarillo text-azul-fuerte hover:bg-yellow-400">
                            Actualizar
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
