<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Contactos</h2>
            <p class="page-header-card__subtitle">Directorio de contactos (consulta y captura)</p>
        </div>
        @can('contacts.create')
        <a href="{{ route('contacts.create') }}" class="btn-amber-app ml-auto">Nuevo Contacto</a>
        @endcan
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark rounded-lg border border-white/20 p-4">
            <p class="text-sm text-white/90">
                Verá los contactos <strong class="text-[#FFE600]">asignados por administración</strong> y los que <strong class="text-[#FFE600]">usted registre</strong>. Los que usted dé de alta quedan <strong class="text-white">pendientes</strong> hasta aprobación: no serán visibles para el resto del equipo hasta entonces.
            </p>
        </div>
        <div class="panel-card-dark overflow-hidden p-6">
            <form method="GET" action="{{ route('contacts.index') }}" class="mb-6 flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1 min-w-0">
                    <label for="search" class="block text-sm font-medium text-white/90 mb-1">Buscar por nombre de contacto</label>
                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nombre del contacto..."
                        list="contact_names_list"
                        class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3"
                    >
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('contacts.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/40 text-white/90 text-sm font-medium hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        Limpiar
                    </a>
                    <button type="submit" class="btn-primary-app">Buscar</button>
                </div>
            </form>
            @if(isset($contactNames) && $contactNames->isNotEmpty())
                <datalist id="contact_names_list">
                    @foreach($contactNames as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
            @endif
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] table-fixed divide-y divide-white/20">
                    <thead>
                        <tr class="table-header-panel-dark">
                            <th scope="col" class="crm-row-marker-head w-11 min-w-[2.75rem] px-1 py-3 text-center text-[10px] font-semibold uppercase tracking-wide text-[#FFE600]/90" title="Seguimiento personal (solo en este navegador)">Seg.</th>
                            <th class="w-[22%] min-w-[11rem] px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nombre</th>
                            <th class="w-[20%] min-w-[10rem] px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Empresa</th>
                            <th class="w-[11%] min-w-[6rem] px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                            <th class="w-[18%] min-w-[9rem] px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Correo</th>
                            <th class="w-[12%] min-w-[7rem] px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Teléfono</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($contacts as $contact)
                        <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                            <x-crm-row-marker entity="contact" :id="$contact->id" />
                            <td class="px-6 py-4 align-top whitespace-normal break-words">
                                <a href="{{ \App\Support\CrmNavigation::withReturn(route('contacts.show', $contact)) }}" title="Ver ficha del contacto" class="group block rounded-lg -m-1 p-1 min-w-0 hover:bg-white/[0.06] focus:outline-none focus:ring-2 focus:ring-[#FFE600]/35">
                                    <span class="block text-sm font-medium text-white group-hover:text-[#FFE600] group-hover:underline">{{ $contact->nombre_completo }}</span>
                                    @if(($contact->approval_status ?? '') === 'pendiente')
                                        <span class="mt-1 inline-block text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-md bg-amber-500/25 text-amber-200 border border-amber-400/40" title="Pendiente de aprobación por administrador">Pendiente</span>
                                    @endif
                                </a>
                            </td>
                            <td class="px-6 py-4 align-top whitespace-normal break-words text-sm text-white/90">
                                @if($contact->company)
                                    <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $contact->company)) }}" title="Ver ficha de la empresa" class="inline-block rounded-md -m-0.5 p-0.5 font-medium text-white hover:text-[#FFE600] hover:underline focus:outline-none focus:ring-2 focus:ring-[#FFE600]/35">{{ $contact->company->nombre_comercial }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top min-w-0 w-[11%] max-w-[8.5rem]">
                                <span class="inline-block max-w-full truncate px-2.5 py-1 text-xs font-medium rounded-lg align-middle badge-prospect-{{ $contact->status_color ?? 'seguimiento' }}" title="{{ $contact->status_label }}">{{ $contact->status_label_short }}</span>
                            </td>
                            <td class="px-6 py-4 align-top min-w-0 text-sm text-white/90 whitespace-normal break-words [overflow-wrap:anywhere]">
                                {{ ($contact->email_activo ?? true) ? ($contact->email ?? '—') : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $contact->celular ?? '-' }}</td>
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
