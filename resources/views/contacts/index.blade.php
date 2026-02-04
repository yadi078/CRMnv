<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-azul-fuerte leading-tight">
                Contactos
            </h2>
            @can('contacts.create')
            <a href="{{ route('contacts.create') }}" class="bg-amarillo text-azul-fuerte px-4 py-2 rounded-md font-semibold hover:bg-yellow-400 transition">
                Nuevo Contacto
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Filtros -->
                <form method="GET" action="{{ route('contacts.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="rounded-md border-gray-300">
                    <select name="company_id" class="rounded-md border-gray-300">
                        <option value="">Todas las empresas</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-azul-fuerte text-white px-4 py-2 rounded-md hover:bg-azul transition">Filtrar</button>
                </form>

                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-azul-fuerte text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Email</th>
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
                                    @can('contacts.generate-pdf')
                                    <a href="{{ route('contacts.pdf', $contact) }}" class="text-red-600 hover:text-red-800 mr-3">PDF</a>
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

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $contacts->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
