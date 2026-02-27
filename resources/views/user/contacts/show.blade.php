<x-app-user-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">{{ $contact->nombre_completo }}</h2>
            <p class="page-header-card__subtitle">Detalle de contacto</p>
        </div>
        <div class="flex gap-2 ml-auto">
            @can('contacts.edit')
            <a href="{{ route('contacts.edit', $contact) }}" class="btn-amber-app">Editar</a>
            @endcan
            <a href="{{ route('contacts.index') }}" class="btn-panel-dark bg-white/10 text-white border-2 border-[#FFE600] hover:bg-white/20">Volver</a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-6">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Información del Contacto</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><p class="text-sm text-white/70">Nombre Completo</p><p class="text-lg font-medium text-white">{{ $contact->nombre_completo }}</p></div>
                @if($contact->genero)<div><p class="text-sm text-white/70">Género</p><p class="text-lg font-medium text-white">{{ $contact->genero }}</p></div>@endif
                <div><p class="text-sm text-white/70">Empresa</p><p class="text-lg font-medium text-white">@if($contact->company)<a href="{{ route('companies.show', $contact->company) }}" class="text-[#FFE600] hover:text-white">{{ $contact->company->nombre_comercial }}</a>@else<span class="text-white/60">-</span>@endif</p></div>
                <div><p class="text-sm text-white/70">Puesto</p><p class="text-lg font-medium text-white">{{ $contact->puesto_de_trabajo ?? '-' }}</p></div>
                <div><p class="text-sm text-white/70">Departamento</p><p class="text-lg font-medium text-white">{{ $contact->departamento ?? '-' }}</p></div>
                <div><p class="text-sm text-white/70">Correo</p><p class="text-lg font-medium text-white">{{ $contact->email }}</p></div>
                <div><p class="text-sm text-white/70">Teléfono</p><p class="text-lg font-medium text-white">{{ $contact->telefono ?? '-' }}</p></div>
                <div><p class="text-sm text-white/70">Celular</p><p class="text-lg font-medium text-white">{{ $contact->celular ?? '-' }}</p></div>
                @if($contact->notas)
                <div class="md:col-span-2"><p class="text-sm text-white/70">Notas</p><p class="text-white">{{ $contact->notas }}</p></div>
                @endif
            </div>
        </div>
    </div>
</x-app-user-layout>
