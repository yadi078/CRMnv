<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Empresas</h2>
            <p class="page-header-card__subtitle">Listado de empresas</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filtros + Nueva Empresa: contenedor azul, título amarillo -->
        <div class="panel-card-dark">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Filtros</h3>
            <form method="GET" action="{{ route('companies.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4 mb-0">
                <!-- Fila 1: buscador ancho -->
                <div class="md:col-span-12">
                    <label for="search" class="block text-sm font-medium text-white/90 mb-1">Buscar</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nombre de la empresa..."
                        list="company_names"
                        class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3"
                    >
                </div>

                <!-- Fila 2: cuatro selects -->
                <div class="md:col-span-3">
                    <label for="status_color" class="block text-sm font-medium text-white/90 mb-1">Estado prospecto</label>
                    <select id="status_color" name="status_color" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-[#1a3d6b] [&>option]:text-white">
                        <option value="">Todos los estados</option>
                        <option value="seguimiento" {{ request('status_color') === 'seguimiento' ? 'selected' : '' }}>Seguimiento</option>
                        <option value="interesado" {{ request('status_color') === 'interesado' ? 'selected' : '' }}>Interesado</option>
                        <option value="si_le_interesa_nos_llaman_o_no_compro" {{ request('status_color') === 'si_le_interesa_nos_llaman_o_no_compro' ? 'selected' : '' }}>Si le interesa nos llaman o no compro</option>
                        <option value="vendido" {{ request('status_color') === 'vendido' ? 'selected' : '' }}>Vendido</option>
                        <option value="no_estaba" {{ request('status_color') === 'no_estaba' ? 'selected' : '' }}>No estaba</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label for="sector" class="block text-sm font-medium text-white/90 mb-1">Sector</label>
                    <select id="sector" name="sector" class="w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3">
                        <option value="">Todos</option>
                        @foreach($sectorOptions as $sector)
                            <option value="{{ $sector }}" {{ request('sector') === $sector ? 'selected' : '' }}>{{ $sector }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label for="estado" class="block text-sm font-medium text-white/90 mb-1">Estado</label>
                    <select id="estado" name="estado" class="w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3">
                        <option value="">Todos</option>
                        @foreach($estadoOptions as $estado)
                            <option value="{{ $estado }}" {{ request('estado') === $estado ? 'selected' : '' }}>{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>
                @can('companies.approve')
                <div class="md:col-span-3">
                    <label for="approval_status" class="block text-sm font-medium text-white/90 mb-1">Estatus de aprobación</label>
                    <select id="approval_status" name="approval_status" class="w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3">
                        <option value="">Todos</option>
                        <option value="aprobado" {{ request('approval_status') === 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                        <option value="pendiente" {{ request('approval_status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="rechazado" {{ request('approval_status') === 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    </select>
                </div>
                @endcan

                <!-- Fila 3: ejecutivo + botones a la derecha -->
                <div class="md:col-span-4">
                    <label for="ejecutivo_asignado" class="block text-sm font-medium text-white/90 mb-1">Ejecutivo</label>
                    <select id="ejecutivo_asignado" name="ejecutivo_asignado" class="w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3">
                        <option value="">Todos</option>
                        @foreach($ejecutivoOptions as $ejecutivo)
                            <option value="{{ $ejecutivo }}" {{ request('ejecutivo_asignado') === $ejecutivo ? 'selected' : '' }}>{{ $ejecutivo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-8 flex flex-wrap justify-end items-end gap-3 pt-2">
                    @can('companies.create')
                    <a href="{{ route('companies.create') }}" class="btn-amber-app flex-shrink-0">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nueva Empresa
                    </a>
                    @endcan

                    <button type="submit" class="btn-panel-dark">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                        </svg>
                        Filtrar
                    </button>
                </div>
            </form>
            @if(isset($companyNames) && $companyNames->isNotEmpty())
                <datalist id="company_names">
                    @foreach($companyNames as $companyName)
                        <option value="{{ $companyName }}"></option>
                    @endforeach
                </datalist>
            @endif
        </div>

        @if(isset($companyContactsCard) && $companyContactsCard)
        <!-- Ficha de contactos de la empresa seleccionada -->
        <div class="panel-card-dark">
            <div class="mb-4 pb-4 border-b border-white/20">
                <h4 class="text-xl font-bold text-[#FFE600]">
                    {{ $companyContactsCard->nombre_comercial }}
                </h4>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-2 text-sm text-white/90">
                    @if($companyContactsCard->rfc)
                        <p><span class="text-white/70 font-medium">RFC:</span> {{ $companyContactsCard->rfc }}</p>
                    @endif
                    @if($companyContactsCard->municipio || $companyContactsCard->estado)
                        <p><span class="text-white/70 font-medium">Ciudad, Estado:</span> {{ trim(($companyContactsCard->municipio ?? '') . ', ' . ($companyContactsCard->estado ?? ''), ' ,') }}</p>
                    @endif
                    @if($companyContactsCard->sector)
                        <p><span class="text-white/70 font-medium">Sector:</span> {{ $companyContactsCard->sector }}</p>
                    @endif
                    @if($companyContactsCard->ejecutivo_asignado)
                        <p><span class="text-white/70 font-medium">Ejecutivo asignado:</span> {{ $companyContactsCard->ejecutivo_asignado }}</p>
                    @endif
                </div>
                @if($companyContactsCard->datos_fiscales)
                    <p class="mt-3 pt-3 border-t border-white/10 text-sm text-white/90"><span class="text-white/70 font-medium">Domicilio fiscal:</span> {{ Str::limit($companyContactsCard->datos_fiscales, 120) }}</p>
                @endif
            </div>

            @if($companyContactsCard->contacts->isEmpty())
                <p class="text-white/80 text-sm">
                    Esta empresa aún no tiene contactos registrados.
                </p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($companyContactsCard->contacts as $contact)
                        <a
                            href="{{ route('contacts.show', $contact) }}"
                            class="bg-white rounded-2xl shadow-md px-4 py-3 flex flex-col gap-1 border-2 border-[#071A3D] border-t-4 border-t-[#FFE600] transition transform hover:-translate-y-0.5 hover:shadow-lg cursor-pointer"
                        >
                            <span class="text-sm font-semibold text-[#071A3D]">
                                {{ $contact->nombre_completo }}
                            </span>
                            @if($contact->puesto_de_trabajo)
                                <p class="text-xs text-gray-600">
                                    {{ $contact->puesto_de_trabajo }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-700">
                                <span class="font-semibold">Correo:</span>
                                {{ ($contact->email_activo ?? true) ? ($contact->email ?? '—') : '—' }}
                            </p>
                            <p class="text-xs text-gray-700">
                                <span class="font-semibold">Teléfono:</span>
                                {{ $contact->celular ?? $contact->telefono ?? '—' }}
                            </p>
                            <div class="pt-2 mt-1 border-t border-gray-200">
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
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
        @endif

        <!-- Tabla: contenedor azul, encabezados amarillos, texto blanco -->
        <div class="panel-card-dark overflow-hidden">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Listado</h3>
            <div class="overflow-x-auto w-full min-w-0 -mx-4 sm:-mx-0" style="-webkit-overflow-scrolling: touch;">
                <table class="min-w-full divide-y divide-white/20">
                    <thead>
                        <tr class="table-header-panel-dark">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">RFC</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Ejecutivo</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($companies as $company)
                        <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 dot-prospect-{{ $company->status_color }}"></span>
                                    <div>
                                        <div class="text-sm font-medium text-white">{{ $company->nombre_comercial }}</div>
                                        @if($company->approval_status === 'pendiente')
                                        <span class="text-xs font-medium text-[#FCD34D] bg-amber-500/20 px-2 py-0.5 rounded-lg mt-0.5 inline-block">Pendiente</span>
                                        @elseif($company->approval_status === 'rechazado')
                                        <span class="text-xs font-medium text-red-300 bg-red-500/20 px-2 py-0.5 rounded-lg mt-0.5 inline-block">Rechazado</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $company->rfc ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-lg badge-prospect-{{ $company->status_color }}">
                                    {{ $company->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white/90">{{ $company->ejecutivo_asignado ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('companies.show', $company) }}" class="text-[#FFE600] hover:text-[#fff] mr-3 inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver
                                </a>
                                @can('companies.edit')
                                <a href="{{ route('companies.edit', $company) }}" class="text-[#FFE600] hover:text-[#fff] mr-3 inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                    Editar
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-white/70">No se encontraron empresas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-6 pt-4 border-t border-white/20">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
