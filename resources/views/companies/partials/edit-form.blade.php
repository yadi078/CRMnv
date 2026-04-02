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
            <x-input-label for="modal_estado" value="Entidad federativa" />
            <x-text-input id="modal_estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $company->estado)" />
            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
        </div>

        <div class="md:col-span-2">
            <p class="text-sm font-semibold text-white/95 border-b border-white/15 pb-2 mb-1">Contacto telefónico</p>
        </div>
        <div>
            <x-input-label for="modal_telefono" value="Teléfono" />
            <x-text-input id="modal_telefono" name="telefono" type="tel" class="mt-1 block w-full" :value="old('telefono', $company->telefono)" maxlength="50" autocomplete="tel" />
            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="modal_celular" value="Celular" />
            <x-text-input id="modal_celular" name="celular" type="tel" class="mt-1 block w-full" :value="old('celular', $company->celular)" maxlength="50" autocomplete="tel" />
            <x-input-error :messages="$errors->get('celular')" class="mt-2" />
        </div>

        <x-executive-assignment-field
            :executiveUsers="$executiveUsers"
            :isAdmin="$isAdmin"
            :selectedAssignedUserId="$selectedAssignedUserId"
            :readonlyExecutiveName="$readonlyExecutiveName"
            inputId="modal_company_ejecutivo"
            selectClass="mt-1 block w-full rounded-md border-gray-300 dark:border-white/20 dark:bg-white/10 dark:text-white py-2 px-3"
            readonlyClass="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 dark:bg-white/10 dark:text-white py-2 px-3"
        />

        <div>
            <x-input-label for="modal_status_color" value="Estatus de prospecto" />
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
