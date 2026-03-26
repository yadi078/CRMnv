<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Empresas</h2>
            <p class="page-header-card__subtitle">Consultar y agregar empresas (solo aprobadas visibles aquí)</p>
        </div>
        @can('companies.create')
        <a href="{{ route('companies.create') }}" class="btn-amber-app ml-auto">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva Empresa
        </a>
        @endcan
    </x-slot>

    <div class="space-y-8">
            @if(isset($misPendientes) && $misPendientes > 0)
            <div class="panel-card-dark rounded-lg border-2 border-[#FFE600]/50 p-4">
                <p class="text-sm text-white">
                    <strong>{{ $misPendientes }}</strong> empresa(s) en estado <strong>Pendiente</strong> de aprobación. Se reflejarán en el sistema cuando un administrador las apruebe.
                </p>
            </div>
            @endif
            @if(isset($misEliminacionesPendientes) && $misEliminacionesPendientes > 0)
            <div class="panel-card-dark rounded-lg border-2 border-red-500/40 p-4">
                <p class="text-sm text-white">
                    <strong>{{ $misEliminacionesPendientes }}</strong> solicitud(es) de <strong>eliminación de empresa</strong> en revisión por un administrador.
                </p>
            </div>
            @endif
            <div class="panel-card-dark overflow-hidden p-6">
                <p class="text-white/90 text-sm mb-4">Las empresas que agregue quedarán en estado <span class="font-semibold text-[#FFE600]">Pendiente</span> hasta que un administrador las apruebe. Cada empresa tiene un estado de prospecto (Seguimiento, Interesado, Vendido, etc.) para llevar el avance en el proceso comercial.</p>
                <form method="GET" action="{{ route('companies.index') }}" class="mb-6 flex flex-col sm:flex-row sm:items-end gap-3">
                    <div class="flex-1 min-w-0">
                        <label for="search" class="block text-sm font-medium text-white/90 mb-1">Buscar por nombre de empresa</label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Nombre de la empresa..."
                            class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3"
                        >
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/40 text-white/90 text-sm font-medium hover:bg-white/10 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            Limpiar
                        </a>
                        <button type="submit" class="btn-primary-app">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                            </svg>
                            Buscar
                        </button>
                    </div>
                </form>

                @if(isset($companyContactsCard) && $companyContactsCard)
                <div class="mb-8 pb-8 border-b border-white/20">
                    <p class="text-white/70 text-xs mb-4">
                        Resultado único: datos de la empresa y <strong class="text-white/90">sus contactos que usted agregó</strong> (aprobados), con los <strong class="text-white/90">seguimientos</strong> que creó o tiene asignados.
                    </p>
                    <div class="rounded-xl border border-white/20 bg-white/5 p-4 sm:p-5">
                        <h4 class="text-lg font-bold text-[#FFE600] mb-3">
                            {{ $companyContactsCard->nombre_comercial }}
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-2 text-sm text-white/90 mb-4">
                            @if($companyContactsCard->rfc)
                                <p><span class="text-white/60 font-medium">RFC:</span> {{ $companyContactsCard->rfc }}</p>
                            @endif
                            @if($companyContactsCard->municipio || $companyContactsCard->estado)
                                <p><span class="text-white/60 font-medium">Ciudad, Estado:</span> {{ trim(($companyContactsCard->municipio ?? '') . ', ' . ($companyContactsCard->estado ?? ''), ' ,') }}</p>
                            @endif
                            @if($companyContactsCard->sector)
                                <p><span class="text-white/60 font-medium">Sector:</span> {{ $companyContactsCard->sector }}</p>
                            @endif
                            @if($companyContactsCard->ejecutivo_asignado)
                                <p><span class="text-white/60 font-medium">Ejecutivo asignado:</span> {{ $companyContactsCard->ejecutivo_asignado }}</p>
                            @endif
                        </div>
                        @if($companyContactsCard->datos_fiscales)
                            <p class="text-sm text-white/85 mb-4 pb-4 border-b border-white/10">
                                <span class="text-white/60 font-medium">Domicilio fiscal:</span> {{ \Illuminate\Support\Str::limit($companyContactsCard->datos_fiscales, 120) }}
                            </p>
                        @endif

                        @if($companyContactsCard->contacts->isEmpty())
                            <p class="text-white/80 text-sm">
                                Usted no tiene contactos <strong class="text-white">aprobados</strong> registrados para esta empresa, o aún están pendientes de aprobación.
                            </p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($companyContactsCard->contacts as $contact)
                                    <div class="bg-white rounded-2xl shadow-md px-4 py-3 flex flex-col gap-2 border-2 border-[#071A3D] border-t-4 border-t-[#FFE600]">
                                        <a href="{{ route('contacts.show', $contact) }}" class="text-sm font-semibold text-[#071A3D] hover:text-[#0f172a] hover:underline">
                                            {{ $contact->nombre_completo }}
                                        </a>
                                        @if($contact->puesto_de_trabajo)
                                            <p class="text-xs text-gray-600">{{ $contact->puesto_de_trabajo }}</p>
                                        @endif
                                        <p class="text-xs text-gray-700">
                                            <span class="font-semibold">Correo:</span>
                                            {{ ($contact->email_activo ?? true) ? ($contact->email ?? '—') : '—' }}
                                        </p>
                                        <p class="text-xs text-gray-700">
                                            <span class="font-semibold">Teléfono:</span>
                                            {{ $contact->celular ?? $contact->telefono ?? '—' }}
                                        </p>
                                        <div class="pt-2 border-t border-gray-200">
                                            @if($contact->status_color)
                                                <span class="px-3 py-1 text-xs font-bold rounded-full badge-prospect-{{ $contact->status_color }} border border-[#071A3D]/50 shrink-0 inline-block !text-[#0f172a]">
                                                    {{ $contact->status_label }}
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-300 !text-[#0f172a] border border-[#071A3D]/50 shrink-0 inline-block">
                                                    Seguimiento
                                                </span>
                                            @endif
                                        </div>
                                        <div class="pt-2 mt-1 border-t border-gray-200">
                                            <p class="text-xs font-semibold text-[#071A3D] mb-2">Seguimientos</p>
                                            @forelse($contact->followUps as $followUp)
                                                <a href="{{ route('follow-ups.show', $followUp) }}" class="block text-xs text-gray-700 hover:text-[#071A3D] py-1.5 border-b border-gray-100 last:border-0">
                                                    <span class="font-medium">{{ $followUp->fecha_alarma?->format('d/m/Y H:i') }}</span>
                                                    <span class="text-gray-500"> · {{ ucfirst($followUp->tipo_accion) }}</span>
                                                    @if($followUp->completado)
                                                        <span class="ml-1 text-green-700 font-medium">(Completado)</span>
                                                    @elseif(method_exists($followUp, 'estaVencido') && $followUp->estaVencido())
                                                        <span class="ml-1 text-red-600 font-medium">(Vencido)</span>
                                                    @else
                                                        <span class="ml-1 text-amber-700 font-medium">(Pendiente)</span>
                                                    @endif
                                                </a>
                                            @empty
                                                <p class="text-xs text-gray-500">Sin seguimientos que pueda ver para este contacto.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/20">
                        <thead>
                            <tr class="table-header-panel-dark">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">RFC</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Ejecutivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/15">
                            @forelse($companies as $company)
                            <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-2 dot-prospect-{{ $company->status_color }}"></div>
                                        <div class="text-sm font-medium text-white">{{ $company->nombre_comercial }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $company->rfc ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded badge-prospect-{{ $company->status_color }}">
                                        {{ $company->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $company->ejecutivo_asignado ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('companies.show', $company) }}" class="text-[#FFE600] hover:text-white mr-3">Ver</a>
                                    @can('companies.edit')
                                    <a href="{{ route('companies.edit', $company) }}" class="text-[#FFE600] hover:text-white mr-3">Editar</a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-white/80">No hay empresas aprobadas para mostrar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 pt-4 border-t border-white/20">{{ $companies->links() }}</div>
            </div>
    </div>
</x-app-user-layout>
