<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Contactos</h2>
            <p class="page-header-card__subtitle">Listado de contactos</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Filtros</h3>
            <form method="GET" action="{{ route('contacts.index') }}" class="flex flex-wrap items-end gap-3 sm:gap-4 mb-0">
                <div class="flex-1 min-w-[180px]">
                    <label for="search" class="block text-sm font-medium text-white/90 mb-1">Buscar</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3">
                </div>
                <div class="min-w-[180px]">
                    <label for="company_id" class="block text-sm font-medium text-white/90 mb-1">Empresa</label>
                    <select id="company_id" name="company_id" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-[#1a3d6b] [&>option]:text-white">
                        <option value="">Todas las empresas</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-panel-dark">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
                    Filtrar
                </button>
                @can('contacts.create')
                <a href="{{ route('contacts.create') }}" class="btn-amber-app flex-shrink-0">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Nuevo Contacto
                </a>
                @endcan
            </form>
        </div>

        <div class="panel-card-dark overflow-hidden">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Listado</h3>
            <div class="overflow-x-auto w-full min-w-0 -mx-4 sm:-mx-0" style="-webkit-overflow-scrolling: touch;">
                <table class="min-w-full divide-y divide-white/20">
                    <thead>
                        <tr class="table-header-panel-dark">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Empresa</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Correo</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($contacts as $contact)
                        <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ $contact->nombre_completo }}</div>
                                <div class="text-sm text-white/80">{{ $contact->puesto_de_trabajo ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $contact->company?->nombre_comercial ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $contact->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $contact->celular ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('contacts.show', $contact) }}" class="text-[#FFE600] hover:text-[#fff] mr-3 inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Ver
                                </a>
                                @can('contacts.generate-pdf')
                                <a href="{{ route('contacts.pdf', $contact) }}" class="text-red-400 hover:text-red-300 mr-3 inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                    PDF
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-white/70">No se encontraron contactos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6 pt-4 border-t border-white/20">
                {{ $contacts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
