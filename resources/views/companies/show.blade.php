<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">{{ $company->nombre_comercial }}</h2>
                    <p class="view-header__subtitle">Detalle de empresa</p>
                </div>
            </div>
            <div class="btn-icon-text gap-2">
                @can('companies.edit')
                <a href="{{ route('companies.edit', $company) }}" class="btn-amber-app">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                @endcan
                <a href="{{ route('companies.index') }}" class="btn-icon-text px-4 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Información Principal -->
            <div class="view-card p-6">
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
            <div class="view-card p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-azul-fuerte">Contactos</h3>
                    @can('contacts.create')
                    <a href="{{ route('contacts.create', ['company_id' => $company->id]) }}" class="btn-primary-app">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <a href="{{ route('contacts.show', $contact) }}" class="text-azul-bright hover:text-azul-fuerte btn-icon-text">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="view-card p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-azul-fuerte">Seguimientos</h3>
                    @can('follow-ups.create')
                    <a href="{{ route('follow-ups.create', ['company_id' => $company->id]) }}" class="btn-primary-app">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
