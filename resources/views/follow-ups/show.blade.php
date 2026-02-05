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
                    <h2 class="view-header__title">Seguimiento - {{ ucfirst($followUp->tipo_accion) }}</h2>
                    <p class="view-header__subtitle">Detalle del seguimiento</p>
                </div>
            </div>
            <div class="btn-icon-text gap-2">
                @can('follow-ups.edit')
                <a href="{{ route('follow-ups.edit', $followUp) }}" class="btn-amber-app">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                @endcan
                <a href="{{ route('follow-ups.index') }}" class="btn-icon-text px-4 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="view-card p-6 space-y-6">
                <!-- Información Principal -->
                <div>
                    <h3 class="text-lg font-semibold text-azul-fuerte mb-4">Información del Seguimiento</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500">Tipo de Acción</p>
                            <span class="px-3 py-1 text-sm font-semibold rounded @if($followUp->tipo_accion === 'llamada') bg-blue-100 text-blue-800 @elseif($followUp->tipo_accion === 'reunión') bg-purple-100 text-purple-800 @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($followUp->tipo_accion) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Estado</p>
                            @if($followUp->completado)
                            <span class="px-3 py-1 text-sm font-semibold rounded bg-green-100 text-green-800">Completado</span>
                            @elseif($followUp->estaVencido())
                            <span class="px-3 py-1 text-sm font-semibold rounded bg-red-100 text-red-800">Vencido</span>
                            @else
                            <span class="px-3 py-1 text-sm font-semibold rounded bg-yellow-100 text-yellow-800">Pendiente</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Fecha Programada</p>
                            <p class="text-lg font-medium text-gray-900">{{ $followUp->fecha_alarma->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($followUp->asignado)
                        <div>
                            <p class="text-sm text-gray-500">Asignado a</p>
                            <p class="text-lg font-medium text-gray-900">{{ $followUp->asignado->name }}</p>
                        </div>
                        @endif
                        @if($followUp->company)
                        <div>
                            <p class="text-sm text-gray-500">Empresa</p>
                            <p class="text-lg font-medium text-gray-900">
                                <a href="{{ route('companies.show', $followUp->company) }}" class="text-azul-bright hover:text-azul-fuerte">
                                    {{ $followUp->company->nombre_comercial }}
                                </a>
                            </p>
                        </div>
                        @endif
                        @if($followUp->contact)
                        <div>
                            <p class="text-sm text-gray-500">Contacto</p>
                            <p class="text-lg font-medium text-gray-900">
                                <a href="{{ route('contacts.show', $followUp->contact) }}" class="text-azul-bright hover:text-azul-fuerte">
                                    {{ $followUp->contact->nombre_completo }}
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Bitácora de Notas -->
                @if($followUp->bitacora_notas)
                <div>
                    <h3 class="text-lg font-semibold text-azul-fuerte mb-4">Bitácora de Notas</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $followUp->bitacora_notas }}</p>
                    </div>
                </div>
                @endif

                <!-- Acciones -->
                @if(!$followUp->completado)
                <div class="flex justify-end">
                    <form method="POST" action="{{ route('follow-ups.complete', $followUp) }}">
                        @csrf
                        <button type="submit" class="btn-icon-text bg-green-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-green-700 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Marcar como Completado
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
