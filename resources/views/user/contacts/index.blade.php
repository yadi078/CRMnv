<x-app-user-layout>
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
                    <p class="view-header__subtitle">Directorio de contactos (consulta y captura)</p>
                </div>
            </div>
            @can('contacts.create')
            <a href="{{ route('contacts.create') }}" class="btn-amber-app">Nuevo Contacto</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="view-card p-6">
                <form method="GET" action="{{ route('contacts.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="rounded-xl border-gray-300 shadow-sm focus:border-amarillo focus:ring-2 focus:ring-amber-400/20 py-2">
                    <select name="company_id" class="rounded-xl border-gray-300 shadow-sm focus:border-amarillo focus:ring-2 focus:ring-amber-400/20 py-2">
                        <option value="">Todas las empresas</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary-app">Filtrar</button>
                </form>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-azul-fuerte text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Correo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($contacts as $contact)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $contact->nombre_completo }}</div>
                                    <div class="text-sm text-gray-500">{{ $contact->puesto_de_trabajo ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->company->nombre_comercial }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $contact->celular ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('contacts.show', $contact) }}" class="text-azul-bright hover:text-azul-fuerte mr-3">Ver</a>
                                    @can('contacts.edit')
                                    <a href="{{ route('contacts.edit', $contact) }}" class="text-azul-fuerte hover:text-yellow-600 mr-3">Editar</a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron contactos</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $contacts->links() }}</div>
            </div>
        </div>
    </div>
</x-app-user-layout>
