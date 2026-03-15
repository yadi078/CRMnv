<x-app-user-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Contactos</h2>
            <p class="page-header-card__subtitle">Directorio de contactos (consulta y captura)</p>
        </div>
        @can('contacts.create')
        <a href="{{ route('contacts.create') }}" class="btn-amber-app ml-auto">Nuevo Contacto</a>
        @endcan
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark overflow-hidden p-6">
            <form method="GET" action="{{ route('contacts.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Fila 1: buscador ancho -->
                <div class="md:col-span-12">
                    <label for="search" class="block text-sm font-medium text-white/90 mb-1">Buscar</label>
                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nombre del contacto..."
                        list="contact_names"
                        class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3"
                    >
                </div>

                <!-- Fila 2: cuatro filtros principales -->
                <div class="md:col-span-3">
                    <label for="company_id" class="block text-sm font-medium text-white/90 mb-1">Empresa</label>
                    <select
                        name="company_id"
                        id="company_id"
                        class="w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3"
                    >
                        <option value="">Todas las empresas</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->nombre_comercial }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label for="status_color" class="block text-sm font-medium text-white/90 mb-1">Estado prospecto</label>
                    <select name="status_color" id="status_color" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-[#1a3d6b] [&>option]:text-white">
                        <option value="">Estado prospecto</option>
                        @foreach(\App\Models\Contact::PROSPECT_STATUS_LABELS as $value => $label)
                        <option value="{{ $value }}" {{ request('status_color') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label for="genero" class="block text-sm font-medium text-white/90 mb-1">Género</label>
                    <select name="genero" id="genero" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-[#1a3d6b] [&>option]:text-white">
                        <option value="">Género</option>
                        <option value="Masculino" {{ request('genero') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Femenino" {{ request('genero') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                        <option value="Otro" {{ request('genero') === 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label for="email_activo" class="block text-sm font-medium text-white/90 mb-1">Correo</label>
                    <select name="email_activo" id="email_activo" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-[#1a3d6b] [&>option]:text-white">
                        <option value="">Correo</option>
                        <option value="1" {{ request('email_activo') === '1' ? 'selected' : '' }}>Solo activos</option>
                        <option value="0" {{ request('email_activo') === '0' ? 'selected' : '' }}>Solo desactivados</option>
                    </select>
                </div>

                <!-- Fila 3: ciudad/estado + botón -->
                <div class="md:col-span-4">
                    <label for="municipio" class="block text-sm font-medium text-white/90 mb-1">Municipio / Ciudad</label>
                    <input
                        id="municipio"
                        type="text"
                        name="municipio"
                        value="{{ request('municipio') }}"
                        placeholder="Municipio / Ciudad"
                        class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3"
                    >
                </div>
                <div class="md:col-span-4">
                    <label for="estado" class="block text-sm font-medium text-white/90 mb-1">Estado</label>
                    <input
                        id="estado"
                        type="text"
                        name="estado"
                        value="{{ request('estado') }}"
                        placeholder="Estado"
                        class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3"
                    >
                </div>
                <div class="md:col-span-4 flex justify-end items-end pt-1">
                    <button type="submit" class="btn-primary-app">Filtrar</button>
                </div>
            </form>
            @if(!empty($contactNames))
                <datalist id="contact_names">
                    @foreach($contactNames as $contactName)
                        <option value="{{ $contactName }}"></option>
                    @endforeach
                </datalist>
            @endif
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/20">
                    <thead>
                        <tr class="table-header-panel-dark">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Empresa</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Correo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($contacts as $contact)
                        <tr class="panel-card-dark__row hover:bg:white/8 transition-colors">
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
                                <a href="{{ route('contacts.show', $contact) }}" class="text-[#FFE600] hover:text-[#fff] mr-3 inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Ver
                                </a>
                                @can('contacts.generate-pdf')
                                <a href="{{ route('contacts.pdf', $contact) }}" class="text-red-400 hover:text-red-300 mr-3 inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                    PDF
                                </a>
                                <a href="{{ route('contacts.word', $contact) }}" class="text-blue-300 hover:text-blue-200 inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16v16H4z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 8l2 8 3-6 3 6 2-8" /></svg>
                                    Word
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-white/80">No se encontraron contactos</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">{{ $contacts->links() }}</div>
        </div>
    </div>
</x-app-user-layout>
