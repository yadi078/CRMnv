<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-azul-fuerte leading-tight">
                Seguimiento - {{ ucfirst($followUp->tipo_accion) }}
            </h2>
            <div class="flex space-x-2">
                @can('follow-ups.edit')
                <a href="{{ route('follow-ups.edit', $followUp) }}" class="bg-amarillo text-azul-fuerte px-4 py-2 rounded-md font-semibold hover:bg-yellow-400 transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Editar
                </a>
                @endcan
                <a href="{{ route('follow-ups.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-semibold hover:bg-gray-300 transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
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
                        <x-primary-button class="bg-green-600 hover:bg-green-700 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Marcar como Completado
                        </x-primary-button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
