<x-app-user-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">{{ $contact->nombre_completo }}</h2>
                    <p class="view-header__subtitle">Detalle de contacto</p>
                </div>
            </div>
            <div class="btn-icon-text gap-2">
                @can('contacts.edit')
                <a href="{{ route('contacts.edit', $contact) }}" class="btn-amber-app">Editar</a>
                @endcan
                <a href="{{ route('contacts.index') }}" class="btn-icon-text px-4 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="view-card p-6">
                <h3 class="text-lg font-semibold text-azul-fuerte mb-4">Información del Contacto</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><p class="text-sm text-gray-500">Nombre Completo</p><p class="text-lg font-medium text-gray-900">{{ $contact->nombre_completo }}</p></div>
                    <div><p class="text-sm text-gray-500">Empresa</p><p class="text-lg font-medium text-gray-900"><a href="{{ route('companies.show', $contact->company) }}" class="text-azul-bright hover:text-azul-fuerte">{{ $contact->company->nombre_comercial }}</a></p></div>
                    <div><p class="text-sm text-gray-500">Puesto</p><p class="text-lg font-medium text-gray-900">{{ $contact->puesto_de_trabajo ?? '-' }}</p></div>
                    <div><p class="text-sm text-gray-500">Departamento</p><p class="text-lg font-medium text-gray-900">{{ $contact->departamento ?? '-' }}</p></div>
                    <div><p class="text-sm text-gray-500">Correo</p><p class="text-lg font-medium text-gray-900">{{ $contact->email }}</p></div>
                    <div><p class="text-sm text-gray-500">Celular</p><p class="text-lg font-medium text-gray-900">{{ $contact->celular ?? '-' }}</p></div>
                    @if($contact->notas)
                    <div class="md:col-span-2"><p class="text-sm text-gray-500">Notas</p><p class="text-gray-900">{{ $contact->notas }}</p></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-user-layout>
