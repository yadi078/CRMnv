<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-azul-fuerte leading-tight">
                {{ $company->nombre_comercial }}
            </h2>
            <div class="flex space-x-2">
                @can('companies.edit')
                <a href="{{ route('companies.edit', $company) }}" class="bg-amarillo text-azul-fuerte px-4 py-2 rounded-md font-semibold hover:bg-yellow-400 transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Editar
                </a>
                @endcan
                <a href="{{ route('companies.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-semibold hover:bg-gray-300 transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Información Principal -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-azul-fuerte">Información de la Empresa</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded-full @if($company->status_color === 'verde') bg-green-500 @elseif($company->status_color === 'amarillo') bg-yellow-500 @else bg-red-500 @endif"></div>
                        <span class="text-sm font-medium text-gray-700">{{ ucfirst($company->status_color) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Nombre Comercial</p>
                        <p class="text-lg font-medium text-gray-900">{{ $company->nombre_comercial }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">RFC</p>
                        <p class="text-lg font-medium text-gray-900">{{ $company->rfc }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Sector/Giro</p>
                        <p class="text-lg font-medium text-gray-900">{{ $company->sector ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ejecutivo Asignado</p>
                        <p class="text-lg font-medium text-gray-900">{{ $company->ejecutivo_asignado ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ubicación</p>
                        <p class="text-lg font-medium text-gray-900">{{ $company->municipio ?? '' }} {{ $company->estado ? ', ' . $company->estado : '' }}</p>
                    </div>
                    @if($company->datos_fiscales)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Datos Fiscales</p>
                        <p class="text-gray-900">{{ $company->datos_fiscales }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contactos -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-azul-fuerte">Contactos</h3>
                    @can('contacts.create')
                    <a href="{{ route('contacts.create', ['company_id' => $company->id]) }}" class="bg-azul-bright text-white px-4 py-2 rounded-md font-semibold hover:bg-azul-fuerte transition inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nuevo Contacto
                    </a>
                    @endcan
                </div>

                <div class="space-y-3">
                    @forelse($company->contacts as $contact)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900">{{ $contact->nombre_completo }}</p>
                                <p class="text-sm text-gray-500">{{ $contact->puesto_de_trabajo ?? '-' }}</p>
                                <p class="text-sm text-gray-500">{{ $contact->email }}</p>
                                @if($contact->celular)
                                <p class="text-sm text-gray-500">{{ $contact->celular }}</p>
                                @endif
                            </div>
                            <a href="{{ route('contacts.show', $contact) }}" class="text-azul-bright hover:text-azul-fuerte inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No hay contactos registrados</p>
                    @endforelse
                </div>
            </div>

            <!-- Seguimientos -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-azul-fuerte">Seguimientos</h3>
                    @can('follow-ups.create')
                    <a href="{{ route('follow-ups.create', ['company_id' => $company->id]) }}" class="bg-azul text-white px-4 py-2 rounded-md font-semibold hover:bg-azul-fuerte transition inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nuevo Seguimiento
                    </a>
                    @endcan
                </div>

                <div class="space-y-3">
                    @forelse($company->followUps as $followUp)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="px-2 py-1 text-xs font-semibold rounded @if($followUp->tipo_accion === 'llamada') bg-blue-100 text-blue-800 @elseif($followUp->tipo_accion === 'reunión') bg-purple-100 text-purple-800 @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($followUp->tipo_accion) }}
                                </span>
                                <p class="text-sm text-gray-500 mt-2">{{ $followUp->fecha_alarma->format('d/m/Y H:i') }}</p>
                                @if($followUp->bitacora_notas)
                                <p class="text-sm text-gray-700 mt-2">{{ $followUp->bitacora_notas }}</p>
                                @endif
                            </div>
                            @if(!$followUp->completado)
                            <span class="text-xs text-yellow-600 bg-yellow-100 px-2 py-1 rounded">Pendiente</span>
                            @else
                            <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">Completado</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No hay seguimientos registrados</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
