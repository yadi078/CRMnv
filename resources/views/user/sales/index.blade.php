<x-app-user-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Historial de Ventas</h2>
            <p class="page-header-card__subtitle">Cursos y servicios vendidos por empresa</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark overflow-hidden p-6">
            <form method="GET" action="{{ route('user.sales.index') }}" class="mb-6 flex flex-wrap items-end gap-3 sm:gap-4">
                <div class="min-w-[140px] max-w-[220px] flex-1 sm:flex-initial">
                    <label for="search" class="block text-sm font-medium text-white/90 mb-1">Buscar</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Servicio o empresa..." class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3">
                </div>
                <div class="min-w-[140px] flex-1 sm:flex-initial sm:max-w-[180px]">
                    <label for="filtro_fecha" class="block text-sm font-medium text-white/90 mb-1">Filtrar por fecha</label>
                    <select id="filtro_fecha" name="filtro_fecha" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-[#1a3d6b] [&>option]:text-white">
                        <option value="todos" {{ request('filtro_fecha', 'todos') === 'todos' ? 'selected' : '' }}>Todos</option>
                        <option value="7" {{ request('filtro_fecha') === '7' ? 'selected' : '' }}>Últimos 7 días</option>
                        <option value="14" {{ request('filtro_fecha') === '14' ? 'selected' : '' }}>Últimos 14 días</option>
                        <option value="30" {{ request('filtro_fecha') === '30' ? 'selected' : '' }}>Último mes</option>
                    </select>
                </div>
                <div class="min-w-[140px] flex-1 sm:flex-initial sm:max-w-[200px]">
                    <label for="company_id" class="block text-sm font-medium text-white/90 mb-1">Empresa</label>
                    <select id="company_id" name="company_id" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-[#1a3d6b] [&>option]:text-white">
                        <option value="">Todas las empresas</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre_comercial }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-panel-dark flex items-center justify-center gap-2">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    Filtrar
                </button>
                @can('sales.create')
                <a href="{{ route('user.sales.create') }}" class="btn-amber-app flex items-center justify-center gap-2 flex-shrink-0">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Nueva Venta
                </a>
                @endcan
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/20">
                    <thead>
                        <tr class="table-header-panel-dark">
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Servicio / Curso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Participantes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($sales as $sale)
                        <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-white">{{ $sale->nombre_servicio }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('companies.show', $sale->company) }}" class="text-[#FFE600] hover:text-white text-sm font-medium">{{ $sale->company->nombre_comercial }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $sale->fecha_venta->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $sale->monto_formateado }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $sale->participantes ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('user.sales.show', $sale) }}" class="text-[#FFE600] hover:text-white mr-3">Ver</a>
                                @can('sales.edit')
                                <a href="{{ route('user.sales.edit', $sale) }}" class="text-[#FFE600] hover:text-white mr-3">Editar</a>
                                    @endcan
                                @can('sales.delete')
                                <form action="{{ route('user.sales.destroy', $sale) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este registro de venta?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300">Eliminar</button>
                                </form>
                                @endcan
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-white/80">
                                No hay ventas registradas. 
                                @can('sales.create')
                                <a href="{{ route('user.sales.create') }}" class="text-[#FFE600] hover:text-white underline ml-1">Registrar la primera</a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">{{ $sales->links() }}</div>
        </div>
    </div>
</x-app-user-layout>
