<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Historial de ventas</h2>
            <p class="page-header-card__subtitle">Empresa, contacto y curso vendido al marcar como vendido</p>
            <div class="mt-3">
                <a
                    href="{{ route('companies.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-white/35 bg-white/[0.07] px-3.5 py-2 text-sm font-medium text-white/95 shadow-sm transition-all hover:border-[#FFE600]/45 hover:bg-white/12 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FFE600]/50"
                >
                    <svg class="h-4 w-4 flex-shrink-0 text-[#FFE600]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Volver a Empresas</span>
                </a>
            </div>
        </div>
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
            </form>

            @include('user.sales.partials.sales-table', ['sales' => $sales])
        </div>
    </div>

    @include('contacts.partials.ficha-venta-auto-download')
</x-app-user-layout>
