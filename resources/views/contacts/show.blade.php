<x-app-layout>
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
                @can('contacts.generate-pdf')
                <a href="{{ route('contacts.pdf', $contact) }}" class="btn-icon-text px-4 py-2 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Generar PDF
                </a>
                @endcan
                @can('contacts.edit')
                <a href="{{ route('contacts.edit', $contact) }}" class="btn-amber-app">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                @endcan
                <a href="{{ route('contacts.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-[#FFE600] bg-white/10 text-white font-medium hover:bg-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Volver
                </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-6">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Información del Contacto</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-white/70">Nombre Completo</p>
                    <p class="text-lg font-medium text-white">{{ $contact->nombre_completo }}</p>
                    </div>
                @if($contact->genero)
                <div>
                    <p class="text-sm text-white/70">Género</p>
                    <p class="text-lg font-medium text-white">{{ $contact->genero }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-white/70">Empresa</p>
                    <p class="text-lg font-medium text-white">
                        @if($contact->company)
                        <a href="{{ route('companies.show', $contact->company) }}" class="text-[#FFE600] hover:text-white">
                            {{ $contact->company->nombre_comercial }}
                        </a>
                        @else
                        <span class="text-white/60">-</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-white/70">Puesto de Trabajo</p>
                    <p class="text-lg font-medium text-white">{{ $contact->puesto_de_trabajo ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70">Departamento</p>
                    <p class="text-lg font-medium text-white">{{ $contact->departamento ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70">Correo</p>
                    <p class="text-lg font-medium text-white">{{ $contact->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70">Teléfono</p>
                    <p class="text-lg font-medium text-white">{{ $contact->telefono ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-white/70">Celular</p>
                    <p class="text-lg font-medium text-white">{{ $contact->celular ?? '-' }}</p>
                </div>
                @if($contact->extension)
                <div>
                    <p class="text-sm text-white/70">Extensión</p>
                    <p class="text-lg font-medium text-white">{{ $contact->extension }}</p>
                </div>
                @endif
                @if($contact->municipio || $contact->estado)
                <div>
                    <p class="text-sm text-white/70">Ubicación</p>
                    <p class="text-lg font-medium text-white">{{ $contact->municipio ?? '' }}{{ $contact->municipio && $contact->estado ? ', ' : '' }}{{ $contact->estado ?? '' }}</p>
                </div>
                @endif
                @if($contact->notas)
                <div class="md:col-span-2">
                    <p class="text-sm text-white/70">Notas</p>
                    <p class="text-white">{{ $contact->notas }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
