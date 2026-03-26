<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Nuevo Contacto</h2>
            <p class="page-header-card__subtitle">Registrar nuevo contacto</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-6">
                <form id="form-nuevo-contacto" method="POST" action="{{ route('contacts.store') }}">
                    @csrf

                    <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Datos del contacto</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="company_id" class="block text-sm font-medium text-white/90 mb-1">Empresa *</label>
                            <select id="company_id" name="company_id" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white [&>option]:bg-[#1a3d6b] [&>option]:text-white py-2.5 px-3" required>
                                <option value="">Seleccione una empresa</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ (old('company_id', $companyId ?? null) == $company->id) ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2 text-red-300" />
                        </div>

                        <div class="md:col-span-2">
                            <label for="nombre_completo" class="block text-sm font-medium text-white/90 mb-1">Nombre Completo *</label>
                            <input id="nombre_completo" name="nombre_completo" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('nombre_completo') }}" required />
                            <x-input-error :messages="$errors->get('nombre_completo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="genero" value="Género" />
                            <select id="genero" name="genero" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Seleccione</option>
                                <option value="Masculino" {{ old('genero') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="Femenino" {{ old('genero') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                <option value="Otro" {{ old('genero') === 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            <x-input-error :messages="$errors->get('genero')" class="mt-2" />
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
                        <div class="flex items-center gap-3 md:col-span-2">
                            <input id="email_activo" name="email_activo" type="checkbox" value="1" class="rounded border-gray-300 text-amber-500 shadow-sm focus:border-amber-500 focus:ring-amber-500" @checked(old('email_activo', true)) />
                            <label for="email_activo" class="text-sm text-white/90 select-none">
                                Mostrar correo en fichas, listados y PDF
                            </label>
                        </div>

                        <div>
                            <x-input-label for="telefono" value="Teléfono" />
                            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono')" placeholder="Teléfono fijo" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
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
                            <x-input-label for="fecha_cumpleanos" value="Fecha de cumpleaños (opcional)" />
                            <x-text-input id="fecha_cumpleanos" name="fecha_cumpleanos" type="date" class="mt-1 block w-full text-gray-900" :value="old('fecha_cumpleanos')" />
                            <p class="mt-1 text-xs text-white/60">Para enviar felicitaciones al administrador el día del cumpleaños.</p>
                            <x-input-error :messages="$errors->get('fecha_cumpleanos')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="municipio" value="Municipio / Ciudad" />
                            <x-text-input id="municipio" name="municipio" type="text" class="mt-1 block w-full" :value="old('municipio')" />
                            <x-input-error :messages="$errors->get('municipio')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="estado" value="Estado" />
                            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado')" />
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2 mt-2 pt-6 border-t border-white/20">
                            <h3 class="text-lg font-semibold text-[#FFE600] mb-2">Datos para ficha de registro del cliente</h3>
                            <p class="text-sm text-white/80 mb-4">Razón social, nombre comercial, domicilio fiscal, RFC y régimen. TEL se toma de Teléfono/Celular de arriba.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <x-input-label for="razon_social" value="RAZÓN SOCIAL" />
                                    <x-text-input id="razon_social" name="razon_social" type="text" class="mt-1 block w-full" :value="old('razon_social')" />
                                    <x-input-error :messages="$errors->get('razon_social')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="nombre_comercial" value="Nombre comercial" />
                                    <x-text-input id="nombre_comercial" name="nombre_comercial" type="text" class="mt-1 block w-full" :value="old('nombre_comercial')" />
                                    <x-input-error :messages="$errors->get('nombre_comercial')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="calle_numero" value="CALLE Y NÚMERO" />
                                    <x-text-input id="calle_numero" name="calle_numero" type="text" class="mt-1 block w-full" :value="old('calle_numero')" />
                                    <x-input-error :messages="$errors->get('calle_numero')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="colonia_cp" value="COLONIA Y C.P." />
                                    <x-text-input id="colonia_cp" name="colonia_cp" type="text" class="mt-1 block w-full" :value="old('colonia_cp')" />
                                    <x-input-error :messages="$errors->get('colonia_cp')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="rfc" value="RFC" />
                                    <x-text-input id="rfc" name="rfc" type="text" class="mt-1 block w-full" :value="old('rfc')" />
                                    <x-input-error :messages="$errors->get('rfc')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="regimen_fiscal" value="RÉGIMEN EN QUE TRIBUTA" />
                                    <x-text-input id="regimen_fiscal" name="regimen_fiscal" type="text" class="mt-1 block w-full" :value="old('regimen_fiscal')" />
                                    <x-input-error :messages="$errors->get('regimen_fiscal')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="notas" value="Notas" />
                            <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('notas') }}</textarea>
                            <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="status_color" value="Estado de prospecto" />
                            <select id="status_color" name="status_color" class="mt-1 block w-full rounded-md border-gray-300">
                                @foreach(\App\Models\Contact::PROSPECT_STATUS_LABELS as $value => $label)
                                <option value="{{ $value }}" {{ old('status_color', 'seguimiento') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status_color')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ route('contacts.index') }}" class="btn-panel-dark bg-white/10 text-white border-2 border-[#FFE600] hover:bg-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancelar
                        </a>
                        <button type="submit" id="btn-guardar-contacto" class="btn-amber-app">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-modal-success id="modal-registro-exitoso" />
    <x-modal-error id="modal-error" title="No se pudo registrar" />

    @if(session('error') || $errors->any())
    <div id="initial-error-message" class="hidden" data-message="{{ e(session('error') ?: 'Por favor corrige los errores del formulario. El contacto no se registró.') }}"></div>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('form-nuevo-contacto');
        var modal = document.getElementById('modal-registro-exitoso');
        var modalError = document.getElementById('modal-error');
        var modalErrorMessage = document.getElementById('modal-error-message');
        var btnGuardar = document.getElementById('btn-guardar-contacto');
        var acceptBtn = document.getElementById('modal-registro-exitoso-accept');
        var closeBtn = document.getElementById('modal-registro-exitoso-close');
        var backdrop = document.getElementById('modal-registro-exitoso-backdrop');
        var errorAcceptBtn = document.getElementById('modal-error-accept');
        var errorCloseBtn = document.getElementById('modal-error-close');
        var errorBackdrop = document.getElementById('modal-error-backdrop');

        function showModal() {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideModalAndResetForm() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            form.reset();
            var companySelect = document.getElementById('company_id');
            if (companySelect) companySelect.selectedIndex = 0;
        }

        function showErrorModal(message) {
            if (modalErrorMessage) modalErrorMessage.textContent = message || 'Error al crear el contacto. Por favor, intente nuevamente.';
            modalError.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideErrorModal() {
            modalError.classList.add('hidden');
            document.body.style.overflow = '';
        }

        var initialError = document.getElementById('initial-error-message');
        if (initialError && initialError.getAttribute('data-message')) {
            showErrorModal(initialError.getAttribute('data-message'));
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(form);
                var url = form.getAttribute('action');
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<svg class="animate-spin h-5 w-5 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Guardando...';

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(function(r) {
                    return r.json().then(function(data) {
                        return { status: r.status, data: data };
                    }).catch(function() {
                        return { status: r.status, data: {} };
                    });
                })
                .then(function(result) {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Guardar';

                    if (result.status === 201 && result.data.success) {
                        showModal();
                    } else if (result.status === 422) {
                        var msg = (result.data && result.data.message) || 'Por favor corrige los errores. El contacto no se registró.';
                        if (result.data && result.data.errors) {
                            var firstKey = Object.keys(result.data.errors)[0];
                            if (firstKey) msg = result.data.errors[firstKey][0] || msg;
                        }
                        showErrorModal(msg);
                    } else {
                        showErrorModal(result.data.message || 'Error al crear el contacto. Por favor, intente nuevamente.');
                    }
                })
                .catch(function() {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Guardar';
                    showErrorModal('Error de conexión. Por favor, intente nuevamente.');
                });
            });
        }

        if (acceptBtn) acceptBtn.addEventListener('click', hideModalAndResetForm);
        if (closeBtn) closeBtn.addEventListener('click', hideModalAndResetForm);
        if (backdrop) backdrop.addEventListener('click', hideModalAndResetForm);
        if (errorAcceptBtn) errorAcceptBtn.addEventListener('click', hideErrorModal);
        if (errorCloseBtn) errorCloseBtn.addEventListener('click', hideErrorModal);
        if (errorBackdrop) errorBackdrop.addEventListener('click', hideErrorModal);
    });
    </script>
</x-app-layout>
