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
        {{-- Filtros avanzados estilo tablero (similar a Empresas) --}}
        <div class="panel-card-dark">
            @php
                $chips = [];
                if(request('search')) $chips[] = 'Nombre: ' . request('search');
                if(request('empresa')) $chips[] = 'Empresa: ' . request('empresa');
                if(request('municipio')) $chips[] = 'Ciudad: ' . request('municipio');
                if(request('estado')) $chips[] = 'Estado: ' . request('estado');
                if(request('genero')) $chips[] = 'Género: ' . request('genero');
                if(request('status_color')) $chips[] = 'Estado prospecto';
                if(request('tiene_telefono')) $chips[] = 'Tiene teléfono: ' . request('tiene_telefono');
                if(request('telefono_exacto')) $chips[] = 'Teléfono: ' . request('telefono_exacto');
                if(request('tiene_celular')) $chips[] = 'Tiene celular: ' . request('tiene_celular');
                if(request('celular_exacto')) $chips[] = 'Celular: ' . request('celular_exacto');
                if(request('no_desea_correos')) $chips[] = 'No desea correos: ' . request('no_desea_correos');
            @endphp

            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-white/90">Filtros avanzados</span>
                    <div class="flex flex-wrap gap-2">
                        @forelse($chips as $chip)
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 text-xs text-white/90 border border-white/20">
                                {{ $chip }}
                            </span>
                        @empty
                            <span class="text-xs text-white/60">Sin filtros aplicados</span>
                        @endforelse
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('contacts.index') }}" class="text-xs text-white/70 hover:text-[#FFE600] inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Limpiar filtros
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('contacts.index') }}" class="space-y-4">
                <!-- Datos de contacto -->
                <div class="bg-white/5 rounded-2xl px-4 py-4">
                    <h4 class="text-sm font-semibold text-white/90 mb-3">Datos de contacto</h4>
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-white/80 mb-1">Nombre</label>
                            <div class="flex gap-2">
                                <select name="nombre_op" class="rounded-xl border-0 bg-white/15 text-white py-2 px-2 text-xs flex-shrink-0 w-28">
                                    <option value="contiene" {{ request('nombre_op') === 'contiene' ? 'selected' : '' }}>Contiene</option>
                                    <option value="exacto" {{ request('nombre_op') === 'exacto' ? 'selected' : '' }}>Exacto</option>
                                    <option value="empieza" {{ request('nombre_op') === 'empieza' ? 'selected' : '' }}>Empieza</option>
                                    <option value="termina" {{ request('nombre_op') === 'termina' ? 'selected' : '' }}>Termina</option>
                                </select>
                                <input
                                    type="text"
                                    id="search"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Nombre del contacto..."
                                    list="contact_names"
                                    class="flex-1 rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3 text-xs"
                                >
                            </div>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-medium text-white/80 mb-1">Orden nombre</label>
                            <select name="nombre_orden" class="w-full rounded-xl border-0 bg-white/15 text-white text-xs py-2 px-2">
                                <option value="">Ninguno</option>
                                <option value="az" {{ request('nombre_orden') === 'az' ? 'selected' : '' }}>A-Z</option>
                                <option value="za" {{ request('nombre_orden') === 'za' ? 'selected' : '' }}>Z-A</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-white/80 mb-1">Empresa</label>
                            <input
                                type="text"
                                id="empresa"
                                name="empresa"
                                value="{{ request('empresa') }}"
                                placeholder="Nombre de la empresa..."
                                class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3 text-xs"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-white/80 mb-1">Estado</label>
                            <input type="text" id="estado" name="estado" value="{{ request('estado') }}" class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3 text-xs" placeholder="Ej. Jalisco">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-white/80 mb-1">Ciudad</label>
                            <input type="text" id="municipio" name="municipio" value="{{ request('municipio') }}" class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2 px-3 text-xs" placeholder="Ej. Guadalajara">
                        </div>
                    </div>
                </div>

                <!-- Estado comercial -->
                <div class="bg-white/5 rounded-2xl px-4 py-4">
                    <h4 class="text-sm font-semibold text-white/90 mb-3">Estado comercial</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-white/80 mb-1">Estado prospecto</label>
                            <select id="status_color" name="status_color" class="w-full rounded-xl border-0 bg-white/15 text-white text-xs py-2 px-3 [&>option]:bg-[#1a3d6b]">
                                <option value="">Todos</option>
                                @foreach(\App\Models\Contact::PROSPECT_STATUS_LABELS as $value => $label)
                                    <option value="{{ $value }}" {{ request('status_color') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contacto disponible -->
                <div class="bg-white/5 rounded-2xl px-4 py-4">
                    <h4 class="text-sm font-semibold text-white/90 mb-3">Contacto disponible</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-white/80 mb-1">Género</label>
                            <select id="genero" name="genero" class="w-full rounded-xl border-0 bg-white/15 text-white text-xs py-2 px-3 [&>option]:bg-[#1a3d6b]">
                                <option value="">Todos</option>
                                <option value="Masculino" {{ request('genero') === 'Masculino' ? 'selected' : '' }}>Hombre</option>
                                <option value="Femenino" {{ request('genero') === 'Femenino' ? 'selected' : '' }}>Mujer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-white/80 mb-1">Tiene teléfono</label>
                            <select name="tiene_telefono" class="w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] text-xs py-2 px-2">
                                <option value="">Todos</option>
                                <option value="si" {{ request('tiene_telefono') === 'si' ? 'selected' : '' }}>Sí</option>
                                <option value="no" {{ request('tiene_telefono') === 'no' ? 'selected' : '' }}>No</option>
                            </select>
                            <select name="telefono_exacto" class="mt-1 w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] text-xs py-2 px-2">
                                <option value="">Todos los números</option>
                                @foreach(($telefonos ?? []) as $tel)
                                    <option value="{{ $tel }}" {{ request('telefono_exacto') === $tel ? 'selected' : '' }}>{{ $tel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-white/80 mb-1">Tiene celular</label>
                            <select name="tiene_celular" class="w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] text-xs py-2 px-2">
                                <option value="">Todos</option>
                                <option value="si" {{ request('tiene_celular') === 'si' ? 'selected' : '' }}>Sí</option>
                                <option value="no" {{ request('tiene_celular') === 'no' ? 'selected' : '' }}>No</option>
                            </select>
                            <select name="celular_exacto" class="mt-1 w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] text-xs py-2 px-2">
                                <option value="">Todos los celulares</option>
                                @foreach(($celulares ?? []) as $cel)
                                    <option value="{{ $cel }}" {{ request('celular_exacto') === $cel ? 'selected' : '' }}>{{ $cel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-white/80 mb-1">No desea correos</label>
                            <select name="no_desea_correos" class="w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] text-xs py-2 px-2">
                                <option value="">Todos</option>
                                <option value="si" {{ request('no_desea_correos') === 'si' ? 'selected' : '' }}>Sí</option>
                                <option value="no" {{ request('no_desea_correos') === 'no' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('contacts.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/30 text-white/90 text-sm font-medium hover:bg-white/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Limpiar filtros
                        </a>
                    </div>
                    <div class="flex items-center gap-3">
                        @can('contacts.create')
                        <a href="{{ route('contacts.create') }}" class="btn-amber-app flex-shrink-0">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Nuevo Contacto
                        </a>
                        @endcan
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#FFE600] text-[#071A3D] text-sm font-semibold shadow-md hover:bg-[#ffeb3b]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                            </svg>
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>
            @if(!empty($contactNames))
                <datalist id="contact_names">
                    @foreach($contactNames as $contactName)
                        <option value="{{ $contactName }}"></option>
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
                                    @can('contacts.generate-pdf')
                                    <a href="{{ route('contacts.pdf', $contact) }}" class="text-green-400 hover:text-green-200 inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                        PDF
                                    </a>
                                    @endcan
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
