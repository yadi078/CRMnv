<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-azul-fuerte leading-tight">
                {{ $contact->nombre_completo }}
            </h2>
            <div class="flex space-x-2">
                @can('contacts.generate-pdf')
                <a href="{{ route('contacts.pdf', $contact) }}" class="bg-red-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-red-700 transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Generar PDF
                </a>
                @endcan
                @can('contacts.edit')
                <a href="{{ route('contacts.edit', $contact) }}" class="bg-amarillo text-azul-fuerte px-4 py-2 rounded-md font-semibold hover:bg-yellow-400 transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Editar
                </a>
                @endcan
                <a href="{{ route('contacts.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-semibold hover:bg-gray-300 transition inline-flex items-center">
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
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-azul-fuerte mb-4">Información del Contacto</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Nombre Completo</p>
                        <p class="text-lg font-medium text-gray-900">{{ $contact->nombre_completo }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Empresa</p>
                        <p class="text-lg font-medium text-gray-900">
                            <a href="{{ route('companies.show', $contact->company) }}" class="text-azul-bright hover:text-azul-fuerte">
                                {{ $contact->company->nombre_comercial }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Puesto de Trabajo</p>
                        <p class="text-lg font-medium text-gray-900">{{ $contact->puesto_de_trabajo ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Departamento</p>
                        <p class="text-lg font-medium text-gray-900">{{ $contact->departamento ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-lg font-medium text-gray-900">{{ $contact->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Celular</p>
                        <p class="text-lg font-medium text-gray-900">{{ $contact->celular ?? '-' }}</p>
                    </div>
                    @if($contact->extension)
                    <div>
                        <p class="text-sm text-gray-500">Extensión</p>
                        <p class="text-lg font-medium text-gray-900">{{ $contact->extension }}</p>
                    </div>
                    @endif
                    @if($contact->municipio || $contact->estado)
                    <div>
                        <p class="text-sm text-gray-500">Ubicación</p>
                        <p class="text-lg font-medium text-gray-900">{{ $contact->municipio ?? '' }}{{ $contact->municipio && $contact->estado ? ', ' : '' }}{{ $contact->estado ?? '' }}</p>
                    </div>
                    @endif
                    @if($contact->notas)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Notas</p>
                        <p class="text-gray-900">{{ $contact->notas }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
