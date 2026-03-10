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
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-6 text-xl">Información del Contacto</h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Datos de identidad --}}
                <div class="space-y-4 border border-white/10 rounded-xl p-4 bg-white/5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-400/10 text-amber-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-base text-white/70">Nombre Completo</p>
                            <p class="text-xl font-semibold text-white">{{ $contact->nombre_completo }}</p>
                        </div>
                    </div>

                    @if($contact->genero)
                    <div class="flex items-start justify-between gap-3 border-t border-white/10 pt-3">
                        <div>
                            <p class="text-base text-white/70">Género</p>
                            <p class="text-lg font-medium text-white">{{ $contact->genero }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-start justify-between gap-3 border-t border-white/10 pt-3">
                        <div>
                            <p class="text-base text-white/70">Departamento</p>
                            <p class="text-lg font-medium text-white">{{ $contact->departamento ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Datos de la empresa --}}
                <div class="space-y-4 border border-white/10 rounded-xl p-4 bg-white/5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-400/10 text-amber-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10l9 4 9-4V7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-base text-white/70">Empresa</p>
                            <p class="text-lg font-semibold text-white">
                                @if($contact->company)
                                    <a href="{{ route('companies.show', $contact->company) }}" class="text-[#FFE600] hover:text-white underline underline-offset-4">
                                        {{ $contact->company->nombre_comercial }}
                                    </a>
                                @else
                                    <span class="text-white/60">-</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start justify-between gap-3 border-t border-white/10 pt-3">
                        <div>
                            <p class="text-base text-white/70">Puesto</p>
                            <p class="text-lg font-medium text-white">{{ $contact->puesto_de_trabajo ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Datos de contacto --}}
                <div class="lg:col-span-2 space-y-4 border border-white/10 rounded-xl p-4 bg-white/5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-400/10 text-emerald-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12V8m0 0l-2 2m2-2l2 2M8 12v4m0 0l2-2m-2 2l-2-2" />
                            </svg>
                        </div>
                        <div class="w-full">
                            <p class="text-base text-white/70">Correo</p>
                            <div class="flex flex-wrap items-center gap-4 mt-1">
                                <p class="text-lg font-medium text-white">
                            {{ $contact->email_activo ? $contact->email : 'Correo desactivado' }}
                                </p>
                                @can('contacts.edit')
                                <form method="POST" action="{{ route('contacts.email-status', $contact) }}" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="email_activo" value="{{ $contact->email_activo ? 0 : 1 }}">
                                    <button type="submit" class="relative inline-flex items-center h-7 w-11 rounded-full transition-colors shadow-sm {{ $contact->email_activo ? 'bg-green-500' : 'bg-gray-300' }}">
                                        <span class="inline-block w-4 h-4 bg-white rounded-full transform transition-transform duration-200 {{ $contact->email_activo ? 'translate-x-4' : '' }}"></span>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-white/10 pt-4 mt-2">
                        <div>
                            <p class="text-base text-white/70">Teléfono</p>
                            <p class="text-lg font-medium text-white">{{ $contact->telefono ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-base text-white/70">Celular</p>
                            <p class="text-lg font-medium text-white">{{ $contact->celular ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Notas --}}
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm text-white/70">Notas</p>
                        @can('contacts.edit')
                        <button
                            type="button"
                            onclick="document.getElementById('user-notas-view').classList.add('hidden'); document.getElementById('user-notas-form').classList.remove('hidden');"
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full border border-white/30 bg-white/5 text-white hover:bg-white/15 transition"
                            title="Editar notas"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        @endcan
                    </div>
                    <div id="user-notas-view" class="text-white text-sm {{ $contact->notas ? '' : 'text-white/60 italic' }}">
                        {{ $contact->notas ?: 'Sin notas registradas.' }}
                    </div>
                    @can('contacts.edit')
                    <form
                        id="user-notas-form"
                        method="POST"
                        action="{{ route('contacts.notes', $contact) }}"
                        class="hidden space-y-2"
                    >
                        @csrf
                        @method('PATCH')
                        <textarea
                            name="notas"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm"
                        >{{ old('notas', $contact->notas) }}</textarea>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="btn-amber-app text-xs py-1.5 px-3">Guardar notas</button>
                            <button
                                type="button"
                                class="text-xs text-white/80 hover:text-white"
                                onclick="document.getElementById('user-notas-form').classList.add('hidden'); document.getElementById('user-notas-view').classList.remove('hidden');"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-user-layout>
