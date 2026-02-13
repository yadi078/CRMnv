<x-app-layout>
    <div class="space-y-8">
            <!-- Estadísticas Generales - Tarjetas oscuras azul marino con íconos dorados -->
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
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

                <a href="{{ route('follow-ups.index', ['completado' => 0]) }}" class="metric-card-dark cursor-pointer block no-underline">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="metric-card-dark__label">Pendientes</p>
                            <p class="metric-card-dark__value">{{ $seguimientosPendientes }}</p>
                        </div>
                        <div class="metric-card-dark__icon-wrap">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Estado de prospectos - Contenedor azul como el resto, tarjetas pastel dentro -->
            <div class="panel-card-dark">
                <h3 class="panel-card-dark__title panel-card-dark__title--spaced section-title-underline">Estado de prospectos</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="{{ route('companies.index', ['status_color' => 'verde']) }}" class="text-center p-6 rounded-[var(--radius-card)] badge-semaphore-verde hover:opacity-95 transition-opacity cursor-pointer border-4 border-[#15803D] shadow-sm">
                        <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-[#15803D]/15 flex items-center justify-center">
                            <span class="text-xl font-semibold text-[#15803D]">{{ $empresasVerde }}</span>
                        </div>
                        <h4 class="font-semibold text-[#15803D] mb-1">Verde</h4>
                        <p class="text-sm text-[#6B7280]">Actividad reciente (últimos 7 días)</p>
                    </a>
                    <a href="{{ route('companies.index', ['status_color' => 'amarillo']) }}" class="text-center p-6 rounded-[var(--radius-card)] badge-semaphore-amarillo hover:opacity-95 transition-opacity cursor-pointer border-4 border-[#CA8A04] shadow-sm">
                        <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-[#EAB308]/20 flex items-center justify-center">
                            <span class="text-xl font-semibold text-[#CA8A04]">{{ $empresasAmarillo }}</span>
                        </div>
                        <h4 class="font-semibold text-[#CA8A04] mb-1">Amarillo</h4>
                        <p class="text-sm text-[#6B7280]">Actividad moderada (7-30 días)</p>
                    </a>
                    <a href="{{ route('companies.index', ['status_color' => 'rojo']) }}" class="text-center p-6 rounded-[var(--radius-card)] badge-semaphore-rojo hover:opacity-95 transition-opacity cursor-pointer border-4 border-[#B91C1C] shadow-sm">
                        <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-[#B91C1C]/15 flex items-center justify-center">
                            <span class="text-xl font-semibold text-[#B91C1C]">{{ $empresasRojo }}</span>
                        </div>
                        <h4 class="font-semibold text-[#B91C1C] mb-1">Rojo</h4>
                        <p class="text-sm text-[#6B7280]">Sin actividad (más de 30 días)</p>
                    </a>
                </div>
            </div>

            <!-- Aprobaciones Pendientes (Solo Admin) -->
            @can('companies.approve')
            @if($empresasPendientes > 0)
            <div class="view-card flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="font-semibold text-[#1F2937] section-title-underline">Aprobaciones pendientes</h3>
                    <p class="text-sm text-[#6B7280]">Tienes {{ $empresasPendientes }} empresa(s) esperando aprobación</p>
                </div>
                <a href="{{ route('approvals.companies') }}" class="btn-amber-app">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Revisar
                </a>
            </div>
            @endif
            @endcan

            <!-- Seguimientos Vencidos -->
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
                <!-- Últimas Empresas - Panel oscuro -->
                <div class="panel-card-dark">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="panel-card-dark__title section-title-underline">Últimas empresas</h3>
                        <a href="{{ route('companies.index') }}" class="panel-card-dark__link">
                            Ver todas
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                    <div class="space-y-0">
                        @forelse($ultimasEmpresas as $empresa)
                        <a href="{{ route('companies.show', $empresa) }}" class="panel-card-dark__item block">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <span class="panel-card-dark__item-icon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="panel-card-dark__item-name">{{ $empresa->nombre_comercial }}</p>
                                    <p class="panel-card-dark__item-meta">{{ $empresa->rfc }}</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-white/70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @empty
                        <p class="text-white/70 text-center py-6 text-sm">No hay empresas registradas</p>
                        @endforelse
                    </div>
                </div>

                <!-- Próximos Seguimientos - Panel oscuro -->
                <div class="panel-card-dark">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="panel-card-dark__title section-title-underline">Próximos seguimientos</h3>
                        <a href="{{ route('follow-ups.index') }}" class="panel-card-dark__link">
                            Ver todos
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                    <div class="space-y-0">
                        @forelse($proximosSeguimientos as $seguimiento)
                        <a href="{{ route('follow-ups.show', $seguimiento) }}" class="panel-card-dark__item block">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <span class="panel-card-dark__item-icon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="panel-card-dark__item-name">
                                        @if($seguimiento->company)
                                            {{ $seguimiento->company->nombre_comercial }}
                                        @elseif($seguimiento->contact)
                                            {{ $seguimiento->contact->nombre_completo }}
                                        @else
                                            Seguimiento
                                        @endif
                                    </p>
                                    <p class="panel-card-dark__item-meta">{{ $seguimiento->fecha_alarma->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-white/70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @empty
                        <p class="text-white/70 text-center py-6 text-sm">No hay seguimientos programados</p>
                        @endforelse
                    </div>
                </div>
            </div>
    </div>
</x-app-layout>
