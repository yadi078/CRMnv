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
        <a href="{{ route('filtros.index') }}" class="ml-auto inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/90 text-sm font-medium hover:bg-white/10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            Limpiar filtros
        </a>
    </x-slot>

    <div class="space-y-6">
        {{-- Chips de filtros activos --}}
        @php
            // UI tipo Excel: mantenemos filas fijas (menos Género, que lo movemos arriba).
            $visibleRequiredFields = [
                'nombre_completo' => 'contains',
                'telefono' => 'contains',
                'celular' => 'contains',
                'email' => 'contains',
            ];

            // Campos que deben aparecer en el select "Campo" (lado izquierdo).
            // Mapeos: area_trabajo -> departamento, ciudad -> municipio, giro -> sector.
            $fieldsToShow = [
                'genero',
                'nombre_completo',
                'telefono',
                'celular',
                'email',
                'departamento',
                'puesto_de_trabajo',
                'municipio',
                'estado',
                'comercial',
                'sector',
                'no_recibir_correos',
            ];

            $fieldsForRows = collect($fields ?? [])->only($fieldsToShow)->all();

            $specMap = collect($filterSpecs ?? [])
                ->map(fn ($s) => $s instanceof \App\DataTransferObjects\FilterSpec ? $s->toArray() : $s)
                ->keyBy(fn ($row) => $row['field'] ?? '')
                ->all();

            // Género: se toma desde filterSpecs para seleccionar M/F/O/Todos.
            $generoSpec = $specMap['genero'] ?? null;
            $generoValue = $generoSpec['value'] ?? '';
            $generoRadioTodosChecked = empty($generoValue);
            $generoRadioMasculinoChecked = (string)$generoValue === 'Masculino';
            $generoRadioFemeninoChecked = (string)$generoValue === 'Femenino';
            $generoRadioOtroChecked = (string)$generoValue === 'Otro';

            $filterRows = [];
            foreach ($visibleRequiredFields as $fieldKey => $defaultOp) {
                $spec = $specMap[$fieldKey] ?? null;
                $filterRows[] = [
                    'field' => $fieldKey,
                    'operator' => $spec['operator'] ?? $defaultOp,
                    'value' => $spec['value'] ?? null,
                ];
            }
        @endphp
        <div id="filtros-chips">
            <x-filters.chips :filters="$filtersForChips ?? []" :clearUrl="route('filtros.index')" />
        </div>

        <form method="GET" action="{{ route('filtros.index') }}" id="form-filtros" class="space-y-4">
            @csrf
            <div class="panel-card-dark p-6">
                <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">
                    Filtrar contactos y empresas
                </h3>

                {{-- Género: selección única (M/F/O/Todos) --}}
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <span class="text-sm font-medium text-white/90">Género:</span>
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-white/90">
                        <input
                            type="radio"
                            name="genero_radio"
                            value=""
                            {{ $generoRadioTodosChecked ? 'checked' : '' }}
                            class="rounded border-white/30 bg-white/10 text-[#FFE600] focus:ring-[#FFE600]"
                        >
                        <span>Todos</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-white/90">
                        <input
                            type="radio"
                            name="genero_radio"
                            value="Masculino"
                            {{ $generoRadioMasculinoChecked ? 'checked' : '' }}
                            class="rounded border-white/30 bg-white/10 text-[#FFE600] focus:ring-[#FFE600]"
                        >
                        <span>M</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-white/90">
                        <input
                            type="radio"
                            name="genero_radio"
                            value="Femenino"
                            {{ $generoRadioFemeninoChecked ? 'checked' : '' }}
                            class="rounded border-white/30 bg-white/10 text-[#FFE600] focus:ring-[#FFE600]"
                        >
                        <span>F</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-white/90">
                        <input
                            type="radio"
                            name="genero_radio"
                            value="Otro"
                            {{ $generoRadioOtroChecked ? 'checked' : '' }}
                            class="rounded border-white/30 bg-white/10 text-[#FFE600] focus:ring-[#FFE600]"
                        >
                        <span>Otro</span>
                    </label>
                </div>

                {{-- Hidden inputs para que el backend reciba filters[0] como Género --}}
                <input type="hidden" name="filters[0][field]" value="genero">
                <input type="hidden" name="filters[0][operator]" value="equals">
                <input type="hidden" name="filters[0][value]" id="filters-0-value-genero" value="{{ $generoValue }}">

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
                        @php $realIndex = $idx + 1; @endphp
                        <x-filters.row
                            :index="$realIndex"
                            :fields="$fieldsForRows ?? []"
                            :operatorLabels="$operatorLabels ?? []"
                            :suggestions="$fieldSuggestions ?? []"
                            :showRemove="false"
                            :currentField="$row['field'] ?? ''"
                            :currentOperator="$row['operator'] ?? 'equals'"
                            :currentValue="$row['value'] ?? ''"
                        />
                    @endforeach
                </div>

                <p class="text-xs text-white/60 mt-2">Puedes escribir y seleccionar sugerencias en Tel/Cel/Email/Nombre.</p>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="submit" class="btn-amber-app">Aplicar filtros</button>
                    <a href="{{ route('filtros.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/40 text-white/90 text-sm font-medium hover:bg-white/10">Limpiar</a>
                </div>
            </div>
        </form>

        <div id="filtros-results">
        {{-- Resultados contactos --}}
        @if(isset($contacts) && $contacts->isNotEmpty())
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
        @elseif(isset($contacts) && $contacts->isEmpty() && !empty($filterSpecs))
            <div class="panel-card-dark p-6 text-center text-white/70">No hay contactos con los filtros aplicados. Ajuste los criterios o limpie filtros.</div>
        @endif

        {{-- Resultados empresas --}}
        @if(isset($companies) && $companies->isNotEmpty())
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
        @elseif(isset($companies) && $companies->isEmpty() && !empty($filterSpecs))
            <div class="panel-card-dark p-6 text-center text-white/70">No hay empresas con los filtros aplicados. Ajuste los criterios o limpie filtros.</div>
        @endif
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('form-filtros');
            const chipsEl = document.getElementById('filtros-chips');
            const resultsEl = document.getElementById('filtros-results');
            if (!form) return;

            // +1 porque el filtro de Género va como hidden en index 0.
            const rowCount = {{ count($filterRows) + 1 }};
            const fieldConfigs = @json($fieldsForRows ?? []);
            const fieldSuggestions = @json($fieldSuggestions ?? []);
            const operatorLabelsJs = @json($operatorLabels ?? []);

            // Sincronizar Género (radio arriba) con hidden filters[0][value]
            const generoHidden = document.getElementById('filters-0-value-genero');
            if (generoHidden) {
                const radios = form.querySelectorAll('input[name="genero_radio"]');
                radios.forEach(r => {
                    r.addEventListener('change', () => {
                        generoHidden.value = r.value || '';
                    });
                });
            }

            // Operadores donde el valor no es requerido.
            const valueHiddenOps = new Set(['is_empty', 'is_not_empty', 'has_value', 'no_value']);
            const defaultOperatorsJs = [
                'contains',
                'not_contains',
                'starts_with',
                'ends_with',
                'equals',
                'not_equals',
                'is_empty',
                'is_not_empty',
                'has_value',
                'no_value'
            ];

            function getExistingValueFromWrap(valueWrap, rowIdx) {
                const checkbox = valueWrap.querySelector(`input[name="filters[${rowIdx}][value]"][type="checkbox"]`);
                if (checkbox) {
                    return checkbox.checked ? '1' : null;
                }

                const text = valueWrap.querySelector(`input[name="filters[${rowIdx}][value]"][type="text"]`);
                if (text) {
                    return text.value || null;
                }

                const singleSelect = valueWrap.querySelector(`select[name="filters[${rowIdx}][value]"]`);
                if (singleSelect) {
                    return singleSelect.value || null;
                }

                const multiSelect = valueWrap.querySelector(`select[name="filters[${rowIdx}][value][]"]`);
                if (multiSelect) {
                    return Array.from(multiSelect.selectedOptions).map(o => o.value);
                }

                return null;
            }

            function syncOperatorsAndValue(rowIdx) {
                const fieldSelect = form.querySelector(`select[name="filters[${rowIdx}][field]"]`);
                const operatorSelect = form.querySelector(`select[name="filters[${rowIdx}][operator]"]`);
                const valueWrap = form.querySelector(`[data-value-wrap="${rowIdx}"]`);
                if (!fieldSelect || !operatorSelect || !valueWrap) return;

                const selectedField = fieldSelect.value;
                const cfg = fieldConfigs[selectedField] ?? null;
                const ops = (cfg && Array.isArray(cfg.operators) && cfg.operators.length > 0) ? cfg.operators : defaultOperatorsJs;

                // Guardar valores existentes (para conservar si el filtro ya venía aplicado).
                const existingValue = getExistingValueFromWrap(valueWrap, rowIdx);

                // Actualizar operadores
                const currentOp = operatorSelect.value;
                operatorSelect.innerHTML = '';
                ops.forEach(op => {
                    const opt = document.createElement('option');
                    opt.value = op;
                    opt.textContent = operatorLabelsJs[op] ?? op;
                    operatorSelect.appendChild(opt);
                });

                if (ops.includes(currentOp)) {
                    operatorSelect.value = currentOp;
                } else {
                    operatorSelect.value = ops[0] ?? 'equals';
                }

                const operator = operatorSelect.value;
                const hideValue = valueHiddenOps.has(operator);

                valueWrap.style.display = hideValue ? 'none' : 'block';
                if (hideValue) return;

                // Renderizar control de valor según tipo del campo
                const type = (cfg && cfg.type) ? cfg.type : 'text';
                const options = (cfg && cfg.options) ? cfg.options : {};

                valueWrap.innerHTML = '';

                const baseClasses = 'w-full rounded-xl border border-gray-200 bg-white text-[#0B2C66] py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40';

                if (type === 'select') {
                    const isMultiple = !!cfg.multiple;
                    const select = document.createElement('select');
                    if (isMultiple) {
                        select.name = `filters[${rowIdx}][value][]`;
                        select.multiple = true;
                        select.className = `${baseClasses} h-28`;
                    } else {
                        select.name = `filters[${rowIdx}][value]`;
                        select.className = baseClasses;
                    }

                    const entries = Object.entries(options);
                    entries.forEach(([val, label]) => {
                        const opt = document.createElement('option');
                        opt.value = String(val);
                        opt.textContent = String(label);
                        select.appendChild(opt);
                    });

                    // Restaurar selección existente si aplica.
                    if (existingValue != null) {
                        if (isMultiple && Array.isArray(existingValue)) {
                            const set = new Set(existingValue.map(v => String(v)));
                            Array.from(select.options).forEach(o => {
                                o.selected = set.has(String(o.value));
                            });
                        } else if (!isMultiple) {
                            select.value = String(existingValue);
                        }
                    }

                    valueWrap.appendChild(select);
                    return;
                }

                if (type === 'checkbox') {
                    const labelText = options['1'] ?? options[1] ?? 'Sí';
                    const wrapper = document.createElement('label');
                    wrapper.className = 'inline-flex items-center gap-2 text-sm text-white/90 cursor-pointer';

                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.name = `filters[${rowIdx}][value]`;
                    input.value = '1';
                    input.className = 'rounded border-gray-300 bg-white text-[#0B2C66] focus:ring-[#FFE600]';

                    if (existingValue === '1' || existingValue === 1) {
                        input.checked = true;
                    }

                    const span = document.createElement('span');
                    span.textContent = labelText;

                    wrapper.appendChild(input);
                    wrapper.appendChild(span);
                    valueWrap.appendChild(wrapper);
                    return;
                }

                // Texto (con datalist sugerencias)
                const input = document.createElement('input');
                input.type = 'text';
                input.name = `filters[${rowIdx}][value]`;
                input.placeholder = 'Valor...';
                input.autocomplete = 'off';
                input.className = baseClasses + ' placeholder-gray-400';

                const suggestions = fieldSuggestions[selectedField] ?? [];
                if (Array.isArray(suggestions) && suggestions.length > 0) {
                    const listId = `datalist-${rowIdx}-${selectedField}`;
                    input.setAttribute('list', listId);

                    const datalist = document.createElement('datalist');
                    datalist.id = listId;
                    suggestions.forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = String(v);
                        datalist.appendChild(opt);
                    });
                    valueWrap.appendChild(input);
                    valueWrap.appendChild(datalist);
                } else {
                    valueWrap.appendChild(input);
                }

                if (existingValue != null && typeof existingValue === 'string') {
                    input.value = existingValue;
                }
            }

            for (let i = 0; i < rowCount; i++) {
                const fieldSelect = form.querySelector(`select[name="filters[${i}][field]"]`);
                const operatorSelect = form.querySelector(`select[name="filters[${i}][operator]"]`);
                if (fieldSelect) {
                    fieldSelect.addEventListener('change', () => syncOperatorsAndValue(i));
                }
                if (operatorSelect) {
                    operatorSelect.addEventListener('change', () => syncOperatorsAndValue(i));
                }
                syncOperatorsAndValue(i);
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                // Evitar doble filtro de Género:
                // Si el usuario eligió "genero" en alguna fila adicional, la invalidamos limpiando su valor.
                for (let i = 1; i < rowCount; i++) {
                    const fieldSelect = form.querySelector(`select[name="filters[${i}][field]"]`);
                    if (!fieldSelect || fieldSelect.value !== 'genero') continue;

                    const operatorSelect = form.querySelector(`select[name="filters[${i}][operator]"]`);
                    if (operatorSelect) operatorSelect.value = 'equals';

                    const valueWrap = form.querySelector(`[data-value-wrap="${i}"]`);
                    if (!valueWrap) continue;

                    const checkbox = valueWrap.querySelector(`input[name="filters[${i}][value]"][type="checkbox"]`);
                    if (checkbox) checkbox.checked = false;

                    const text = valueWrap.querySelector(`input[name="filters[${i}][value]"][type="text"]`);
                    if (text) text.value = '';

                    const singleSelect = valueWrap.querySelector(`select[name="filters[${i}][value]"]`);
                    if (singleSelect) {
                        singleSelect.value = '';
                        if (singleSelect.value !== '') singleSelect.selectedIndex = 0;
                    }

                    const multiSelect = valueWrap.querySelector(`select[name="filters[${i}][value][]"]`);
                    if (multiSelect) {
                        Array.from(multiSelect.options).forEach(o => (o.selected = false));
                    }
                }

                const url = @json(route('filtros.ajax'));
                const fd = new FormData(form);

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: fd
                    });

                    if (!res.ok) {
                        const params = new URLSearchParams(fd);
                        window.location.href = form.action + '?' + params.toString();
                        return;
                    }

                    const data = await res.json();
                    if (data.chipsHtml && chipsEl) chipsEl.innerHTML = data.chipsHtml;
                    if (data.resultsHtml && resultsEl) resultsEl.innerHTML = data.resultsHtml;
                } catch (err) {
                    console.error(err);
                    const params = new URLSearchParams(fd);
                    window.location.href = form.action + '?' + params.toString();
                }
            });
        })();
    </script>

</x-app-layout>
