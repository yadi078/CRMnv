<x-app-user-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
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
            <div class="panel-card-dark overflow-hidden p-6">
                <p class="text-white/90 text-sm mb-4">Las empresas que agregue quedarán en estado <span class="font-semibold text-[#FFE600]">Pendiente</span> hasta que un administrador las apruebe. Cada empresa tiene un estado de prospecto (Seguimiento, Interesado, Vendido, etc.) para llevar el avance en el proceso comercial.</p>
                <form method="GET" action="{{ route('companies.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-12 gap-4">
                    <!-- Fila 1: buscador ancho -->
                    <div class="md:col-span-12">
                        <label for="search" class="block text-sm font-medium text-white/90 mb-1">Buscar</label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Buscar por nombre, RFC o ejecutivo..."
                            class="w-full rounded-xl border-0 bg-white/15 text-white placeholder-white/60 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3"
                        >
                    </div>

                    <!-- Fila 2: cuatro selects -->
                    <div class="md:col-span-3">
                        <label for="status_color" class="block text-sm font-medium text-white/90 mb-1">Estado prospecto</label>
                        <select name="status_color" id="status_color" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-[#1a3d6b] [&>option]:text-white">
                            <option value="">Todos los estados</option>
                            @foreach(\App\Models\Company::PROSPECT_STATUS_LABELS as $value => $label)
                                <option value="{{ $value }}" {{ request('status_color') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label for="sector" class="block text-sm font-medium text-white/90 mb-1">Sector</label>
                        <select name="sector" id="sector" class="w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3">
                            <option value="">Todos los sectores</option>
                            @foreach($sectorOptions as $sector)
                                <option value="{{ $sector }}" {{ request('sector') === $sector ? 'selected' : '' }}>{{ $sector }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label for="estado" class="block text-sm font-medium text-white/90 mb-1">Estado</label>
                        <select name="estado" id="estado" class="w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3">
                            <option value="">Todos los estados</option>
                            @foreach($estadoOptions as $estado)
                                <option value="{{ $estado }}" {{ request('estado') === $estado ? 'selected' : '' }}>{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label for="ejecutivo_asignado" class="block text-sm font-medium text-white/90 mb-1">Ejecutivo</label>
                        <select name="ejecutivo_asignado" id="ejecutivo_asignado" class="w-full rounded-xl border border-gray-200 bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3">
                            <option value="">Todos los ejecutivos</option>
                            @foreach($ejecutivoOptions as $ejecutivo)
                                <option value="{{ $ejecutivo }}" {{ request('ejecutivo_asignado') === $ejecutivo ? 'selected' : '' }}>{{ $ejecutivo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Fila 3: solo botón filtrar alineado a la derecha -->
                    <div class="md:col-span-12 flex justify-end gap-3 pt-1">
                        <button type="submit" class="btn-primary-app">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                            </svg>
                            Filtrar
                        </button>
                    </div>
                </form>

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
