<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-azul-fuerte leading-tight">
                {{ $company->nombre_comercial }}
            </h2>
            <div class="flex space-x-2">
                @can('companies.edit')
                <a href="{{ route('companies.edit', $company) }}" class="bg-amarillo text-azul-fuerte px-4 py-2 rounded-md font-semibold hover:bg-yellow-400 transition">
                    Editar
                </a>
                @endcan
                <a href="{{ route('companies.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-semibold hover:bg-gray-300 transition">
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
                    <a href="{{ route('contacts.create', ['company_id' => $company->id]) }}" class="bg-azul-bright text-white px-4 py-2 rounded-md font-semibold hover:bg-azul-fuerte transition">
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
                            <a href="{{ route('contacts.show', $contact) }}" class="text-azul-bright hover:text-azul-fuerte">
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
                    <a href="{{ route('follow-ups.create', ['company_id' => $company->id]) }}" class="bg-azul text-white px-4 py-2 rounded-md font-semibold hover:bg-azul-fuerte transition">
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
