@php
    $suppressGlobalCreateLink = true;
    $emptyExtra = null;
    if ($scope === 'company' && $sales->isEmpty() && auth()->user()->can('sales.create')) {
        $emptyExtra = '<a href="' . e(route('user.sales.create', ['company_id' => $company->id])) . '" class="text-[#FFE600] hover:text-white underline ml-1">Registrar venta para esta empresa</a>';
    }
@endphp
<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg></x-page-header-avatar>
        <div>
            @if($scope === 'company')
                <h2 class="page-header-card__title">Historial de ventas — {{ $company->nombre_comercial }}</h2>
                <p class="page-header-card__subtitle">Ventas registradas para esta empresa</p>
            @else
                <h2 class="page-header-card__title">Historial de ventas — {{ $contact->nombre_completo }}</h2>
                <p class="page-header-card__subtitle">{{ $company?->nombre_comercial ?? '—' }}</p>
            @endif
            <div class="mt-3 flex flex-wrap gap-2">
                @can('sales.create')
                    @if($scope === 'company')
                        <a
                            href="{{ route('user.sales.create', ['company_id' => $company->id]) }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#FFE600] px-3.5 py-2 text-sm font-semibold text-[#071A3D] shadow-sm transition-all hover:bg-yellow-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FFE600]/50"
                        >
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Registrar nueva venta</span>
                        </a>
                    @else
                        <a
                            href="{{ $contact->company_id ? route('user.sales.create', ['company_id' => $contact->company_id, 'contact_id' => $contact->id]) : route('user.sales.create') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#FFE600] px-3.5 py-2 text-sm font-semibold text-[#071A3D] shadow-sm transition-all hover:bg-yellow-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FFE600]/50"
                        >
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Generar ficha de inscripción</span>
                        </a>
                    @endif
                @endcan
                <a
                    href="{{ $scope === 'company' ? \App\Support\CrmNavigation::withReturn(route('companies.show', $company)) : \App\Support\CrmNavigation::withReturn(route('contacts.show', $contact)) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-white/35 bg-white/[0.07] px-3.5 py-2 text-sm font-medium text-white/95 shadow-sm transition-all hover:border-[#FFE600]/45 hover:bg-white/12 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FFE600]/50"
                >
                    <svg class="h-4 w-4 flex-shrink-0 text-[#FFE600]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Volver a la ficha</span>
                </a>
                <a
                    href="{{ route('user.sales.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-white/35 bg-white/[0.07] px-3.5 py-2 text-sm font-medium text-white/95 shadow-sm transition-all hover:border-[#FFE600]/45 hover:bg-white/12 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FFE600]/50"
                >
                    <span>Historial general</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if($scope === 'company')
            <div class="panel-card-dark overflow-hidden p-6">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-y-3">
                    <h3 class="panel-card-dark__title panel-card-dark__title--accent text-xl m-0 min-w-0">Historial de ventas</h3>
                    @can('sales.create')
                        <a href="{{ route('user.sales.create', ['company_id' => $company->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FFE600] text-[#071A3D] font-semibold text-sm shadow hover:bg-yellow-300 transition-colors shrink-0 self-end sm:self-auto">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Registrar nueva venta
                        </a>
                    @endcan
                </div>
                <form method="GET" action="{{ $formAction }}" class="mb-6 flex flex-wrap items-end gap-3 sm:gap-4">
                    <div class="min-w-[140px] flex-1 sm:flex-initial sm:max-w-[180px]">
                        <label for="filtro_fecha_scoped" class="block text-sm font-medium text-white/90 mb-1">Fecha</label>
                        <select id="filtro_fecha_scoped" name="filtro_fecha" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-[#1a3d6b] [&>option]:text-white">
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
                ])
            </div>
        @else
            @include('contacts.partials.contact-sales-history', [
                'contact' => $contact,
                'sales' => $sales,
                'formAction' => $formAction,
                'showPanelTitle' => false,
            ])
        @endif
    </div>
</x-app-user-layout>
