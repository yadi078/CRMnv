<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
            </svg></x-page-header-avatar>
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
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4 text-center">Gestión de exportación e importación</h3>

            <h4 class="text-lg font-bold text-[#FFE600] text-center tracking-wide mb-3">EXPORTAR</h4>
            <div class="flex flex-wrap justify-center gap-4 mb-8">
                <form action="{{ route('data-management.export') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="table" value="companies">
                    <button type="submit" class="btn-amber-app flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 12l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Exportar empresas (CSV)</span>
                    </button>
                </form>
                <form action="{{ route('data-management.export') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="table" value="contacts">
                    <button type="submit" class="btn-amber-app flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 12l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Exportar contactos (CSV)</span>
                    </button>
                </form>
                <form action="{{ route('data-management.export') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="table" value="follow_ups">
                    <button type="submit" class="btn-amber-app flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 12l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Exportar seguimientos (CSV)</span>
                    </button>
                </form>
            </div>

            <h4 class="text-lg font-bold text-[#FFE600] text-center tracking-wide mt-2 mb-3">IMPORTAR</h4>
            <form action="{{ route('companies.import') }}" method="POST" enctype="multipart/form-data" class="mt-6 flex flex-wrap items-end justify-center gap-6" x-data="{ fileName: 'Ningún archivo seleccionado' }">
                @csrf
                <div class="flex flex-col items-center">
                    <label class="block text-base font-semibold text-white/90 mb-2">Archivo Excel</label>
                    <div class="flex flex-wrap items-center gap-3">
                        <label for="file_excel_dm" class="btn-amber-app cursor-pointer flex items-center gap-2 font-extrabold hover:text-[#003366]" style="color:#003366 !important;">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h7l5 5v11H4a2 2 0 01-2-2V6a2 2 0 012-2zm8 5v6m0 0l-3-3m3 3l3-3" />
                            </svg>
                            <span>Seleccionar archivo</span>
                        </label>
                        <span class="text-sm text-white min-w-[260px]" x-text="fileName"></span>
                    </div>
                    <input id="file_excel_dm" type="file" name="file" accept=".xlsx,.xls,.csv" required class="hidden" @change="fileName = $event.target.files[0]?.name || 'Ningún archivo seleccionado'">
                </div>
                <button type="submit" class="btn-amber-app flex items-center gap-2 text-[#003366] font-semibold">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M8 12l4-4m0 0l4 4m-4-4v12" />
                    </svg>
                    <span>Importar base Excel</span>
                </button>
            </form>
        </div>
        @endif

        {{-- Listado reciente: Empresas --}}
        <div class="panel-card-dark overflow-hidden">
            <div class="relative mb-8">
                <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-0 text-center text-2xl font-extrabold tracking-wide text-[#FFE600] drop-shadow-md">
                    Últimas empresas
                </h3>
                <a href="{{ route('companies.index') }}" class="btn-amber-app absolute right-0 top-1/2 -translate-y-1/2 text-sm font-semibold px-4 py-2">
                    Ver todas
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 rounded-2xl overflow-hidden bg-gradient-to-b from-[#06244a] via-[#082d5d] to-[#0a356e]">
                    <thead>
                        <tr class="bg-[#FFE600]">
                            <th class="px-7 py-4 text-left text-sm font-semibold uppercase tracking-wide text-[#003366]">Nombre</th>
                            <th class="px-7 py-4 text-left text-sm font-semibold uppercase tracking-wide text-[#003366]">RFC</th>
                            <th class="px-7 py-4 text-left text-sm font-semibold uppercase tracking-wide text-[#003366]">Contactos</th>
                            <th class="px-7 py-4 text-center text-sm font-semibold uppercase tracking-wide text-[#003366]">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($companies as $company)
                        <tr class="panel-card-dark__row odd:bg-white/5 even:bg-white/0 hover:bg-white/10 transition-colors">
                            <td class="px-7 py-5 text-base text-white">{{ $company->nombre_comercial }}</td>
                            <td class="px-7 py-5 text-base text-white/90">{{ $company->rfc ?? '-' }}</td>
                            <td class="px-7 py-5 text-base text-white/90">{{ $company->contacts_count }}</td>
                            <td class="px-7 py-5">
                                <div class="flex flex-wrap items-center gap-6">
                                    <a href="{{ route('companies.show', $company) }}" class="inline-flex items-center gap-1 text-[#FFE600] font-medium hover:text-white">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Ver</span>
                                    </a>
                                    @if($isAdmin)
                                        @can('update', $company)
                                        <button type="button" @click="openEditCompany({{ $company->id }})" class="inline-flex items-center gap-1 text-green-400 font-medium hover:text-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l9.232-9.232a2 2 0 000-2.828l-2.172-2.172a2 2 0 00-2.828 0L4 16v4z" />
                                            </svg>
                                            <span>Editar</span>
                                        </button>
                                        @endcan
                                        @can('delete', $company)
                                        <button type="button" @click="confirmDeleteCompany({{ $company->id }}, '{{ addslashes($company->nombre_comercial) }}')" class="inline-flex items-center gap-1 text-red-400 font-medium hover:text-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 5V4a1 1 0 011-1h4a1 1 0 011 1v1m-9 0h10" />
                                            </svg>
                                            <span>Eliminar</span>
                                        </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-white/70">No hay empresas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-2 px-1 border-t border-white/15">{{ $companies->withQueryString()->links() }}</div>
        </div>

        {{-- Listado reciente: Contactos --}}
        <div class="panel-card-dark overflow-hidden mt-10">
            <div class="relative mb-8">
                <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-0 text-center text-2xl font-extrabold tracking-wide text-[#FFE600] drop-shadow-md">
                    Últimos contactos
                </h3>
                <a href="{{ route('contacts.index') }}" class="btn-amber-app absolute right-0 top-1/2 -translate-y-1/2 text-sm font-semibold px-4 py-2">
                    Ver todos
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 rounded-2xl overflow-hidden bg-gradient-to-b from-[#06244a] via-[#082d5d] to-[#0a356e]">
                    <thead>
                        <tr class="bg-[#FFE600]">
                            <th class="px-7 py-4 text-left text-sm font-semibold uppercase tracking-wide text-[#003366]">Nombre</th>
                            <th class="px-7 py-4 text-left text-sm font-semibold uppercase tracking-wide text-[#003366]">Correo electrónico</th>
                            <th class="px-7 py-4 text-left text-sm font-semibold uppercase tracking-wide text-[#003366]">Empresa</th>
                            <th class="px-7 py-4 text-center text-sm font-semibold uppercase tracking-wide text-[#003366]">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($contacts as $contact)
                        <tr class="panel-card-dark__row odd:bg-white/5 even:bg-white/0 hover:bg-white/10 transition-colors">
                            <td class="px-7 py-5 text-base text-white">{{ $contact->nombre_completo }}</td>
                            <td class="px-7 py-5 text-base text-white/90">{{ $contact->email }}</td>
                            <td class="px-7 py-5 text-base text-white/90">{{ $contact->company?->nombre_comercial ?? '—' }}</td>
                            <td class="px-7 py-5">
                                <div class="flex flex-wrap items-center gap-6">
                                    <a href="{{ route('contacts.show', $contact) }}" class="inline-flex items-center gap-1 text-[#FFE600] font-medium hover:text-white">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Ver</span>
                                    </a>
                                    @if($isAdmin)
                                        @can('update', $contact)
                                        <button type="button" @click="openEditContact({{ $contact->id }})" class="inline-flex items-center gap-1 text-green-400 font-medium hover:text-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l9.232-9.232a2 2 0 000-2.828l-2.172-2.172a2 2 0 00-2.828 0L4 16v4z" />
                                            </svg>
                                            <span>Editar</span>
                                        </button>
                                        @endcan
                                        @can('delete', $contact)
                                        <button type="button" @click="confirmDeleteContact({{ $contact->id }}, '{{ addslashes($contact->nombre_completo) }}')" class="inline-flex items-center gap-1 text-red-400 font-medium hover:text-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 5V4a1 1 0 011-1h4a1 1 0 011 1v1m-9 0h10" />
                                            </svg>
                                            <span>Eliminar</span>
                                        </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-white/70">No hay contactos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-2 px-1 border-t border-white/15">{{ $contacts->withQueryString()->links() }}</div>
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
