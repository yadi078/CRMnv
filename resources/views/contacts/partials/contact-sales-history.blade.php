@php
    $showPanelTitle = $showPanelTitle ?? true;
    $company = $contact?->company;
    $suppressGlobalCreateLink = true;
    $emptyExtra = null;
    if ($sales && $sales->isEmpty() && auth()->user()->can('sales.create') && $contact && $company) {
        $emptyExtra = '<a href="' . e(route('user.sales.create', ['company_id' => $company->id, 'contact_id' => $contact->id])) . '" class="text-[#FFE600] hover:text-white underline ml-1">Generar ficha de inscripción para este contacto</a>';
    }
    $nuevaVentaHistorialUrl = $contact && $contact->company_id
        ? route('user.sales.create', ['company_id' => $contact->company_id, 'contact_id' => $contact->id])
        : route('user.sales.create');
@endphp

@if($sales !== null && $contact)
    <div class="panel-card-dark overflow-hidden p-6">
        @if($showPanelTitle)
            @can('sales.create')
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="panel-card-dark__title panel-card-dark__title--accent !text-2xl md:!text-3xl !font-extrabold tracking-tight m-0 min-w-0">Historial de ventas</h3>
                    <a href="{{ $nuevaVentaHistorialUrl }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FFE600] text-[#071A3D] font-semibold text-sm shadow hover:bg-yellow-300 transition-colors shrink-0 self-end sm:self-auto">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Generar ficha de inscripción
                    </a>
                </div>
            @else
                <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4 !text-2xl md:!text-3xl !font-extrabold tracking-tight">Historial de ventas</h3>
            @endcan
        @else
            @can('sales.create')
                <div class="mb-4 flex justify-end">
                    <a href="{{ $nuevaVentaHistorialUrl }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FFE600] text-[#071A3D] font-semibold text-sm shadow hover:bg-yellow-300 transition-colors shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Generar ficha de inscripción
                    </a>
                </div>
            @endcan
        @endif
        <form method="GET" action="{{ $formAction }}" class="mb-6 flex flex-wrap items-end gap-3 sm:gap-4">
            <div class="min-w-[140px] flex-1 sm:flex-initial sm:max-w-[180px]">
                <label for="filtro_fecha_contact_sales" class="block text-sm font-medium text-white/90 mb-1">Fecha</label>
                <select id="filtro_fecha_contact_sales" name="filtro_fecha" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-[#1a3d6b] [&>option]:text-white">
                    <option value="todos" {{ request('filtro_fecha', 'todos') === 'todos' ? 'selected' : '' }}>Todos</option>
                    <option value="7" {{ request('filtro_fecha') === '7' ? 'selected' : '' }}>Últimos 7 días</option>
                    <option value="14" {{ request('filtro_fecha') === '14' ? 'selected' : '' }}>Últimos 14 días</option>
                    <option value="30" {{ request('filtro_fecha') === '30' ? 'selected' : '' }}>Último mes</option>
                </select>
            </div>
            <a href="{{ $formAction }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-white/40 text-white/90 text-sm font-medium hover:bg-white/10 transition-all">
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

        @include('user.sales.partials.sales-table', [
            'sales' => $sales,
            'suppressGlobalCreateLink' => $suppressGlobalCreateLink,
            'emptyExtra' => $emptyExtra,
            'showFichaInscripcionColumn' => true,
        ])
    </div>
@endif
