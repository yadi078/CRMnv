<x-app-user-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Contactos</h2>
            <p class="page-header-card__subtitle">Directorio de contactos (consulta y captura)</p>
        </div>
        @can('contacts.create')
        <a href="{{ route('contacts.create') }}" class="btn-amber-app ml-auto">Nuevo Contacto</a>
        @endcan
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark overflow-hidden p-6">
            <form method="GET" action="{{ route('contacts.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3">
                <select name="company_id" class="rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-[#1a3d6b] [&>option]:text-white">
                        <option value="">Todas las empresas</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary-app">Filtrar</button>
                </form>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/20">
                    <thead>
                        <tr class="table-header-panel-dark">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Empresa</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Correo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Acciones</th>
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
                                <a href="{{ route('contacts.show', $contact) }}" class="text-[#FFE600] hover:text-white mr-3">Ver</a>
                                @can('contacts.edit')
                                <a href="{{ route('contacts.edit', $contact) }}" class="text-[#FFE600] hover:text-white mr-3">Editar</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-white/80">No se encontraron contactos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">{{ $contacts->links() }}</div>
        </div>
    </div>
</x-app-user-layout>
