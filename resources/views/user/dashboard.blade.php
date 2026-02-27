<x-app-user-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Inicio</h2>
            <p class="page-header-card__subtitle">Bienvenido, {{ Auth::user()->name }}</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        {{-- Resumen de Actividad - Métricas compactas en una sola hilera --}}
        <div class="flex gap-2">
            <a href="{{ route('follow-ups.index', ['completado' => 0]) }}" class="metric-card-dark metric-card-dark--compact cursor-pointer block no-underline flex-1 min-w-0">
                <div class="flex items-center justify-between gap-1.5">
                    <div class="min-w-0">
                        <p class="metric-card-dark__label">Seguimientos pendientes</p>
                        <p class="metric-card-dark__value">{{ $seguimientosPendientes }}</p>
                    </div>
                    <div class="metric-card-dark__icon-wrap flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </a>

            <div class="metric-card-dark metric-card-dark--compact flex-1 min-w-0">
                <div class="flex items-center justify-between gap-1.5">
                    <div class="min-w-0">
                        <p class="metric-card-dark__label">Alarmas programadas</p>
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
                <form method="GET" action="{{ route('user.dashboard') }}" class="mb-4">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-white/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input type="text" name="q_empresas" value="{{ request('q_empresas') }}" placeholder="Buscar empresa..." class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 pl-10 pr-3 text-sm">
                    </div>
                </form>
                <div class="overflow-x-auto">
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
                                        <a href="{{ route('companies.show', $empresa) }}" class="text-[#FFE600] hover:text-white font-medium">{{ $empresa->nombre_comercial }}</a>
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
                                <td colspan="3" class="px-4 py-6 text-center text-white/70">No hay empresas registradas</td>
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
                <div class="overflow-x-auto">
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
                                    <a href="{{ route('contacts.show', $contacto) }}" class="text-[#FFE600] hover:text-white font-medium">{{ $contacto->nombre_completo }}</a>
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
            <div class="overflow-x-auto">
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
                                <a href="{{ route('companies.show', $venta->company) }}" class="text-[#FFE600] hover:text-white">{{ $venta->company->nombre_comercial ?? '—' }}</a>
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
