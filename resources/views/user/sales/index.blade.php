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
        @can('sales.create')
        <a href="{{ route('user.sales.create') }}" class="btn-amber-app ml-auto">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Nueva Venta
        </a>
        @endcan
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark overflow-hidden p-6">
            <form method="GET" action="{{ route('user.sales.index') }}" class="mb-6 flex flex-wrap items-end gap-3 sm:gap-4">
                <div class="min-w-[140px] flex-1 sm:flex-initial sm:max-w-[180px]">
                    <label for="filtro_fecha" class="block text-sm font-medium text-white/90 mb-1">Fecha</label>
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
                <a href="{{ route('user.sales.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-white/40 text-white/90 text-sm font-medium hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    Limpiar filtros
                </a>
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
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="border-b border-[#5b8fc7]/50">
                            <th class="text-left py-3 px-6 text-[#FFE600] font-semibold text-sm uppercase tracking-wide">EMPRESA</th>
                            <th class="text-left py-3 px-6 text-[#FFE600] font-semibold text-sm uppercase tracking-wide">CONTACTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr class="border-b border-[#5b8fc7]/30 hover:bg-white/5 transition-colors">
                            <td class="py-4 px-6 align-middle">
                                <span class="text-[#FFE600] font-medium">{{ $sale->company->nombre_comercial }}</span>
                            </td>
                            <td class="py-4 px-6 align-middle">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div class="text-white space-y-0.5 min-w-0">
                                        @if($sale->contact)
                                            <div class="font-bold text-white">{{ $sale->contact->nombre_completo }}</div>
                                            @if($sale->contact->puesto_de_trabajo)
                                                <div class="text-white/90 text-sm">{{ $sale->contact->puesto_de_trabajo }}</div>
                                            @endif
                                            <div class="text-white/80 text-sm">{{ $sale->contact->email ?? '—' }}</div>
                                        @else
                                            <span class="text-white/70">—</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-4 flex-shrink-0">
                                        <span class="text-white text-sm">{{ $sale->fecha_venta->format('d/m/Y') }}</span>
                                        <a href="{{ route('user.sales.edit', $sale) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#FFE600] text-[#071A3D] font-semibold text-sm hover:bg-yellow-300 transition shadow">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                            Crear Ficha
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="py-8 px-6 text-center text-white/80">
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
