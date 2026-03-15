<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M5 10h14M9 16h6m-3 4v-4" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Filtros</h2>
            <p class="page-header-card__subtitle">Filtros avanzados por contactos y empresas</p>
        </div>
        <a href="{{ route('filtros.index') }}" class="ml-auto inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/40 text-white/90 text-sm font-medium hover:bg-white/10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            Limpiar filtros
        </a>
    </x-slot>

    <div class="space-y-6">
        {{-- Tabs --}}
        <div class="panel-card-dark p-2 flex gap-2 border-b border-white/10">
            <a href="{{ route('filtros.index', ['tab' => 'contactos'] + request()->except('tab', 'page')) }}" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ ($tab ?? 'contactos') === 'contactos' ? 'bg-[#FFE600] text-[#003366]' : 'text-white/80 hover:bg-white/10' }}">Contactos</a>
            <a href="{{ route('filtros.index', ['tab' => 'empresas'] + request()->except('tab', 'page')) }}" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ ($tab ?? '') === 'empresas' ? 'bg-[#FFE600] text-[#003366]' : 'text-white/80 hover:bg-white/10' }}">Empresas</a>
        </div>

        {{-- Chips de filtros activos --}}
        @php
            $filterRows = collect($filterSpecs ?? [])->map(fn ($s) => $s instanceof \App\DataTransferObjects\FilterSpec ? $s->toArray() : $s)->values()->all();
            $filterRows[] = ['field' => '', 'operator' => 'equals', 'value' => ''];
        @endphp
        <x-filters.chips :filters="$filtersForChips ?? []" :clearUrl="route('filtros.index', ['tab' => $tab ?? 'contactos'])" />

        <form method="GET" action="{{ route('filtros.index') }}" id="form-filtros" class="space-y-4">
            <input type="hidden" name="tab" value="{{ $tab ?? 'contactos' }}">

            <div class="panel-card-dark p-6">
                <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">
                    {{ ($tab ?? 'contactos') === 'contactos' ? 'Filtrar contactos' : 'Filtrar empresas' }}
                </h3>

                {{-- Lógica AND / OR --}}
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <span class="text-sm font-medium text-white/90">Combinar filtros:</span>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="filter_logic" value="and" {{ ($filterLogic ?? 'and') === 'and' ? 'checked' : '' }} class="rounded border-white/30 bg-white/10 text-[#FFE600] focus:ring-[#FFE600]">
                        <span class="text-sm text-white/90">Y (AND)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="filter_logic" value="or" {{ ($filterLogic ?? '') === 'or' ? 'checked' : '' }} class="rounded border-white/30 bg-white/10 text-[#FFE600] focus:ring-[#FFE600]">
                        <span class="text-sm text-white/90">O (OR)</span>
                    </label>
                </div>

                <div class="space-y-3" id="filter-rows">
                    @foreach($filterRows as $idx => $row)
                        <x-filters.row
                            :index="$idx"
                            :fields="$fields ?? []"
                            :operatorLabels="$operatorLabels ?? []"
                            :currentField="$row['field'] ?? ''"
                            :currentOperator="$row['operator'] ?? 'equals'"
                            :currentValue="$row['value'] ?? ''"
                        />
                    @endforeach
                </div>

                <p class="text-xs text-white/60 mt-2">Deje el campo vacío en la última fila para ignorarla. Agregue más filas rellenando y aplicando.</p>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="submit" class="btn-amber-app">Aplicar filtros</button>
                    <a href="{{ route('filtros.index', ['tab' => $tab ?? 'contactos']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/40 text-white/90 text-sm font-medium hover:bg-white/10">Limpiar</a>
                </div>
            </div>
        </form>

        {{-- Resultados contactos --}}
        @if(($tab ?? 'contactos') === 'contactos' && isset($contacts) && $contacts->isNotEmpty())
            <div class="panel-card-dark overflow-hidden">
                <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Resultados ({{ $contacts->total() }})</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/20">
                        <thead>
                            <tr class="table-header-panel-dark">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Empresa</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Tel / Cel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($contacts as $contact)
                                <tr class="panel-card-dark__row hover:bg-white/5">
                                    <td class="px-4 py-3 text-sm text-white">{{ $contact->nombre_completo }}</td>
                                    <td class="px-4 py-3 text-sm text-white/90">{{ $contact->company?->nombre_comercial ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-white/90">{{ $contact->telefono ?? $contact->celular ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-white/90">{{ $contact->email ?? '—' }}</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 text-xs rounded-lg badge-prospect-{{ $contact->status_color ?? 'seguimiento' }}">{{ $contact->status_label ?? '—' }}</span></td>
                                    <td class="px-4 py-3"><a href="{{ route('contacts.show', $contact) }}" class="text-[#FFE600] hover:text-white text-sm">Ver</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 pt-4 border-t border-white/20">{{ $contacts->links() }}</div>
            </div>
        @elseif(($tab ?? 'contactos') === 'contactos' && isset($contacts) && $contacts->isEmpty() && !empty($filterSpecs))
            <div class="panel-card-dark p-6 text-center text-white/70">No hay contactos con los filtros aplicados. Ajuste los criterios o limpie filtros.</div>
        @endif

        {{-- Resultados empresas --}}
        @if(($tab ?? '') === 'empresas' && isset($companies) && $companies->isNotEmpty())
            <div class="panel-card-dark overflow-hidden">
                <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Resultados ({{ $companies->total() }})</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/20">
                        <thead>
                            <tr class="table-header-panel-dark">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">RFC</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Ejecutivo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($companies as $company)
                                <tr class="panel-card-dark__row hover:bg-white/5">
                                    <td class="px-4 py-3 text-sm text-white">{{ $company->nombre_comercial }}</td>
                                    <td class="px-4 py-3 text-sm text-white/90">{{ $company->rfc ?? '—' }}</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 text-xs rounded-lg badge-prospect-{{ $company->status_color }}">{{ $company->status_label }}</span></td>
                                    <td class="px-4 py-3 text-sm text-white/90">{{ $company->ejecutivo_asignado ?? '—' }}</td>
                                    <td class="px-4 py-3"><a href="{{ route('companies.show', $company) }}" class="text-[#FFE600] hover:text-white text-sm">Ver</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 pt-4 border-t border-white/20">{{ $companies->links() }}</div>
            </div>
        @elseif(($tab ?? '') === 'empresas' && isset($companies) && $companies->isEmpty() && !empty($filterSpecs))
            <div class="panel-card-dark p-6 text-center text-white/70">No hay empresas con los filtros aplicados. Ajuste los criterios o limpie filtros.</div>
        @endif
    </div>
</x-app-layout>
