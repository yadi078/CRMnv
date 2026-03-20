<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Contactos</h2>
            <p class="page-header-card__subtitle">Listado de contactos</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <form method="GET" action="{{ route('contacts.index') }}" class="flex flex-col sm:flex-row sm:items-end gap-3 flex-1 min-w-0">
                    <div class="flex-1 min-w-0 w-full">
                        <label for="search" class="block text-sm font-semibold text-white/90 mb-2">Buscar por nombre de contacto</label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Nombre del contacto..."
                            list="contact_names_list"
                            class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 text-sm"
                        >
                    </div>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <a href="{{ route('contacts.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/30 text-white/90 text-sm font-medium hover:bg-white/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Limpiar
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#FFE600] text-[#071A3D] text-sm font-semibold shadow-md hover:bg-[#ffeb3b]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                            </svg>
                            Buscar
                        </button>
                        @can('contacts.create')
                        <a href="{{ route('contacts.create') }}" class="btn-amber-app flex-shrink-0">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Nuevo Contacto
                        </a>
                        @endcan
                    </div>
                </form>
            </div>
            @if(isset($contactNames) && $contactNames->isNotEmpty())
                <datalist id="contact_names_list">
                    @foreach($contactNames as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
            @endif
        </div>

        <div class="panel-card-dark overflow-hidden">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Listado</h3>
            <div class="overflow-x-auto w-full min-w-0 -mx-4 sm:-mx-0" style="-webkit-overflow-scrolling: touch;">
                <table class="min-w-full divide-y divide-white/20">
                    <thead>
                        <tr class="table-header-panel-dark">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Empresa</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Correo</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($contacts as $contact)
                        <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ $contact->nombre_completo }}</div>
                                <div class="text-sm text-white/80">{{ $contact->puesto_de_trabajo ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $contact->company?->nombre_comercial ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-lg badge-prospect-{{ $contact->status_color ?? 'seguimiento' }}">{{ $contact->status_label ?? 'Seguimiento' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">
                                {{ ($contact->email_activo ?? true) ? ($contact->email ?? '—') : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $contact->celular ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('contacts.show', $contact) }}" class="text-[#FFE600] hover:text-white inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        Ver
                                    </a>
                                    @can('delete', $contact)
                                    <form method="POST" action="{{ route('contacts.destroy', $contact) }}" class="inline-flex items-center gap-1"
                                        onsubmit="return confirm('¿Eliminar el contacto \'{{ addslashes($contact->nombre_completo) }}\'? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-200 inline-flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v2H9V4a1 1 0 011-1z" /></svg>
                                            Eliminar
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-white/70">No se encontraron contactos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6 pt-4 border-t border-white/20">
                {{ $contacts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
