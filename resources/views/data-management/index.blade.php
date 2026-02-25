<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">Gestión de Datos</h2>
                    <p class="view-header__subtitle">Consultar y gestionar empresas y contactos</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('companies.index') }}" class="btn-primary-app">Ver todas las empresas</a>
                <a href="{{ route('contacts.index') }}" class="btn-amber-app">Ver todos los contactos</a>
            </div>
        </div>
    </x-slot>

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
        <div class="view-card">
            <h3 class="text-lg font-semibold text-[#1F2937] mb-4">Exportar o importar datos</h3>
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
            <p class="text-sm text-[#6B7280] mt-3">Importar: use la misma estructura de columnas que el CSV exportado.</p>
            <form action="{{ route('data-management.import') }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-end gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-[#374151] mb-1">Tabla</label>
                    <select name="table" class="rounded-xl border-[#E2E8F0] bg-white shadow-sm focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 py-2 px-3 text-[#1F2937]">
                        <option value="companies">Empresas</option>
                        <option value="contacts">Contactos</option>
                        <option value="follow_ups">Seguimientos</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#374151] mb-1">Archivo CSV</label>
                    <input type="file" name="file" accept=".csv,.txt" required class="rounded-xl border-[#E2E8F0] bg-white shadow-sm py-2 px-3 text-[#1F2937]">
                </div>
                <button type="submit" class="btn-amber-app">Importar</button>
            </form>
        </div>
        @endif

        {{-- Listado reciente: Empresas --}}
        <div class="view-card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-[#1F2937]">Últimas empresas</h3>
                <a href="{{ route('companies.index') }}" class="text-sm font-medium text-[#003366] hover:underline">Ver todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="table-header-corporate">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">RFC</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Contactos</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse($companies as $company)
                        <tr class="hover:bg-fondo transition-colors">
                            <td class="px-4 py-3 text-sm text-[#1F2937]">{{ $company->nombre_comercial }}</td>
                            <td class="px-4 py-3 text-sm text-[#6B7280]">{{ $company->rfc ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-[#6B7280]">{{ $company->contacts_count }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('companies.show', $company) }}" class="text-[#003366] font-medium hover:underline">Ver</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-[#6B7280]">No hay empresas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $companies->withQueryString()->links() }}</div>
        </div>

        {{-- Listado reciente: Contactos --}}
        <div class="view-card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-[#1F2937]">Últimos contactos</h3>
                <a href="{{ route('contacts.index') }}" class="text-sm font-medium text-[#003366] hover:underline">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="table-header-corporate">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Correo electrónico</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Empresa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse($contacts as $contact)
                        <tr class="hover:bg-fondo transition-colors">
                            <td class="px-4 py-3 text-sm text-[#1F2937]">{{ $contact->nombre_completo }}</td>
                            <td class="px-4 py-3 text-sm text-[#6B7280]">{{ $contact->email }}</td>
                            <td class="px-4 py-3 text-sm text-[#6B7280]">{{ $contact->company?->nombre_comercial ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('contacts.show', $contact) }}" class="text-[#003366] font-medium hover:underline">Ver</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-[#6B7280]">No hay contactos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $contacts->withQueryString()->links() }}</div>
        </div>
    </div>
</x-app-layout>
