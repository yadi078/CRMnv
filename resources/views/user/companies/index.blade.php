<x-app-user-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">Empresas</h2>
                    <p class="view-header__subtitle">Consultar y agregar empresas (solo aprobadas visibles aquí)</p>
                </div>
            </div>
            @can('companies.create')
            <a href="{{ route('companies.create') }}" class="btn-amber-app">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva Empresa
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(isset($misPendientes) && $misPendientes > 0)
            <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 p-4">
                <p class="text-sm text-amber-800">
                    <strong>{{ $misPendientes }}</strong> empresa(s) en estado <strong>Pendiente</strong> de aprobación. Se reflejarán en el sistema cuando un administrador las apruebe.
                </p>
            </div>
            @endif
            <div class="view-card p-6">
                <p class="text-[#6B7280] text-sm mb-4">Las empresas que agregue quedarán en estado <span class="font-semibold text-amber-700">Pendiente</span> hasta que un administrador las apruebe. Estatus con colores tipo semáforo.</p>
                <form method="GET" action="{{ route('companies.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, RFC o ejecutivo..." class="rounded-xl border-gray-300 shadow-sm focus:border-amarillo focus:ring-2 focus:ring-amber-400/20 py-2">
                    <select name="status_color" class="rounded-xl border-gray-300 shadow-sm focus:border-amarillo focus:ring-2 focus:ring-amber-400/20 py-2">
                        <option value="">Todos los colores</option>
                        <option value="verde" {{ request('status_color') === 'verde' ? 'selected' : '' }}>Verde</option>
                        <option value="amarillo" {{ request('status_color') === 'amarillo' ? 'selected' : '' }}>Amarillo</option>
                        <option value="rojo" {{ request('status_color') === 'rojo' ? 'selected' : '' }}>Rojo</option>
                    </select>
                    <button type="submit" class="btn-primary-app">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                        </svg>
                        Filtrar
                    </button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-azul-fuerte text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">RFC</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Ejecutivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($companies as $company)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-2 @if($company->status_color === 'verde') bg-green-500 @elseif($company->status_color === 'amarillo') bg-yellow-500 @else bg-red-500 @endif"></div>
                                        <div class="text-sm font-medium text-gray-900">{{ $company->nombre_comercial }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $company->rfc }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded @if($company->status_color === 'verde') bg-green-100 text-green-800 @elseif($company->status_color === 'amarillo') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($company->status_color) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $company->ejecutivo_asignado ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('companies.show', $company) }}" class="text-azul-bright hover:text-azul-fuerte mr-3 btn-icon-text">Ver</a>
                                    @can('companies.edit')
                                    <a href="{{ route('companies.edit', $company) }}" class="text-azul-fuerte hover:text-yellow-600 mr-3 btn-icon-text">Editar</a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay empresas aprobadas para mostrar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $companies->links() }}</div>
            </div>
        </div>
    </div>
</x-app-user-layout>
