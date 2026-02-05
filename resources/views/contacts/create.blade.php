<x-app-layout>
    <x-slot name="header">
        <div class="view-header">
            <div class="view-header__icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <div>
                <h2 class="view-header__title">Nuevo Contacto</h2>
                <p class="view-header__subtitle">Registrar nuevo contacto</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="view-card p-6">
                <form method="POST" action="{{ route('contacts.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="company_id" value="Empresa *" />
                            <select id="company_id" name="company_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">Seleccione una empresa</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ (old('company_id', $companyId ?? null) == $company->id) ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="nombre_completo" value="Nombre Completo *" />
                            <x-text-input id="nombre_completo" name="nombre_completo" type="text" class="mt-1 block w-full" :value="old('nombre_completo')" required />
                            <x-input-error :messages="$errors->get('nombre_completo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="puesto_de_trabajo" value="Puesto de Trabajo" />
                            <x-text-input id="puesto_de_trabajo" name="puesto_de_trabajo" type="text" class="mt-1 block w-full" :value="old('puesto_de_trabajo')" />
                            <x-input-error :messages="$errors->get('puesto_de_trabajo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="departamento" value="Departamento" />
                            <x-text-input id="departamento" name="departamento" type="text" class="mt-1 block w-full" :value="old('departamento')" />
                            <x-input-error :messages="$errors->get('departamento')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Correo electrónico *" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="celular" value="Celular" />
                            <x-text-input id="celular" name="celular" type="text" class="mt-1 block w-full" :value="old('celular')" />
                            <x-input-error :messages="$errors->get('celular')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="extension" value="Extensión" />
                            <x-text-input id="extension" name="extension" type="text" class="mt-1 block w-full" :value="old('extension')" />
                            <x-input-error :messages="$errors->get('extension')" class="mt-2" />
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

                        <div class="md:col-span-2">
                            <x-input-label for="notas" value="Notas" />
                            <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('notas') }}</textarea>
                            <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ route('contacts.index') }}" class="btn-icon-text text-gray-600 hover:text-gray-800 px-4 py-2 rounded-xl border border-gray-300 hover:bg-gray-50">
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
