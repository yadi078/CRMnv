<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
        </div>
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
                        <div class="relative">
                            <label for="company_autocomplete" class="block text-sm font-medium text-white/90 mb-1">Empresa *</label>
                            <input type="hidden" id="company_id" name="company_id" value="{{ old('company_id', $companyId ?? '') }}" required />
                            @php
                                $preselectedId = old('company_id', $companyId ?? null);
                                $preselectedName = $preselectedId ? ($companies->firstWhere('id', (int)$preselectedId)?->nombre_comercial ?? '') : '';
                            @endphp
                            <input type="text" id="company_autocomplete" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3 focus:ring-2 focus:ring-[#FFE600]/50" placeholder="Escriba o seleccione una empresa" value="{{ $preselectedName }}" autocomplete="off" />
                            <div id="company_autocomplete_list" class="absolute left-0 right-0 top-full z-10 mt-1 max-h-56 overflow-auto rounded-xl border border-white/20 bg-[#1a3d6b] shadow-lg hidden"></div>
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2 text-red-300" />
                        </div>

                        <div>
                            <label for="nombre_completo" class="block text-sm font-medium text-white/90 mb-1">Nombre Completo *</label>
                            <input id="nombre_completo" name="nombre_completo" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('nombre_completo') }}" minlength="4" maxlength="255" required title="Mínimo 4 caracteres" />
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
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required pattern="^[^@]+@[^@]+\.com$" title="Debe ser un correo válido que termine en .com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telefono" value="Teléfono" />
                            <x-text-input id="telefono" name="telefono" type="tel" class="mt-1 block w-full bg-white text-gray-900" :value="old('telefono')" placeholder="Teléfono fijo" inputmode="numeric" pattern="[0-9]{7,15}" maxlength="15" title="Solo números, entre 7 y 15 dígitos" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="celular" value="Celular" />
                            <x-text-input id="celular" name="celular" type="tel" class="mt-1 block w-full bg-white text-gray-900" :value="old('celular')" inputmode="numeric" pattern="[0-9]{8,15}" maxlength="15" title="Solo números, entre 8 y 15 dígitos" />
                            <x-input-error :messages="$errors->get('celular')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="extension" value="Extensión" />
                            <x-text-input id="extension" name="extension" type="text" class="mt-1 block w-full bg-white text-gray-900" :value="old('extension')" inputmode="numeric" pattern="[0-9]{1,6}" maxlength="6" title="Solo números, máximo 6 dígitos" />
                            <x-input-error :messages="$errors->get('extension')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="municipio" value="Municipio" />
                            <x-text-input id="municipio" name="municipio" type="text" class="mt-1 block w-full" :value="old('municipio')" maxlength="70" />
                            <x-input-error :messages="$errors->get('municipio')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="estado" value="Estado" />
                            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado')" maxlength="70" pattern="^[^0-9]*$" title="No se permiten números y máximo 70 caracteres" />
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="notas" value="Notas" />
                            <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('notas') }}</textarea>
                            <x-input-error :messages="$errors->get('notas')" class="mt-2" />
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
        var companiesData = @json($companies->map(fn($c) => ['id' => $c->id, 'nombre_comercial' => $c->nombre_comercial]));
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

        var companyInput = document.getElementById('company_autocomplete');
        var companyIdInput = document.getElementById('company_id');
        var companyList = document.getElementById('company_autocomplete_list');

        function showModal() {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideModalAndResetForm() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            form.reset();
            if (companyIdInput) companyIdInput.value = '';
            if (companyInput) companyInput.value = '';
        }

        function filterCompanies(q) {
            var qq = (q || '').toLowerCase().trim();
            if (!qq) return companiesData;
            return companiesData.filter(function(c) { return (c.nombre_comercial || '').toLowerCase().indexOf(qq) !== -1; });
        }

        function renderCompanyList(items) {
            if (!companyList) return;
            companyList.innerHTML = '';
            if (items.length === 0) {
                companyList.classList.add('hidden');
                return;
            }
            items.forEach(function(c) {
                var div = document.createElement('div');
                div.className = 'px-3 py-2.5 text-white/90 hover:bg-white/15 cursor-pointer text-sm';
                div.textContent = c.nombre_comercial;
                div.dataset.id = c.id;
                div.dataset.name = c.nombre_comercial;
                div.addEventListener('click', function() {
                    companyIdInput.value = c.id;
                    companyInput.value = c.nombre_comercial;
                    companyList.classList.add('hidden');
                    companyInput.blur();
                });
                companyList.appendChild(div);
            });
            companyList.classList.remove('hidden');
        }

        if (companyInput && companyList) {
            companyInput.addEventListener('focus', function() { renderCompanyList(filterCompanies(companyInput.value)); });
            companyInput.addEventListener('input', function() {
                companyIdInput.value = '';
                renderCompanyList(filterCompanies(companyInput.value));
            });
            companyInput.addEventListener('blur', function() {
                setTimeout(function() { companyList.classList.add('hidden'); }, 200);
            });
            document.addEventListener('click', function(e) {
                if (!companyList.contains(e.target) && e.target !== companyInput) companyList.classList.add('hidden');
            });
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

        // Forzar solo números en teléfono, celular y extensión
        function enforceNumericInput(input) {
            if (!input) return;
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        enforceNumericInput(document.getElementById('telefono'));
        enforceNumericInput(document.getElementById('celular'));
        enforceNumericInput(document.getElementById('extension'));

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (companyIdInput && companyInput && !companyIdInput.value && companyInput.value.trim()) {
                    var match = companiesData.find(function(c) { return (c.nombre_comercial || '').trim().toLowerCase() === companyInput.value.trim().toLowerCase(); });
                    if (match) {
                        companyIdInput.value = match.id;
                    }
                }
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
                        if (result.data.redirect) {
                            window.location.href = result.data.redirect;
                        } else {
                            showModal();
                        }
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
