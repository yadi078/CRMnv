<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg></x-page-header-avatar>
            <div>
                <h2 class="page-header-card__title">Editar Empresa</h2>
            <p class="page-header-card__subtitle">{{ $company->nombre_comercial }}</p>
            </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="panel-card-dark p-6">
                <form method="POST" action="{{ route('companies.update', $company) }}">
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
                            <x-input-label for="sector" value="Sector/Giro *" />
                            <x-text-input id="sector" name="sector" type="text" class="mt-1 block w-full" :value="old('sector', $company->sector)" required />
                        </div>
                        <div>
                            <x-input-label for="municipio" value="Municipio" />
                            <x-text-input id="municipio" name="municipio" type="text" class="mt-1 block w-full" :value="old('municipio', $company->municipio)" />
                        </div>
                        <div>
                            <x-input-label for="estado" value="Entidad federativa" />
                            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $company->estado)" />
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
                            selectClass="mt-1 block w-full rounded-md border-gray-300 py-2 px-3"
                            readonlyClass="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 py-2 px-3"
                        />
                        <div>
                            <x-input-label for="status_color" value="Estatus de prospecto" />
                            <select id="status_color" name="status_color" class="mt-1 block w-full rounded-md border-gray-300">
                                @foreach(\App\Models\Company::PROSPECT_STATUS_LABELS as $value => $label)
                                    <option value="{{ $value }}" {{ old('status_color', $company->status_color) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="datos_fiscales" value="Datos Fiscales" />
                            <textarea id="datos_fiscales" name="datos_fiscales" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('datos_fiscales', $company->datos_fiscales) }}</textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $company)) }}" class="btn-icon-text text-gray-600 hover:text-gray-800 px-4 py-2 rounded-xl border border-gray-300 hover:bg-gray-50">Cancelar</a>
                        <button type="submit" class="btn-amber-app">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-user-layout>
