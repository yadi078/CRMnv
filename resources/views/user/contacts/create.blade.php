<x-app-user-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Nuevo Contacto</h2>
            <p class="page-header-card__subtitle">Registrar nuevo contacto (vinculado a empresa)</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-6">
                <form id="form-nuevo-contacto" method="POST" action="{{ route('contacts.store') }}">
                    @csrf
                    <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Datos del contacto</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2 relative">
                            <label for="company_autocomplete" class="block text-sm font-medium text-white/90 mb-1">Empresa *</label>
                            <p class="text-xs text-white/65 mb-2">Escriba para filtrar o elija de la lista. Incluye empresas aprobadas en el CRM y las que usted haya registrado.</p>
                            <input type="hidden" id="company_id" name="company_id" value="{{ old('company_id', $companyId ?? '') }}" required />
                            @php
                                $preselectedId = old('company_id', $companyId ?? null);
                                $preselectedName = $preselectedId ? ($companies->firstWhere('id', (int) $preselectedId)?->nombre_comercial ?? '') : '';
                            @endphp
                            <input type="text" id="company_autocomplete" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3 focus:ring-2 focus:ring-[#FFE600]/50" placeholder="Buscar o seleccionar empresa…" value="{{ $preselectedName }}" autocomplete="off" />
                            <div id="company_autocomplete_list" role="listbox" class="absolute left-0 right-0 top-full z-[100] mt-1 max-h-56 overflow-auto rounded-xl border border-white/20 bg-[#1a3d6b] shadow-lg hidden"></div>
                            @if($companies->isEmpty())
                                <p class="mt-2 text-sm text-amber-200/90">No hay empresas en el catálogo. Registre una empresa o espere aprobación.</p>
                            @endif
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2 text-red-300" />
                        </div>
                        <div>
                            <label for="nombre_completo" class="block text-sm font-medium text-white/90 mb-1">Nombre Completo *</label>
                            <input id="nombre_completo" name="nombre_completo" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('nombre_completo') }}" minlength="4" maxlength="255" required title="Mínimo 4 caracteres" />
                            <x-input-error :messages="$errors->get('nombre_completo')" class="mt-2 text-red-300" />
                        </div>
                        <div>
                            <label for="genero" class="block text-sm font-medium text-white/90 mb-1">Género</label>
                            <select id="genero" name="genero" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white [&>option]:bg-[#1a3d6b] [&>option]:text-white py-2.5 px-3">
                                <option value="">Seleccione</option>
                                <option value="Masculino" {{ old('genero') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="Femenino" {{ old('genero') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                <option value="Otro" {{ old('genero') === 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                        <div>
                            <label for="puesto_de_trabajo" class="block text-sm font-medium text-white/90 mb-1">Puesto de Trabajo</label>
                            <input id="puesto_de_trabajo" name="puesto_de_trabajo" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('puesto_de_trabajo') }}" />
                        </div>
                        <div>
                            <label for="departamento" class="block text-sm font-medium text-white/90 mb-1">Departamento</label>
                            <input id="departamento" name="departamento" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('departamento') }}" />
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-white/90 mb-1">Correo electrónico *</label>
                            <input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('email') }}" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-300" />
                        </div>
                        <div class="md:col-span-2 flex items-center gap-3">
                            <input id="email_activo" name="email_activo" type="checkbox" value="1" class="rounded border-gray-300 text-amber-500 shadow-sm focus:border-amber-500 focus:ring-amber-500" @checked(old('email_activo', true)) />
                            <label for="email_activo" class="text-sm text-white/90 select-none">
                                Mostrar correo en fichas, listados y PDF
                            </label>
                        </div>
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-white/90 mb-1">Teléfono</label>
                            <input id="telefono" name="telefono" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('telefono') }}" placeholder="Teléfono fijo" />
                        </div>
                        <div>
                            <label for="celular" class="block text-sm font-medium text-white/90 mb-1">Celular</label>
                            <input id="celular" name="celular" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('celular') }}" />
                        </div>
                        <div>
                            <label for="extension" class="block text-sm font-medium text-white/90 mb-1">Extensión</label>
                            <input id="extension" name="extension" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('extension') }}" />
                        </div>
                        <div>
                            <label for="fecha_cumpleanos" class="block text-sm font-medium text-white/90 mb-1">Fecha de cumpleaños (opcional)</label>
                            <input id="fecha_cumpleanos" name="fecha_cumpleanos" type="date" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white [&>option]:bg-[#1a3d6b] py-2.5 px-3" value="{{ old('fecha_cumpleanos') }}" />
                            <p class="mt-1 text-xs text-white/60">Para enviar felicitaciones al administrador el día del cumpleaños.</p>
                        </div>
                        <div>
                            <label for="municipio" class="block text-sm font-medium text-white/90 mb-1">Municipio / Ciudad</label>
                            <input id="municipio" name="municipio" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('municipio') }}" />
                        </div>
                        <div>
                            <label for="estado" class="block text-sm font-medium text-white/90 mb-1">Estado</label>
                            <input id="estado" name="estado" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('estado') }}" />
                        </div>
                        <div class="md:col-span-2 mt-2 pt-6 border-t border-white/20">
                            <h3 class="text-lg font-semibold text-[#FFE600] mb-2">Datos para ficha de registro del cliente</h3>
                            <p class="text-sm text-white/80 mb-4">Razón social, nombre comercial, domicilio fiscal, RFC y régimen. TEL se toma de Teléfono/Celular de arriba.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label for="razon_social" class="block text-sm font-medium text-white/90 mb-1">RAZÓN SOCIAL</label>
                                    <input id="razon_social" name="razon_social" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('razon_social') }}" />
                                </div>
                                <div class="md:col-span-2">
                                    <label for="nombre_comercial" class="block text-sm font-medium text-white/90 mb-1">Nombre comercial</label>
                                    <input id="nombre_comercial" name="nombre_comercial" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('nombre_comercial') }}" />
                                </div>
                                <div class="md:col-span-2">
                                    <label for="calle_numero" class="block text-sm font-medium text-white/90 mb-1">CALLE Y NÚMERO</label>
                                    <input id="calle_numero" name="calle_numero" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('calle_numero') }}" />
                                </div>
                                <div>
                                    <label for="colonia_cp" class="block text-sm font-medium text-white/90 mb-1">COLONIA Y C.P.</label>
                                    <input id="colonia_cp" name="colonia_cp" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('colonia_cp') }}" />
                                </div>
                                <div>
                                    <label for="rfc" class="block text-sm font-medium text-white/90 mb-1">RFC</label>
                                    <input id="rfc" name="rfc" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('rfc') }}" />
                                </div>
                                <div class="md:col-span-2">
                                    <label for="regimen_fiscal" class="block text-sm font-medium text-white/90 mb-1">RÉGIMEN EN QUE TRIBUTA</label>
                                    <input id="regimen_fiscal" name="regimen_fiscal" type="text" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3" value="{{ old('regimen_fiscal') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label for="notas" class="block text-sm font-medium text-white/90 mb-1">Notas</label>
                            <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 py-2.5 px-3">{{ old('notas') }}</textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ route('contacts.index') }}" class="btn-danger-app">Cancelar</a>
                        <button type="submit" id="btn-guardar-contacto" class="btn-amber-app">Guardar</button>
                    </div>
                </form>
            </div>
    </div>

    <x-modal-success id="modal-registro-exitoso" />
    <x-modal-error id="modal-error" title="No se pudo registrar" />

    @if(session('error') || $errors->any())
    <div id="initial-error-message" class="hidden" data-message="{{ e(session('error') ?: 'Por favor corrige los errores del formulario. El contacto no se registró.') }}"></div>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var companiesData = @json($companies->map(fn ($c) => ['id' => $c->id, 'nombre_comercial' => $c->nombre_comercial]));
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
            return companiesData.filter(function(c) {
                return (c.nombre_comercial || '').toLowerCase().indexOf(qq) !== -1;
            });
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
                div.setAttribute('role', 'option');
                div.textContent = c.nombre_comercial;
                div.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    companyIdInput.value = String(c.id);
                    companyInput.value = c.nombre_comercial;
                    companyList.classList.add('hidden');
                });
                companyList.appendChild(div);
            });
            companyList.classList.remove('hidden');
        }

        if (companyInput && companyList) {
            companyInput.addEventListener('focus', function() {
                renderCompanyList(filterCompanies(companyInput.value));
            });
            companyInput.addEventListener('input', function() {
                companyIdInput.value = '';
                renderCompanyList(filterCompanies(companyInput.value));
            });
            companyInput.addEventListener('blur', function() {
                setTimeout(function() { companyList.classList.add('hidden'); }, 150);
            });
            document.addEventListener('click', function(e) {
                if (companyList.classList.contains('hidden')) return;
                if (!companyList.contains(e.target) && e.target !== companyInput) {
                    companyList.classList.add('hidden');
                }
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

        window.showErrorModal = showErrorModal;

        var initialError = document.getElementById('initial-error-message');
        if (initialError && initialError.getAttribute('data-message')) {
            showErrorModal(initialError.getAttribute('data-message'));
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (companyIdInput && companyInput && !companyIdInput.value && companyInput.value.trim()) {
                    var match = companiesData.find(function(c) {
                        return (c.nombre_comercial || '').trim().toLowerCase() === companyInput.value.trim().toLowerCase();
                    });
                    if (match) companyIdInput.value = String(match.id);
                }
                var formData = new FormData(form);
                var url = form.getAttribute('action');
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                btnGuardar.disabled = true;
                btnGuardar.innerHTML = 'Guardando...';

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
                    btnGuardar.innerHTML = 'Guardar';

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
                    btnGuardar.innerHTML = 'Guardar';
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
</x-app-user-layout>
