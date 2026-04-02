<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Inicio</h2>
            <p class="page-header-card__subtitle">Bienvenido, {{ Auth::user()->name }}</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        {{-- Métricas alineadas al panel administrador (alcance: tu cartera) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('companies.index') }}" class="metric-card-dark cursor-pointer block no-underline">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-card-dark__label">Total Empresas</p>
                        <p class="metric-card-dark__value">{{ $totalEmpresas }}</p>
                    </div>
                    <div class="metric-card-dark__icon-wrap">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </a>
            <a href="{{ route('contacts.index') }}" class="metric-card-dark cursor-pointer block no-underline">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-card-dark__label">Total Contactos</p>
                        <p class="metric-card-dark__value">{{ $totalContactos }}</p>
                    </div>
                    <div class="metric-card-dark__icon-wrap">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </a>
            <a href="{{ route('follow-ups.index') }}" class="metric-card-dark cursor-pointer block no-underline">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-card-dark__label">Seguimientos</p>
                        <p class="metric-card-dark__value">{{ $totalSeguimientos }}</p>
                    </div>
                    <div class="metric-card-dark__icon-wrap">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>
            </a>
            <a href="{{ route('filtros.index') }}" class="metric-card-dark cursor-pointer block no-underline">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="metric-card-dark__label">Filtros</p>
                        <p class="metric-card-dark__value text-fluid-lg">Avanzados</p>
                    </div>
                    <div class="metric-card-dark__icon-wrap">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M5 10h14M9 16h6m-3 4v-4" />
                        </svg>
                    </div>
                </div>
            </a>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('follow-ups.index', ['completado' => 0]) }}" class="metric-card-dark metric-card-dark--compact cursor-pointer block no-underline flex-1 min-w-0">
                <div class="flex items-center justify-between gap-1.5">
                    <div class="min-w-0">
                        <p class="metric-card-dark__label">Seguimientos pendientes</p>
                        <p class="metric-card-dark__value">{{ $seguimientosPendientes }}</p>
                    </div>
                    <div class="metric-card-dark__icon-wrap flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>
            </a>
            <div class="metric-card-dark metric-card-dark--compact flex-1 min-w-0">
                <div class="flex items-center justify-between gap-1.5">
                    <div class="min-w-0">
                        <p class="metric-card-dark__label">Alarmas hoy</p>
                        <p class="metric-card-dark__value">{{ $alarmasProgramadas }}</p>
                    </div>
                    <div class="metric-card-dark__icon-wrap flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                </div>
            </div>
            <a href="{{ route('companies.index') }}" class="metric-card-dark metric-card-dark--compact cursor-pointer block no-underline border-l-4 border-l-red-500 flex-1 min-w-0">
                <div class="flex items-center justify-between gap-1.5">
                    <div class="min-w-0">
                        <p class="metric-card-dark__label">Solicitudes pendientes</p>
                        <p class="metric-card-dark__value">{{ $solicitudesPendientes }}</p>
                    </div>
                    <div class="metric-card-dark__icon-wrap flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </a>
        </div>

        <div class="panel-card-dark">
            <h3 class="panel-card-dark__title panel-card-dark__title--spaced section-title-underline">Estatus de prospectos (tus contactos)</h3>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-3">
                <a href="{{ route('contacts.index', ['status_color' => 'seguimiento']) }}" class="prospect-status-bar flex-1 min-h-[80px] sm:min-h-[100px] flex flex-col items-center justify-center gap-1 p-3 sm:p-4 rounded-xl border-4 border-[#15803D] bg-[#BBF7D0] hover:bg-[#86EFAC] transition-all cursor-pointer text-[#14532D] text-center">
                    <span class="text-2xl sm:text-3xl font-bold">{{ $contactosSeguimiento }}</span>
                    <span class="text-xs sm:text-sm font-semibold uppercase leading-tight">Seguimiento</span>
                </a>
                <a href="{{ route('contacts.index', ['status_color' => 'interesado']) }}" class="prospect-status-bar flex-1 min-h-[80px] sm:min-h-[100px] flex flex-col items-center justify-center gap-1 p-3 sm:p-4 rounded-xl border-4 border-[#DC2626] bg-[#FECACA] hover:bg-[#FCA5A5] transition-all cursor-pointer text-[#991B1B] text-center">
                    <span class="text-2xl sm:text-3xl font-bold">{{ $contactosInteresado }}</span>
                    <span class="text-xs sm:text-sm font-semibold uppercase leading-tight">Interesado</span>
                </a>
                <a href="{{ route('contacts.index', ['status_color' => 'si_le_interesa_nos_llaman_o_no_compro']) }}" class="prospect-status-bar flex-1 min-h-[80px] sm:min-h-[100px] flex flex-col items-center justify-center gap-1 p-3 sm:p-4 rounded-xl border-4 border-[#3B82F6] bg-[#BFDBFE] hover:bg-[#93C5FD] transition-all cursor-pointer text-[#1e3a5f] text-center">
                    <span class="text-2xl sm:text-3xl font-bold">{{ $contactosSiLeInteresa }}</span>
                    <span class="text-xs sm:text-sm font-semibold uppercase leading-tight" style="line-height: 1.2;">Si le interesa nos llaman o no compro</span>
                </a>
                <a href="{{ route('contacts.index', ['status_color' => 'vendido']) }}" class="prospect-status-bar flex-1 min-h-[80px] sm:min-h-[100px] flex flex-col items-center justify-center gap-1 p-3 sm:p-4 rounded-xl border-4 border-[#CA8A04] bg-[#FEF08A] hover:bg-[#FDE047] transition-all cursor-pointer text-[#713F12] text-center">
                    <span class="text-2xl sm:text-3xl font-bold">{{ $contactosVendido }}</span>
                    <span class="text-xs sm:text-sm font-semibold uppercase leading-tight">Vendido</span>
                </a>
                <a href="{{ route('contacts.index', ['status_color' => 'no_estaba']) }}" class="prospect-status-bar flex-1 min-h-[80px] sm:min-h-[100px] flex flex-col items-center justify-center gap-1 p-3 sm:p-4 rounded-xl border-4 border-[#7C3AED] bg-[#DDD6FE] hover:bg-[#C4B5FD] transition-all cursor-pointer text-[#4C1D95] text-center">
                    <span class="text-2xl sm:text-3xl font-bold">{{ $contactosNoEstaba }}</span>
                    <span class="text-xs sm:text-sm font-semibold uppercase leading-tight">No estaba</span>
                </a>
            </div>
        </div>

        @if($seguimientosVencidos > 0)
        <div class="view-card flex flex-wrap items-center justify-between gap-4 border-l-4 border-l-[#B91C1C]">
            <div>
                <h3 class="font-semibold text-[#1F2937] section-title-underline">Seguimientos vencidos</h3>
                <p class="text-sm text-[#6B7280]">Tienes {{ $seguimientosVencidos }} seguimiento(s) vencido(s)</p>
            </div>
            <a href="{{ route('follow-ups.index', ['completado' => 0]) }}" class="btn-primary-app">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Ver seguimientos
            </a>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Mis Empresas - Panel oscuro estilo admin --}}
            <div class="panel-card-dark">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="panel-card-dark__title section-title-underline">Mis Empresas</h3>
                    <a href="{{ route('companies.index') }}" class="panel-card-dark__link">
                        Ver todas
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
                <form method="GET" action="{{ route('user.dashboard') }}" class="mb-4 flex flex-col sm:flex-row gap-2 sm:items-stretch">
                    <div class="flex-1 min-w-0">
                        <label for="q_empresas" class="sr-only">Buscar empresa por nombre</label>
                        <input
                            id="q_empresas"
                            type="search"
                            name="q_empresas"
                            value="{{ request('q_empresas') }}"
                            placeholder="Buscar por nombre de empresa..."
                            autocomplete="off"
                            class="w-full rounded-xl border-2 border-[#FFE600]/50 bg-white text-[#1F2937] placeholder-gray-500 shadow-sm py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:border-[#FFE600]"
                        >
                    </div>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 shrink-0 rounded-xl bg-[#FFE600] px-4 py-2.5 text-sm font-semibold text-[#003366] shadow-sm hover:bg-[#e6cf00] focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-[#1a3d6b] transition-colors"
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Buscar</span>
                    </button>
                </form>
                <div class="crm-responsive-x">
                    <table class="min-w-full divide-y divide-white/20 text-sm">
                        <thead>
                            <tr class="table-header-panel-dark">
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Nombre</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Sector</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Contacto Principal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/15">
                            @forelse($misEmpresas as $empresa)
                            <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($empresa->approval_status === 'pendiente')
                                        <span class="w-2 h-2 rounded-full bg-[#FFE600] flex-shrink-0" title="Pendiente"></span>
                                        @endif
                                        <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $empresa)) }}" class="text-[#FFE600] hover:text-white font-medium">{{ $empresa->nombre_comercial }}</a>
                                    </div>
                                    @if($empresa->approval_status === 'pendiente')
                                    <p class="text-xs text-[#FCD34D] mt-0.5">Pendiente</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-white/90">{{ $empresa->sector ?? '—' }}</td>
                                <td class="px-4 py-3 text-white/90">
                                    @php $contactoPrincipal = $empresa->contacts->first(); @endphp
                                    @if($contactoPrincipal)
                                    <span>{{ $contactoPrincipal->nombre_completo }}</span>
                                    @if($contactoPrincipal->celular)
                                    <span class="text-white/70 flex items-center gap-1 mt-0.5">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        {{ $contactoPrincipal->celular }}
                                    </span>
                                    @endif
                                    @else
                                    —
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-white/70">
                                    @if(filled(trim((string) request('q_empresas', ''))))
                                        No se encontraron empresas con ese criterio. Prueba con otra palabra o revisa en <a href="{{ route('companies.index') }}" class="text-[#FFE600] underline hover:text-white">Ver todas</a>.
                                    @else
                                        No hay empresas en tu listado todavía.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-white/70">Empresas en estado Pendiente (aún no aprobadas por el administrador).</p>
            </div>

            {{-- Mis Contactos - Panel oscuro estilo admin --}}
            <div class="panel-card-dark">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="panel-card-dark__title section-title-underline">Mis Contactos</h3>
                    @can('contacts.create')
                    <a href="{{ route('contacts.create') }}" class="panel-card-dark__link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Nuevo contacto
                    </a>
                    @endcan
                </div>
                @if($misContactos->isEmpty())
                <p class="text-white/80 py-6">Aún no has agregado contactos.</p>
                @can('contacts.create')
                <a href="{{ route('contacts.create') }}" class="btn-amber-app inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Añadir Nuevo Contacto
                </a>
                @endcan
                @else
                <div class="crm-responsive-x">
                    <table class="min-w-full divide-y divide-white/20 text-sm">
                        <thead>
                            <tr class="table-header-panel-dark">
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Nombre</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Empresa</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Contacto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/15">
                            @foreach($misContactos->take(5) as $contacto)
                            <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                                <td class="px-4 py-3">
                                    <a href="{{ \App\Support\CrmNavigation::withReturn(route('contacts.show', $contacto)) }}" class="text-[#FFE600] hover:text-white font-medium">{{ $contacto->nombre_completo }}</a>
                                </td>
                                <td class="px-4 py-3 text-white/90">{{ $contacto->company->nombre_comercial ?? '—' }}</td>
                                <td class="px-4 py-3 text-white/90">{{ $contacto->celular ?? $contacto->email ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('contacts.index') }}" class="mt-4 inline-block panel-card-dark__link">Ver todos los contactos →</a>
                @endif
            </div>
        </div>

        {{-- Historial de Ventas - Panel oscuro estilo admin --}}
        <div class="panel-card-dark">
            <div class="flex justify-between items-center mb-4">
                <h3 class="panel-card-dark__title section-title-underline">Historial de Ventas</h3>
                <a href="{{ route('user.sales.index') }}" class="panel-card-dark__link">
                    Ir a Historial de Ventas
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
            <div class="crm-responsive-x">
                <table class="min-w-full divide-y divide-white/20 text-sm">
                    <thead>
                        <tr class="table-header-panel-dark">
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Curso/Servicio</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Cliente</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($ventasRecientes as $venta)
                        <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                            <td class="px-4 py-3 text-white">{{ $venta->nombre_servicio ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $venta->company)) }}" class="text-[#FFE600] hover:text-white">{{ $venta->company->nombre_comercial ?? '—' }}</a>
                            </td>
                            <td class="px-4 py-3 text-white/90">{{ $venta->fecha_venta->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-white/90">—</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-white/70">No hay ventas registradas por el momento.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-user-layout>
