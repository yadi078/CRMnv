<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg></x-page-header-avatar>
            <div>
                <h2 class="page-header-card__title">Editar Contacto</h2>
            <p class="page-header-card__subtitle">{{ $contact->nombre_completo }}</p>
            </div>
        <div class="flex flex-wrap gap-2 ml-auto justify-end items-center shrink-0">
            @if(isset($sale) && $sale && $contact->fichaPdfCompleta())
                @can('view', $sale)
                    <a href="{{ route('user.sales.ficha-pdf', $sale) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/15 text-white border border-white/30 hover:bg-white/25 text-sm font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Generar PDF
                    </a>
                @endcan
            @endif
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="panel-card-dark p-6">
                @php
                    $crmCancelHref = (($r = request('return')) && is_string($r) && \App\Support\CrmNavigation::isSafeReturnUrl($r))
                        ? $r
                        : route('contacts.show', $contact);
                    $selectedCompanyId = old('company_id', $contact->company_id ?? $contact->company?->id);
                @endphp
                <form id="form-editar-contacto" method="POST" action="{{ route('contacts.update', $contact) }}">
                    @csrf
                    @method('PUT')
                    @if(($crmNavReturn = request('return')) && is_string($crmNavReturn) && \App\Support\CrmNavigation::isSafeReturnUrl($crmNavReturn))
                        <input type="hidden" name="return" value="{{ $crmNavReturn }}">
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <x-input-label for="company_id" value="Empresa *" class="mb-0" />
                                <x-copy-field-button target-id="company_id" />
                            </div>
                            <p class="text-xs text-white/65 mb-2">Despliegue la lista y elija la empresa. Incluye las empresas aprobadas; la empresa vinculada a este contacto siempre aparece aunque esté pendiente de aprobación.</p>
                            {{-- Fondo claro + texto oscuro: en Windows el <select> nativo a veces no muestra texto claro sobre fondo oscuro al estar cerrado. --}}
                            <select
                                id="company_id"
                                name="company_id"
                                class="mt-1 block w-full rounded-xl border border-white/20 bg-white text-gray-900 py-2.5 px-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#FFE600]/70 [&>option]:bg-white [&>option]:text-gray-900"
                                required
                            >
                                <option value="">Seleccione una empresa</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ (string) $selectedCompanyId === (string) $company->id ? 'selected' : '' }}>
                                        {{ $company->nombre_comercial }}
                                    </option>
                                @endforeach
                            </select>
                            @if($companies->isEmpty())
                                <p class="mt-2 text-sm text-amber-200/90">No hay empresas aprobadas en el catálogo. Solicite al administrador una alta de empresa.</p>
                            @endif
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2 text-red-300" />
                        </div>
                        <div class="md:col-span-2">
                            <x-executive-assignment-field
                                :executiveUsers="$executiveUsers"
                                :isAdmin="$isAdmin"
                                :selectedAssignedUserId="$selectedAssignedUserId"
                                :readonlyExecutiveName="$readonlyExecutiveName"
                                inputId="contact_ejecutivo"
                                selectClass="mt-1 block w-full rounded-xl border border-white/20 bg-white text-gray-900 py-2.5 px-3 [&>option]:bg-white [&>option]:text-gray-900"
                                readonlyClass="mt-1 block w-full rounded-xl border border-white/20 bg-white/15 text-white py-2.5 px-3"
                            />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="nombre_completo" value="Nombre Completo *" />
                            <x-text-input id="nombre_completo" name="nombre_completo" type="text" class="mt-1 block w-full" :value="old('nombre_completo', $contact->nombre_completo)" minlength="4" maxlength="255" required />
                            <x-input-error :messages="$errors->get('nombre_completo')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="genero" value="Género" />
                            <select id="genero" name="genero" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Seleccione</option>
                                <option value="Masculino" {{ old('genero', $contact->genero) === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="Femenino" {{ old('genero', $contact->genero) === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                <option value="Otro" {{ old('genero', $contact->genero) === 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="puesto_de_trabajo" value="Puesto de Trabajo" />
                            <x-text-input id="puesto_de_trabajo" name="puesto_de_trabajo" type="text" class="mt-1 block w-full" :value="old('puesto_de_trabajo', $contact->puesto_de_trabajo)" />
                        </div>
                        <div>
                            <x-input-label for="departamento" value="Area de trabajo" />
                            <input id="departamento" name="departamento" type="text" list="work-areas-list" class="mt-1 block w-full rounded-md border-gray-300" value="{{ old('departamento', $contact->departamento) }}" placeholder="Escriba para buscar..." />
                            <datalist id="work-areas-list">
                                @foreach($workAreas as $workArea)
                                    <option value="{{ $workArea }}"></option>
                                @endforeach
                            </datalist>
                            <p class="mt-1 text-xs text-white/60">Puede escribir el área o elegir una sugerencia del listado.</p>
                        </div>
                        <div>
                            <x-input-label for="email" value="Correo electrónico *" />
                            <x-text-input id="email" name="email" type="text" inputmode="email" autocomplete="email" placeholder="correo@empresa.com, otro@empresa.com" class="mt-1 block w-full" :value="old('email', $contact->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3 md:col-span-2">
                            <input id="email_activo" name="email_activo" type="checkbox" value="1" class="rounded border-gray-300 text-amber-500 shadow-sm focus:border-amber-500 focus:ring-amber-500" @checked(old('email_activo', $contact->email_activo ?? true)) />
                            <label for="email_activo" class="text-sm text-white/90 select-none">
                                Mostrar correo en fichas, listados y PDF
                            </label>
                        </div>
                        <div>
                            <x-input-label for="telefono" value="Teléfono" />
                            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono', $contact->telefono)" placeholder="Teléfono fijo" />
                        </div>
                        <div>
                            <x-input-label for="celular" value="Celular" />
                            <x-text-input id="celular" name="celular" type="text" class="mt-1 block w-full" :value="old('celular', $contact->celular)" />
                        </div>
                        <div>
                            <x-input-label for="extension" value="Extensión" />
                            <x-text-input id="extension" name="extension" type="text" class="mt-1 block w-full" :value="old('extension', $contact->extension)" />
                        </div>
                        <div>
                            <x-input-label for="fecha_cumpleanos" value="Fecha de cumpleaños (opcional)" />
                            <x-text-input id="fecha_cumpleanos" name="fecha_cumpleanos" type="date" class="mt-1 block w-full text-gray-900" :value="old('fecha_cumpleanos', $contact->fecha_cumpleanos?->format('Y-m-d'))" />
                            <p class="mt-1 text-xs text-white/60">Para enviar felicitaciones al administrador el día del cumpleaños.</p>
                        </div>
                        <div>
                            <x-input-label for="municipio" value="Municipio / Ciudad" />
                            <x-text-input id="municipio" name="municipio" type="text" class="mt-1 block w-full" :value="old('municipio', $contact->municipio)" />
                        </div>
                        <div>
                            <x-input-label for="estado" value="Entidad federativa" />
                            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $contact->estado)" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="notas" value="Notas" />
                            <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('notas', $contact->notas) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="status_color" value="Estatus de prospecto" />
                            <select id="status_color" name="status_color" class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 py-2 px-3 [&>option]:bg-white [&>option]:text-gray-900">
                                @foreach(\App\Models\Contact::PROSPECT_STATUS_LABELS as $value => $label)
                                <option value="{{ $value }}" {{ old('status_color', $contact->status_color ?? 'seguimiento') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status_color')" class="mt-2" />
                        </div>

                    </div>
                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ $crmCancelHref }}" class="btn-icon-text text-gray-600 hover:text-gray-800 px-4 py-2 rounded-xl border border-gray-300 hover:bg-gray-50">Cancelar</a>
                        <button type="submit" class="btn-amber-app">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var companiesData = @json($companies->map(fn ($c) => ['id' => $c->id, 'nombre_comercial' => $c->nombre_comercial]));
        var form = document.getElementById('form-editar-contacto');
        var companyInput = document.getElementById('company_autocomplete');
        var companyIdInput = document.getElementById('company_id');
        var companyList = document.getElementById('company_autocomplete_list');

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

        if (form) {
            form.addEventListener('submit', function(e) {
                if (companyIdInput && companyInput && !companyIdInput.value && companyInput.value.trim()) {
                    var match = companiesData.find(function(c) {
                        return (c.nombre_comercial || '').trim().toLowerCase() === companyInput.value.trim().toLowerCase();
                    });
                    if (match) companyIdInput.value = String(match.id);
                }
            });
        }
    });
    </script>
</x-app-user-layout>
