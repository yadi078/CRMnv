<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-azul-fuerte leading-tight">
                {{ $contact->nombre_completo }}
            </h2>
            <div class="flex space-x-2">
                @can('contacts.generate-pdf')
                <a href="{{ route('contacts.pdf', $contact) }}" class="bg-red-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-red-700 transition">
                    Generar PDF
                </a>
                @endcan
                @can('contacts.edit')
                <a href="{{ route('contacts.edit', $contact) }}" class="bg-amarillo text-azul-fuerte px-4 py-2 rounded-md font-semibold hover:bg-yellow-400 transition">
                    Editar
                </a>
                @endcan
                <a href="{{ route('contacts.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-semibold hover:bg-gray-300 transition">
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
