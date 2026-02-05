<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">Seguimientos</h2>
                    <p class="view-header__subtitle">Listado de seguimientos</p>
                </div>
            </div>
            @can('follow-ups.create')
            <a href="{{ route('follow-ups.create') }}" class="btn-amber-app">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo Seguimiento
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="view-card p-6">
                <!-- Filtros -->
                <form method="GET" action="{{ route('follow-ups.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <select name="completado" class="rounded-xl border-gray-300 shadow-sm focus:border-amarillo focus:ring-2 focus:ring-amber-400/20 py-2">
                        <option value="">Todos</option>
                        <option value="0" {{ request('completado') === '0' ? 'selected' : '' }}>Pendientes</option>
                        <option value="1" {{ request('completado') === '1' ? 'selected' : '' }}>Completados</option>
                    </select>
                    <select name="tipo_accion" class="rounded-xl border-gray-300 shadow-sm focus:border-amarillo focus:ring-2 focus:ring-amber-400/20 py-2">
                        <option value="">Todos los tipos</option>
                        <option value="llamada" {{ request('tipo_accion') === 'llamada' ? 'selected' : '' }}>Llamada</option>
                        <option value="reunión" {{ request('tipo_accion') === 'reunión' ? 'selected' : '' }}>Reunión</option>
                        <option value="cierre" {{ request('tipo_accion') === 'cierre' ? 'selected' : '' }}>Cierre</option>
                    </select>
                    <button type="submit" class="btn-primary-app">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                        </svg>
                        Filtrar
                    </button>
                </form>

                <!-- Lista -->
                <div class="space-y-4">
                    @forelse($followUps as $followUp)
                    <div class="p-4 bg-gray-50 rounded-lg border-l-4 @if($followUp->completado) border-green-500 @elseif($followUp->estaVencido()) border-red-500 @else border-yellow-500 @endif">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded @if($followUp->tipo_accion === 'llamada') bg-blue-100 text-blue-800 @elseif($followUp->tipo_accion === 'reunión') bg-purple-100 text-purple-800 @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($followUp->tipo_accion) }}
                                    </span>
                                    @if($followUp->completado)
                                    <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">Completado</span>
                                    @elseif($followUp->estaVencido())
                                    <span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded">Vencido</span>
                                    @else
                                    <span class="text-xs text-yellow-600 bg-yellow-100 px-2 py-1 rounded">Pendiente</span>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-gray-900">
                                    @if($followUp->company)
                                        Empresa: <a href="{{ route('companies.show', $followUp->company) }}" class="text-azul-bright hover:text-azul-fuerte">{{ $followUp->company->nombre_comercial }}</a>
                                    @elseif($followUp->contact)
                                        Contacto: <a href="{{ route('contacts.show', $followUp->contact) }}" class="text-azul-bright hover:text-azul-fuerte">{{ $followUp->contact->nombre_completo }}</a>
                                    @else
                                        <span class="text-gray-500">Sin empresa/contacto asignado</span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 mt-1">Fecha: {{ $followUp->fecha_alarma->format('d/m/Y H:i') }}</p>
                                @if($followUp->bitacora_notas)
                                <p class="text-sm text-gray-700 mt-2">{{ Str::limit($followUp->bitacora_notas, 100) }}</p>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('follow-ups.show', $followUp) }}" class="text-azul-bright hover:text-azul-fuerte btn-icon-text">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver
                                </a>
                                @if(!$followUp->completado)
                                <form method="POST" action="{{ route('follow-ups.complete', $followUp) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 btn-icon-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Completar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-8">No se encontraron seguimientos</p>
                    @endforelse
                </div>

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $followUps->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
