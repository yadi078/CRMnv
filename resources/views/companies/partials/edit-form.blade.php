<form method="POST" action="{{ route('companies.update', $company) }}" id="company-edit-form-modal" class="company-edit-form-in-modal">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <x-input-label for="modal_nombre_comercial" value="Nombre Comercial *" />
            <x-text-input id="modal_nombre_comercial" name="nombre_comercial" type="text" class="mt-1 block w-full" :value="old('nombre_comercial', $company->nombre_comercial)" required />
            <x-input-error :messages="$errors->get('nombre_comercial')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="modal_rfc" value="RFC" />
            <x-text-input id="modal_rfc" name="rfc" type="text" class="mt-1 block w-full uppercase" :value="old('rfc', $company->rfc)" maxlength="13" />
            <x-input-error :messages="$errors->get('rfc')" class="mt-2" />
        </div>

        <div class="md:col-span-2">
            <x-input-label for="modal_sector" value="Sector/Giro *" />
            <x-text-input id="modal_sector" name="sector" type="text" class="mt-1 block w-full" :value="old('sector', $company->sector)" required />
            <x-input-error :messages="$errors->get('sector')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="modal_municipio" value="Municipio" />
            <x-text-input id="modal_municipio" name="municipio" type="text" class="mt-1 block w-full" :value="old('municipio', $company->municipio)" />
            <x-input-error :messages="$errors->get('municipio')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="modal_estado" value="Estado" />
            <x-text-input id="modal_estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $company->estado)" />
            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="modal_ejecutivo_asignado" value="Ejecutivo Asignado" />
            <x-text-input id="modal_ejecutivo_asignado" name="ejecutivo_asignado" type="text" class="mt-1 block w-full" :value="old('ejecutivo_asignado', $company->ejecutivo_asignado)" />
            <x-input-error :messages="$errors->get('ejecutivo_asignado')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="modal_status_color" value="Estado de prospecto" />
            <select id="modal_status_color" name="status_color" class="mt-1 block w-full rounded-md border-gray-300 dark:border-white/20 dark:bg-white/10 dark:text-white">
                @foreach(\App\Models\Company::PROSPECT_STATUS_LABELS as $value => $label)
                    <option value="{{ $value }}" {{ old('status_color', $company->status_color) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status_color')" class="mt-2" />
        </div>

        <div class="md:col-span-2">
            <x-input-label for="modal_datos_fiscales" value="Datos Fiscales" />
            <textarea id="modal_datos_fiscales" name="datos_fiscales" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-white/20 dark:bg-white/10 dark:text-white">{{ old('datos_fiscales', $company->datos_fiscales) }}</textarea>
            <x-input-error :messages="$errors->get('datos_fiscales')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
        <button type="button" class="js-company-modal-close company-edit-modal__btn-cancel inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cerrar
        </button>
        <button type="submit" class="btn-amber-app">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Actualizar
        </button>
    </div>
</form>
