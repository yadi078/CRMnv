<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Gestión de Datos</h2>
            <p class="page-header-card__subtitle">Consultar y gestionar empresas y contactos</p>
        </div>
        <div class="flex gap-2 ml-auto">
            <a href="{{ route('companies.index') }}" class="btn-panel-dark">Ver todas las empresas</a>
            <a href="{{ route('contacts.index') }}" class="btn-amber-app">Ver todos los contactos</a>
        </div>
    </x-slot>

    <div x-data="dataManagement()">
    <div class="space-y-8">
        {{-- Resumen y acceso rápido --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('companies.index') }}" class="metric-card-dark cursor-pointer block no-underline">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-card-dark__label">Empresas</p>
                        <p class="metric-card-dark__value">{{ $companies->total() }}</p>
                    </div>
                    <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </a>
            <a href="{{ route('contacts.index') }}" class="metric-card-dark cursor-pointer block no-underline">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-card-dark__label">Contactos</p>
                        <p class="metric-card-dark__value">{{ $contacts->total() }}</p>
                    </div>
                    <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </a>
        </div>

        {{-- Admin: Exportar / Importar --}}
        @if($isAdmin)
        <div class="panel-card-dark">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Exportar o importar datos</h3>
            <div class="flex flex-wrap gap-4">
                <form action="{{ route('data-management.export') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="table" value="companies">
                    <button type="submit" class="btn-primary-app">Exportar empresas (CSV)</button>
                </form>
                <form action="{{ route('data-management.export') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="table" value="contacts">
                    <button type="submit" class="btn-primary-app">Exportar contactos (CSV)</button>
                </form>
                <form action="{{ route('data-management.export') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="table" value="follow_ups">
                    <button type="submit" class="btn-primary-app">Exportar seguimientos (CSV)</button>
                </form>
            </div>
            <p class="text-sm text-white/80 mt-3">Importar: use la misma estructura de columnas que el CSV exportado.</p>
            <form action="{{ route('data-management.import') }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-end gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1">Tabla</label>
                    <select name="table" class="rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3 [&>option]:bg-white [&>option]:text-gray-900">
                        <option value="companies">Empresas</option>
                        <option value="contacts">Contactos</option>
                        <option value="follow_ups">Seguimientos</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1">Archivo CSV</label>
                    <input type="file" name="file" accept=".csv,.txt" required class="rounded-xl border-0 bg-white/15 text-white file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#FFE600] file:text-[#003366] file:font-semibold">
                </div>
                <button type="submit" class="btn-amber-app">Importar</button>
            </form>
        </div>
        @endif

        {{-- Listado reciente: Empresas --}}
        <div class="panel-card-dark overflow-hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-0">Últimas empresas</h3>
                <a href="{{ route('companies.index') }}" class="text-sm font-medium text-[#FFE600] hover:text-white">Ver todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/20">
                    <thead>
                        <tr class="table-header-panel-dark">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">RFC</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Contactos</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($companies as $company)
                        <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                            <td class="px-4 py-3 text-sm text-white">{{ $company->nombre_comercial }}</td>
                            <td class="px-4 py-3 text-sm text-white/90">{{ $company->rfc ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-white/90">{{ $company->contacts_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('companies.show', $company) }}" class="text-[#FFE600] font-medium hover:text-white">Ver</a>
                                    @can('update', $company)
                                    <button type="button" @click="openEditCompany({{ $company->id }})" class="text-amber-400 font-medium hover:text-white">Editar</button>
                                    @endcan
                                    @can('delete', $company)
                                    <button type="button" @click="confirmDeleteCompany({{ $company->id }}, '{{ addslashes($company->nombre_comercial) }}')" class="text-red-400 font-medium hover:text-white">Eliminar</button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-white/70">No hay empresas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $companies->withQueryString()->links() }}</div>
        </div>

        {{-- Listado reciente: Contactos --}}
        <div class="panel-card-dark overflow-hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-0">Últimos contactos</h3>
                <a href="{{ route('contacts.index') }}" class="text-sm font-medium text-[#FFE600] hover:text-white">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/20">
                    <thead>
                        <tr class="table-header-panel-dark">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Correo electrónico</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Empresa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($contacts as $contact)
                        <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                            <td class="px-4 py-3 text-sm text-white">{{ $contact->nombre_completo }}</td>
                            <td class="px-4 py-3 text-sm text-white/90">{{ $contact->email }}</td>
                            <td class="px-4 py-3 text-sm text-white/90">{{ $contact->company?->nombre_comercial ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('contacts.show', $contact) }}" class="text-[#FFE600] font-medium hover:text-white">Ver</a>
                                    @can('update', $contact)
                                    <button type="button" @click="openEditContact({{ $contact->id }})" class="text-amber-400 font-medium hover:text-white">Editar</button>
                                    @endcan
                                    @can('delete', $contact)
                                    <button type="button" @click="confirmDeleteContact({{ $contact->id }}, '{{ addslashes($contact->nombre_completo) }}')" class="text-red-400 font-medium hover:text-white">Eliminar</button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-white/70">No hay contactos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $contacts->withQueryString()->links() }}</div>
        </div>

        {{-- Modal Editar Contacto --}}
    <div x-show="modalContactOpen" x-cloak
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         @keydown.escape.window="modalContactOpen = false">
        <div x-show="modalContactOpen" @click.outside="modalContactOpen = false"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="w-full max-w-2xl max-h-[90vh] overflow-y-auto panel-card-dark p-6 rounded-2xl shadow-xl">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Editar contacto</h3>
            <form @submit.prevent="submitContact()" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-white/90 mb-1">Nombre completo *</label>
                        <input type="text" x-model="formContact.nombre_completo" required
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Email *</label>
                        <input type="email" x-model="formContact.email" required
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Puesto</label>
                        <input type="text" x-model="formContact.puesto_de_trabajo"
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Departamento</label>
                        <input type="text" x-model="formContact.departamento"
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Celular</label>
                        <input type="text" x-model="formContact.celular"
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Extensión</label>
                        <input type="text" x-model="formContact.extension"
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Municipio</label>
                        <input type="text" x-model="formContact.municipio"
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Estado</label>
                        <input type="text" x-model="formContact.estado"
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-white/90 mb-1">Notas</label>
                        <textarea x-model="formContact.notas" rows="3"
                                  class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="modalContactOpen = false"
                            class="btn-panel-dark">Cancelar</button>
                    <button type="submit" :disabled="savingContact"
                            class="btn-amber-app disabled:opacity-50">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Editar Empresa --}}
    <div x-show="modalCompanyOpen" x-cloak
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         @keydown.escape.window="modalCompanyOpen = false">
        <div x-show="modalCompanyOpen" @click.outside="modalCompanyOpen = false"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="w-full max-w-2xl max-h-[90vh] overflow-y-auto panel-card-dark p-6 rounded-2xl shadow-xl">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Editar empresa</h3>
            <form @submit.prevent="submitCompany()" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Nombre comercial *</label>
                        <input type="text" x-model="formCompany.nombre_comercial" required
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">RFC</label>
                        <input type="text" x-model="formCompany.rfc" maxlength="13"
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3 uppercase">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-white/90 mb-1">Sector</label>
                        <input type="text" x-model="formCompany.sector"
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Municipio</label>
                        <input type="text" x-model="formCompany.municipio"
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Estado</label>
                        <input type="text" x-model="formCompany.estado"
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Ejecutivo asignado</label>
                        <input type="text" x-model="formCompany.ejecutivo_asignado"
                               class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Estado prospecto</label>
                        <select x-model="formCompany.status_color"
                                class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3 [&>option]:bg-white [&>option]:text-gray-900">
                            @foreach(\App\Models\Company::PROSPECT_STATUS_LABELS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-white/90 mb-1">Datos fiscales</label>
                        <textarea x-model="formCompany.datos_fiscales" rows="3"
                                  class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="modalCompanyOpen = false"
                            class="btn-panel-dark">Cancelar</button>
                    <button type="submit" :disabled="savingCompany"
                            class="btn-amber-app disabled:opacity-50">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    </div>
</x-app-layout>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dataManagement', () => ({
        modalContactOpen: false,
        modalCompanyOpen: false,
        savingContact: false,
        savingCompany: false,
        editingContactId: null,
        editingCompanyId: null,
        formContact: {
            nombre_completo: '', email: '', puesto_de_trabajo: '', departamento: '',
            celular: '', extension: '', municipio: '', estado: '', notas: ''
        },
        formCompany: {
            nombre_comercial: '', rfc: '', sector: '', municipio: '', estado: '',
            ejecutivo_asignado: '', datos_fiscales: '', status_color: 'seguimiento'
        },
        contactUrls: {
            get: @json(route('data-management.contacts.show', ['contact' => '==ID=='])),
            update: @json(route('data-management.contacts.update', ['contact' => '==ID=='])),
            destroy: @json(route('data-management.contacts.destroy', ['contact' => '==ID==']))
        },
        companyUrls: {
            get: @json(route('data-management.companies.show', ['company' => '==ID=='])),
            update: @json(route('data-management.companies.update', ['company' => '==ID=='])),
            destroy: @json(route('data-management.companies.destroy', ['company' => '==ID==']))
        },
        async openEditContact(id) {
            this.editingContactId = id;
            this.modalContactOpen = true;
            try {
                const r = await fetch(this.contactUrls.get.replace('==ID==', id), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!r.ok) throw new Error('Error al cargar');
                const data = await r.json();
                this.formContact = {
                    nombre_completo: data.nombre_completo || '',
                    email: data.email || '',
                    puesto_de_trabajo: data.puesto_de_trabajo || '',
                    departamento: data.departamento || '',
                    celular: data.celular || '',
                    extension: data.extension || '',
                    municipio: data.municipio || '',
                    estado: data.estado || '',
                    notas: data.notas || ''
                };
            } catch (e) {
                if (typeof showAlert === 'function') showAlert('error', e.message || 'No se pudo cargar el contacto.');
                this.modalContactOpen = false;
            }
        },
        async submitContact() {
            if (!this.editingContactId) return;
            this.savingContact = true;
            try {
                const r = await fetch(this.contactUrls.update.replace('==ID==', this.editingContactId), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.formContact)
                });
                const data = await r.json();
                if (!r.ok) throw new Error(data.message || 'Error al guardar');
                if (typeof showAlert === 'function') showAlert('success', 'Contacto actualizado correctamente.');
                this.modalContactOpen = false;
                window.location.reload();
            } catch (e) {
                if (typeof showAlert === 'function') showAlert('error', e.message || 'Error al guardar.');
            } finally {
                this.savingContact = false;
            }
        },
        async confirmDeleteContact(id, name) {
            if (!confirm('¿Eliminar el contacto "' + name + '"? Esta acción no se puede deshacer.')) return;
            try {
                const r = await fetch(this.contactUrls.destroy.replace('==ID==', id), {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!r.ok) {
                    const d = await r.json();
                    throw new Error(d.message || 'Error al eliminar');
                }
                if (typeof showAlert === 'function') showAlert('success', 'Contacto eliminado.');
                window.location.reload();
            } catch (e) {
                if (typeof showAlert === 'function') showAlert('error', e.message || 'Error al eliminar.');
            }
        },
        async openEditCompany(id) {
            this.editingCompanyId = id;
            this.modalCompanyOpen = true;
            try {
                const r = await fetch(this.companyUrls.get.replace('==ID==', id), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!r.ok) throw new Error('Error al cargar');
                const data = await r.json();
                this.formCompany = {
                    nombre_comercial: data.nombre_comercial || '',
                    rfc: data.rfc || '',
                    sector: data.sector || '',
                    municipio: data.municipio || '',
                    estado: data.estado || '',
                    ejecutivo_asignado: data.ejecutivo_asignado || '',
                    datos_fiscales: data.datos_fiscales || '',
                    status_color: data.status_color || 'seguimiento'
                };
            } catch (e) {
                if (typeof showAlert === 'function') showAlert('error', e.message || 'No se pudo cargar la empresa.');
                this.modalCompanyOpen = false;
            }
        },
        async submitCompany() {
            if (!this.editingCompanyId) return;
            this.savingCompany = true;
            try {
                const r = await fetch(this.companyUrls.update.replace('==ID==', this.editingCompanyId), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.formCompany)
                });
                const data = await r.json();
                if (!r.ok) throw new Error(data.message || 'Error al guardar');
                if (typeof showAlert === 'function') showAlert('success', 'Empresa actualizada correctamente.');
                this.modalCompanyOpen = false;
                window.location.reload();
            } catch (e) {
                if (typeof showAlert === 'function') showAlert('error', e.message || 'Error al guardar.');
            } finally {
                this.savingCompany = false;
            }
        },
        async confirmDeleteCompany(id, name) {
            if (!confirm('¿Eliminar la empresa "' + name + '" y sus contactos asociados? Esta acción no se puede deshacer.')) return;
            try {
                const r = await fetch(this.companyUrls.destroy.replace('==ID==', id), {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!r.ok) {
                    const d = await r.json();
                    throw new Error(d.message || 'Error al eliminar');
                }
                if (typeof showAlert === 'function') showAlert('success', 'Empresa eliminada.');
                window.location.reload();
            } catch (e) {
                if (typeof showAlert === 'function') showAlert('error', e.message || 'Error al eliminar.');
            }
        }
    }));
});
</script>
@endpush
