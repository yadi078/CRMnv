<x-app-layout>
    <x-slot name="header">
        <div class="view-header">
            <div class="view-header__icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <div>
                <h2 class="view-header__title">INVERTIR EN VALOR ¡ATRAE VALOR!</h2>
                <p class="view-header__subtitle">Panel principal</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Estadísticas Generales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('companies.index') }}" class="view-card p-6 transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gris">Total Empresas</p>
                            <p class="text-3xl font-bold text-azul-fuerte">{{ $totalEmpresas }}</p>
                        </div>
                        <div class="bg-azul-fuerte bg-opacity-10 p-3 rounded-full">
                            <svg class="w-8 h-8 text-azul-fuerte" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('contacts.index') }}" class="view-card p-6 transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gris">Total Contactos</p>
                            <p class="text-3xl font-bold text-azul-bright">{{ $totalContactos }}</p>
                        </div>
                        <div class="bg-azul-bright bg-opacity-10 p-3 rounded-full">
                            <svg class="w-8 h-8 text-azul-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('follow-ups.index') }}" class="view-card p-6 transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gris">Seguimientos</p>
                            <p class="text-3xl font-bold text-azul">{{ $totalSeguimientos }}</p>
                        </div>
                        <div class="bg-azul bg-opacity-10 p-3 rounded-full">
                            <svg class="w-8 h-8 text-azul" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('follow-ups.index', ['completado' => 0]) }}" class="view-card p-6 transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gris">Pendientes</p>
                            <p class="text-3xl font-bold text-azul-fuerte">{{ $seguimientosPendientes }}</p>
                        </div>
                        <div class="bg-azul-fuerte bg-opacity-10 p-3 rounded-full">
                            <svg class="w-8 h-8 text-azul-fuerte" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Sistema de Semáforo -->
            <div class="view-card p-6">
                <h3 class="text-lg font-semibold text-azul-fuerte mb-4">Sistema de Semáforo - Estado de Prospectos</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="{{ route('companies.index', ['status_color' => 'verde']) }}" class="text-center p-6 rounded-lg bg-green-50 border-2 border-green-500 hover:bg-green-100 hover:shadow-md transition-all cursor-pointer">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-500 flex items-center justify-center">
                            <span class="text-white text-2xl font-bold">{{ $empresasVerde }}</span>
                        </div>
                        <h4 class="font-semibold text-green-700 mb-2">Verde</h4>
                        <p class="text-sm text-gray-600">Actividad reciente<br>(Últimos 7 días)</p>
                    </a>

                    <a href="{{ route('companies.index', ['status_color' => 'amarillo']) }}" class="text-center p-6 rounded-lg bg-yellow-50 border-2 border-yellow-500 hover:bg-yellow-100 hover:shadow-md transition-all cursor-pointer">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-yellow-500 flex items-center justify-center">
                            <span class="text-white text-2xl font-bold">{{ $empresasAmarillo }}</span>
                        </div>
                        <h4 class="font-semibold text-yellow-700 mb-2">Amarillo</h4>
                        <p class="text-sm text-gray-600">Actividad moderada<br>(7-30 días)</p>
                    </a>

                    <a href="{{ route('companies.index', ['status_color' => 'rojo']) }}" class="text-center p-6 rounded-lg bg-red-50 border-2 border-red-500 hover:bg-red-100 hover:shadow-md transition-all cursor-pointer">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-500 flex items-center justify-center">
                            <span class="text-white text-2xl font-bold">{{ $empresasRojo }}</span>
                        </div>
                        <h4 class="font-semibold text-red-700 mb-2">Rojo</h4>
                        <p class="text-sm text-gray-600">Sin actividad<br>(Más de 30 días)</p>
                    </a>
                </div>
            </div>

            <!-- Aprobaciones Pendientes (Solo Admin) -->
            @can('companies.approve')
            @if($empresasPendientes > 0)
            <div class="bg-yellow-50 border-l-4 border-amarillo p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-yellow-800">Aprobaciones Pendientes</h3>
                        <p class="text-sm text-yellow-700">Tienes {{ $empresasPendientes }} empresa(s) esperando aprobación</p>
                    </div>
                    <a href="{{ route('approvals.companies') }}" class="bg-amarillo text-azul-fuerte px-4 py-2 rounded-md font-semibold hover:bg-yellow-400 transition inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Revisar
                    </a>
                </div>
            </div>
            @endif
            @endcan

            <!-- Seguimientos Vencidos -->
            @if($seguimientosVencidos > 0)
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-red-800">Seguimientos Vencidos</h3>
                        <p class="text-sm text-red-700">Tienes {{ $seguimientosVencidos }} seguimiento(s) vencido(s)</p>
                    </div>
                    <a href="{{ route('follow-ups.index', ['completado' => 0]) }}" class="bg-red-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-red-600 transition inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Ver Seguimientos
                    </a>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Últimas Empresas -->
                <div class="view-card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-azul-fuerte">Últimas Empresas</h3>
                        <a href="{{ route('companies.index') }}" class="text-azul-bright hover:text-azul-fuerte text-sm font-medium inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                            Ver todas
                        </a>
                    </div>
                    <div class="space-y-3">
                        @forelse($ultimasEmpresas as $empresa)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full @if($empresa->status_color === 'verde') bg-green-500 @elseif($empresa->status_color === 'amarillo') bg-yellow-500 @else bg-red-500 @endif"></div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $empresa->nombre_comercial }}</p>
                                    <p class="text-sm text-gray-500">{{ $empresa->rfc }}</p>
                                </div>
                            </div>
                            <a href="{{ route('companies.show', $empresa) }}" class="text-azul-bright hover:text-azul-fuerte">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No hay empresas registradas</p>
                        @endforelse
                    </div>
                </div>

                <!-- Próximos Seguimientos -->
                <div class="view-card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-azul-fuerte">Próximos Seguimientos</h3>
                        <a href="{{ route('follow-ups.index') }}" class="text-azul-bright hover:text-azul-fuerte text-sm font-medium inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                            Ver todos
                        </a>
                    </div>
                    <div class="space-y-3">
                        @forelse($proximosSeguimientos as $seguimiento)
                        <a href="{{ route('follow-ups.show', $seguimiento) }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded @if($seguimiento->tipo_accion === 'llamada') bg-blue-100 text-blue-800 @elseif($seguimiento->tipo_accion === 'reunión') bg-purple-100 text-purple-800 @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($seguimiento->tipo_accion) }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $seguimiento->fecha_alarma->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-900">
                                @if($seguimiento->company)
                                    {{ $seguimiento->company->nombre_comercial }}
                                @elseif($seguimiento->contact)
                                    {{ $seguimiento->contact->nombre_completo }}
                                @endif
                            </p>
                            @if($seguimiento->bitacora_notas)
                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($seguimiento->bitacora_notas, 50) }}</p>
                            @endif
                        </a>
                        @empty
                        <p class="text-gray-500 text-center py-4">No hay seguimientos programados</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
