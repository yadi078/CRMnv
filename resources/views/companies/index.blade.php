<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-azul-fuerte leading-tight">
                Empresas
            </h2>
            @can('companies.create')
            <a href="{{ route('companies.create') }}" class="bg-amarillo text-azul-fuerte px-4 py-2 rounded-md font-semibold hover:bg-yellow-400 transition">
                Nueva Empresa
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Filtros -->
                <form method="GET" action="{{ route('companies.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="rounded-md border-gray-300">
                    <select name="status_color" class="rounded-md border-gray-300">
                        <option value="">Todos los colores</option>
                        <option value="verde" {{ request('status_color') === 'verde' ? 'selected' : '' }}>Verde</option>
                        <option value="amarillo" {{ request('status_color') === 'amarillo' ? 'selected' : '' }}>Amarillo</option>
                        <option value="rojo" {{ request('status_color') === 'rojo' ? 'selected' : '' }}>Rojo</option>
                    </select>
                    @can('companies.approve')
                    <select name="approval_status" class="rounded-md border-gray-300">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('approval_status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobado" {{ request('approval_status') === 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                    </select>
                    @endcan
                    <button type="submit" class="bg-azul-fuerte text-white px-4 py-2 rounded-md hover:bg-azul transition">Filtrar</button>
                </form>

                <!-- Tabla -->
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
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $company->nombre_comercial }}</div>
                                            @if($company->approval_status === 'pendiente')
                                            <span class="text-xs text-yellow-600 bg-yellow-100 px-2 py-1 rounded">Pendiente</span>
                                            @endif
                                        </div>
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
                                    <a href="{{ route('companies.show', $company) }}" class="text-azul-bright hover:text-azul-fuerte mr-3">Ver</a>
                                    @can('companies.edit')
                                    <a href="{{ route('companies.edit', $company) }}" class="text-amarillo hover:text-yellow-600 mr-3">Editar</a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron empresas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $companies->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
