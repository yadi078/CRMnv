<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">Contactos</h2>
                    <p class="view-header__subtitle">Listado de contactos</p>
                </div>
            </div>
            @can('contacts.create')
            <a href="{{ route('contacts.create') }}" class="btn-amber-app">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo Contacto
            </a>
            @endcan
        </div>
    </x-slot>

    <div>
            <div class="view-card">
                <!-- Filtros -->
                <form method="GET" action="{{ route('contacts.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="rounded-xl border-[#E2E8F0] bg-white shadow-sm focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 py-2.5 px-3 text-[#1F2937] placeholder-[#6B7280]">
                    <select name="company_id" class="rounded-xl border-[#E2E8F0] bg-white shadow-sm focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 py-2.5 px-3 text-[#1F2937]">
                        <option value="">Todas las empresas</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary-app">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                        </svg>
                        Filtrar
                    </button>
                </form>

                <!-- Tabla -->
                <div class="overflow-x-auto -mx-6 -mb-6">
                    <table class="min-w-full divide-y divide-[#E2E8F0]">
                        <thead class="table-header-corporate">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Correo</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#E2E8F0]">
                            @forelse($contacts as $contact)
                            <tr class="hover:bg-fondo transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-[#1F2937]">{{ $contact->nombre_completo }}</div>
                                    <div class="text-sm text-[#6B7280]">{{ $contact->puesto_de_trabajo ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#6B7280]">{{ $contact->company->nombre_comercial }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#6B7280]">{{ $contact->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#6B7280]">{{ $contact->celular ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('contacts.show', $contact) }}" class="text-[#003366] hover:text-[#000836] mr-3 btn-icon-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver
                                    </a>
                                    @can('contacts.generate-pdf')
                                    <a href="{{ route('contacts.pdf', $contact) }}" class="text-[#B91C1C] hover:text-[#991B1B] mr-3 btn-icon-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        PDF
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-[#6B7280]">No se encontraron contactos</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-6 pt-4 border-t border-[#E2E8F0]">
                    {{ $contacts->links() }}
                </div>
            </div>
    </div>
</x-app-layout>
