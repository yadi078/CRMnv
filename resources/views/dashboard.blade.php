<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Página principal</h2>
            <p class="page-header-card__subtitle">Bienvenido, {{ Auth::user()->name }}</p>
        </div>
    </x-slot>

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

                <a href="{{ route('approvals.index') }}" class="metric-card-dark cursor-pointer block no-underline">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="metric-card-dark__label">Solicitudes pendientes</p>
                            <p class="metric-card-dark__value">{{ $empresasPendientes + $usuariosPendientes }}</p>
                        </div>
                        <div class="metric-card-dark__icon-wrap">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Estado de prospectos (por contactos) - Semáforo -->
            <div class="panel-card-dark">
                <h3 class="panel-card-dark__title panel-card-dark__title--spaced section-title-underline">Estado de prospectos (contactos)</h3>
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
