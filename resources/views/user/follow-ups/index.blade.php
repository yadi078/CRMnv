<x-app-user-layout>
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
                    <p class="view-header__subtitle">Alarmas y notas de seguimiento (códigos de color por estatus)</p>
                </div>
            </div>
            @can('follow-ups.create')
            <a href="{{ route('follow-ups.create') }}" class="btn-amber-app">Nuevo Seguimiento</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="view-card p-6">
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
                    <button type="submit" class="btn-primary-app">Filtrar</button>
                </form>
                <div class="space-y-4">
                    @forelse($followUps as $followUp)
                    <div class="p-4 bg-gray-50 rounded-lg border-l-4 @if($followUp->completado) border-green-500 @elseif($followUp->estaVencido()) border-red-500 @else border-yellow-500 @endif">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded @if($followUp->tipo_accion === 'llamada') bg-blue-100 text-blue-800 @elseif($followUp->tipo_accion === 'reunión') bg-purple-100 text-purple-800 @else bg-green-100 text-green-800 @endif">{{ ucfirst($followUp->tipo_accion) }}</span>
                                    @if($followUp->completado)<span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">Completado</span>@elseif($followUp->estaVencido())<span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded">Vencido</span>@else<span class="text-xs text-yellow-600 bg-yellow-100 px-2 py-1 rounded">Pendiente</span>@endif
                                </div>
                                <p class="text-sm font-medium text-gray-900">
                                    @if($followUp->company) Empresa: <a href="{{ route('companies.show', $followUp->company) }}" class="text-azul-bright hover:text-azul-fuerte">{{ $followUp->company->nombre_comercial }}</a>
                                    @elseif($followUp->contact) Contacto: <a href="{{ route('contacts.show', $followUp->contact) }}" class="text-azul-bright hover:text-azul-fuerte">{{ $followUp->contact->nombre_completo }}</a>
                                    @else <span class="text-gray-500">Sin empresa/contacto asignado</span> @endif
                                </p>
                                <p class="text-sm text-gray-500 mt-1">Fecha: {{ $followUp->fecha_alarma->format('d/m/Y H:i') }}</p>
                                @if($followUp->bitacora_notas)<p class="text-sm text-gray-700 mt-2">{{ Str::limit($followUp->bitacora_notas, 100) }}</p>@endif
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('follow-ups.show', $followUp) }}" class="text-azul-bright hover:text-azul-fuerte btn-icon-text">Ver</a>
                                @if(!$followUp->completado)
                                <form method="POST" action="{{ route('follow-ups.complete', $followUp) }}" class="inline">@csrf<button type="submit" class="text-green-600 hover:text-green-800 btn-icon-text">Completar</button></form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-8">No se encontraron seguimientos</p>
                    @endforelse
                </div>
                <div class="mt-4">{{ $followUps->links() }}</div>
            </div>
        </div>
    </div>
</x-app-user-layout>
