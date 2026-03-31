<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Empresas</h2>
            <p class="page-header-card__subtitle">Listado de empresas</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="panel-card-dark">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-4">
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-white/90 mb-2">Buscar empresa</h3>
                    <form method="GET" action="{{ route('companies.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <label for="search" class="sr-only">Nombre de la empresa</label>
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Nombre de la empresa..."
                                list="company_names"
                                class="w-full rounded-xl border-0 bg-white/15 text-white text-sm py-2.5 px-3 placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50"
                            >
                        </div>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                            <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/30 text-white/90 text-sm font-medium hover:bg-white/10">
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
                            @can('companies.create')
                            <a href="{{ route('companies.create') }}" class="btn-amber-app flex-shrink-0">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Nueva Empresa
                            </a>
                            @endcan
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
                @can('companies.create')
                <form action="{{ route('companies.import') }}" method="POST" enctype="multipart/form-data" class="flex-shrink-0">
                    @csrf
                    <label class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-white/10 border border-white/30 text-xs text-white/90 cursor-pointer hover:bg-white/15">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16v12H4zM4 16l4 4m0 0h8m-8 0l4-4" />
                        </svg>
                        <span>Cargar base (Excel)</span>
                        <input type="file" name="file" class="hidden" accept=".xlsx,.xls,.csv" onchange="this.form.submit()">
                    </label>
                </form>
                @endcan
            </div>
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
            <div class="scroll-x-top w-full min-w-0 -mx-4 sm:-mx-0" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full min-w-[944px] table-fixed divide-y divide-white/20">
                    <thead>
                        <tr class="table-header-panel-dark">
                            <th scope="col" class="crm-row-marker-head w-11 min-w-[2.75rem] px-1 py-3.5 text-center text-[10px] font-semibold uppercase tracking-wide text-[#FFE600]/90" title="Seguimiento personal (solo en este navegador)">Seg.</th>
                            <th class="w-[18rem] px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Nombre</th>
                            <th class="w-[12rem] px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">RFC</th>
                            <th class="w-[10rem] px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Estatus</th>
                            <th class="w-[12rem] px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Ejecutivo</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/15">
                        @forelse($companies as $company)
                        <tr class="panel-card-dark__row hover:bg-white/8 transition-colors">
                            <td class="px-6 py-4 align-top whitespace-normal break-words">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 dot-prospect-{{ $company->status_color }}"></span>
                                    <div class="min-w-0">
                                        <a href="{{ route('companies.show', $company) }}" class="inline-block text-sm font-medium text-white hover:text-[#FFE600] hover:underline focus:outline-none focus:ring-2 focus:ring-[#FFE600]/35 rounded">{{ $company->nombre_comercial }}</a>
                                        @if($company->approval_status === 'pendiente')
                                        <span class="text-xs font-medium text-[#FCD34D] bg-amber-500/20 px-2 py-0.5 rounded-lg mt-0.5 inline-block">Pendiente</span>
                                        @elseif($company->approval_status === 'rechazado')
                                        <span class="text-xs font-medium text-red-300 bg-red-500/20 px-2 py-0.5 rounded-lg mt-0.5 inline-block">Rechazado</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top whitespace-normal break-words text-sm text-white/90">{{ $company->rfc ?? '-' }}</td>
                            <td class="px-6 py-4 align-top whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-lg badge-prospect-{{ $company->status_color }}">
                                    {{ $company->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top whitespace-normal break-words text-sm text-white/90">{{ $company->ejecutivo_asignado ?? '-' }}</td>
                            <td class="px-6 py-4 align-top whitespace-nowrap text-sm font-medium">
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

                                @can('companies.delete')
                                <form action="{{ route('companies.destroy', $company) }}" method="POST" class="inline-flex">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="text-red-300 hover:text-red-200 mr-3 inline-flex items-center gap-1"
                                        onclick="return confirm('¿Seguro que deseas eliminar esta empresa?')"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h10a1 1 0 00-1-1h-3V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v2H9a1 1 0 00-1 1z" />
                                        </svg>
                                        Eliminar
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-white/70">No se encontraron empresas</td>
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
