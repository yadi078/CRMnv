<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M5 10h14M9 16h6m-3 4v-4" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Filtros</h2>
            <p class="page-header-card__subtitle">Filtros avanzados por contactos y empresas</p>
        </div>
        <a href="{{ route('filtros.index', ['clear' => 1]) }}" class="ml-auto inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/90 text-sm font-medium hover:bg-white/10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            Limpiar filtros
        </a>
    </x-slot>

    <div class="space-y-8">
        @php
            $excelHeaders = [
                'genero' => 'Género',
                'nombre_completo' => 'Nombre',
                'telefono' => 'Teléfono',
                'celular' => 'Celular',
                'email' => 'Email',
                'departamento' => 'Área de trabajo',
                'puesto_de_trabajo' => 'Puesto de trabajo',
                'municipio' => 'Ciudad',
                'estado' => 'Estado',
                'status_color' => 'Estatus de prospecto (color)',
                'comercial' => 'Ejecutivo',
                'sector' => 'Giro',
                'notas' => 'Notas',
                'domicilio' => 'Domicilio',
                'no_recibir_correos' => 'No desea recibir correos',
            ];
            $fieldOptions = [];
            foreach ($excelHeaders as $key => $label) {
                $options = $fields[$key]['options'] ?? [];
                if (is_array($options) && !empty($options)) {
                    $fieldOptions[$key] = $options;
                }
            }

            $selectedByField = [];
            foreach (($filterSpecs ?? []) as $spec) {
                $item = $spec instanceof \App\DataTransferObjects\FilterSpec ? $spec->toArray() : $spec;
                $field = $item['field'] ?? null;
                if ($field === 'datos_fiscales') {
                    $field = 'domicilio';
                }
                if (! $field || ! array_key_exists($field, $excelHeaders)) {
                    continue;
                }
                $value = $item['value'] ?? null;
                $selectedByField[$field] = is_array($value) ? array_values($value) : [$value];
            }
        @endphp

        <form method="GET" action="{{ route('filtros.index') }}" id="form-filtros" class="space-y-5">
            @csrf
            <div class="panel-card-dark filtros-form-card p-5 md:p-6 space-y-6 shadow-lg shadow-black/25 border border-white/10 overflow-visible">
                <div id="header-filter-buttons-wrap" class="relative overflow-visible">
                <p class="text-sm text-white/75 mb-3">Elija campos y valores; luego aplique o limpie los filtros.</p>
                <div id="header-filter-buttons" class="flex flex-wrap gap-2.5">
                    @foreach($excelHeaders as $fieldKey => $label)
                        <button
                            type="button"
                            class="excel-filter-btn inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-[#FFD700]/80 bg-[#FFE600] text-[#0B2C66] text-xs font-semibold hover:bg-[#FFE600]/90"
                            data-field="{{ $fieldKey }}"
                        >
                            <span>{{ $label }}</span>
                            <span class="text-[11px] opacity-80" data-count-for="{{ $fieldKey }}">(0)</span>
                        </button>
                    @endforeach
                </div>

                {{-- El panel se mueve a document.body por JS para no quedar recortado por overflow/transform de ancestros --}}
                <div id="excel-filter-panel" class="hidden fixed z-[9999] box-border w-[min(360px,calc(100vw-1.25rem))] max-w-[min(360px,calc(100vw-1.25rem))] max-h-[min(32rem,calc(100vh-8rem))] overflow-y-auto overflow-x-hidden rounded-xl border border-[#0B2C66]/20 bg-white p-3 text-[#0B2C66] shadow-2xl [overscroll-behavior:contain]">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <h4 id="excel-panel-title" class="text-base font-semibold">Filtro</h4>
                        <button
                            type="button"
                            id="excel-panel-apply-header"
                            class="inline-flex items-center justify-center rounded-lg p-1.5 text-[#0B2C66] border border-[#0B2C66]/30 hover:bg-[#0B2C66]/10 focus:outline-none focus:ring-2 focus:ring-[#0B2C66]/40 shrink-0"
                            title="Aplicar filtro"
                            aria-label="Aplicar filtro"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                    <input id="excel-option-search" type="text" placeholder="Buscar" class="w-full rounded-md border border-gray-300 px-2.5 py-1.5 text-sm mb-2 text-[#0B2C66] placeholder:text-[#0B2C66]/45">
                    <label class="inline-flex items-center gap-2 text-sm mb-2 text-[#0B2C66]">
                        <input id="excel-select-all" type="checkbox" class="rounded border-gray-300 text-[#0B2C66] focus:ring-[#0B2C66]">
                        <span style="color:#0B2C66;">(Seleccionar todo)</span>
                    </label>
                    <div id="excel-options-list" class="max-h-52 overflow-y-auto rounded border border-gray-200 bg-white p-2 space-y-1"></div>
                    <div class="mt-2 flex justify-end gap-2">
                        <button type="button" id="excel-panel-accept" class="px-3 py-1.5 rounded-md bg-[#0B2C66] text-white text-sm">Aceptar</button>
                    </div>
                </div>
                </div>

                <div class="rounded-xl border border-white/20 bg-white/10 p-3.5 space-y-2">
                    <p class="text-[11px] uppercase tracking-wide text-white/70">Se está filtrando por</p>
                    <div id="active-filters-summary" class="flex flex-wrap gap-1.5 text-xs text-white/90">
                        <span class="text-white/60">Sin filtros seleccionados</span>
                    </div>
                </div>

                <div class="rounded-xl border border-white/20 bg-white/10 p-3.5 space-y-3">
                    <p class="text-xs font-medium text-white/90">Alcance de resultados</p>
                    <div class="flex flex-wrap gap-6">
                        <label class="inline-flex items-center gap-2 text-xs text-white/90">
                            <input type="radio" name="result_scope" value="empresa" {{ ($resultScope ?? 'ambos') === 'empresa' ? 'checked' : '' }} class="rounded border-white/30 bg-white/10 text-[#FFE600] focus:ring-[#FFE600]">
                            <span>Empresa</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-xs text-white/90">
                            <input type="radio" name="result_scope" value="contacto" {{ ($resultScope ?? 'ambos') === 'contacto' ? 'checked' : '' }} class="rounded border-white/30 bg-white/10 text-[#FFE600] focus:ring-[#FFE600]">
                            <span>Contacto</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-xs text-white/90">
                            <input type="radio" name="result_scope" value="ambos" {{ ($resultScope ?? 'ambos') === 'ambos' ? 'checked' : '' }} class="rounded border-white/30 bg-white/10 text-[#FFE600] focus:ring-[#FFE600]">
                            <span>Ambos</span>
                        </label>
                    </div>
                </div>

                <input type="hidden" name="filter_logic" value="and">
                <div id="dynamic-filters-container"></div>

                <div class="flex flex-wrap gap-3 pt-1 border-t border-white/10">
                    <button type="submit" class="btn-amber-app">Aplicar filtros</button>
                    <a href="{{ route('filtros.index', ['clear' => 1]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-white/40 text-white/90 text-sm font-medium hover:bg-white/10">Limpiar</a>
                </div>
            </div>
        </form>

        <div id="filtros-results" class="space-y-10">
        {{-- Resultados contactos --}}
        @if(isset($contacts) && $contacts->isNotEmpty())
            <div class="panel-card-dark overflow-hidden border border-white/12 ring-1 ring-[#FFE600]/10 shadow-xl shadow-black/30">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 pb-4 mb-1 border-b border-white/15">
                    <h3 class="panel-card-dark__title panel-card-dark__title--accent !mb-0 text-xl md:text-2xl font-bold tracking-tight">Contactos</h3>
                    <span class="text-sm text-white/60 font-medium tabular-nums">{{ $contacts->total() }} {{ $contacts->total() === 1 ? 'registro' : 'registros' }}</span>
                </div>
                <div class="crm-table-scroll-wrap crm-table-scroll w-full min-w-0 -mx-1 px-1">
                    <table class="w-full crm-table-wide divide-y divide-white/15">
                        <thead>
                            <tr class="table-header-panel-dark">
                                <th scope="col" class="crm-row-marker-head w-11 min-w-[2.75rem] px-1 py-3.5 text-center text-[10px] font-semibold uppercase tracking-wide text-[#FFE600]/90" title="Seguimiento personal (solo en este navegador)">Seg.</th>
                                <th class="min-w-[11rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Nombre</th>
                                <th class="min-w-[10rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Empresa</th>
                                <th class="min-w-[10.5rem] px-4 py-3.5 text-left text-xs font-semibold uppercase whitespace-nowrap">Tel / Cel</th>
                                <th class="min-w-[12rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Email</th>
                                <th class="min-w-[9rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Ejecutivo</th>
                                <th class="min-w-[8.5rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($contacts as $contact)
                                <tr class="panel-card-dark__row odd:bg-white/5 even:bg-white/[0.02] hover:bg-white/10 transition-colors duration-150">
                                    <x-crm-row-marker entity="contact" :id="$contact->id" />
                                    <td class="px-4 py-3.5 text-sm align-top whitespace-normal break-words">
                                        <a href="{{ \App\Support\CrmNavigation::withReturn(route('contacts.show', $contact)) }}" title="Ver ficha del contacto" class="font-medium text-white hover:text-[#FFE600] hover:underline focus:outline-none focus:ring-2 focus:ring-[#FFE600]/35 rounded">{{ $contact->nombre_completo }}</a>
                                    </td>
                                    <td class="px-4 py-3.5 text-sm text-white/90 align-top whitespace-normal break-words">
                                        @if($contact->company)
                                            <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $contact->company)) }}" title="Ver ficha de la empresa" class="hover:text-[#FFE600] hover:underline focus:outline-none focus:ring-2 focus:ring-[#FFE600]/35 rounded">{{ $contact->company->nombre_comercial }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-sm text-white/90 whitespace-nowrap tabular-nums">{{ $contact->telefono ?? $contact->celular ?? '—' }}</td>
                                    <td class="px-4 py-3.5 text-sm text-white/90 [overflow-wrap:anywhere]">{{ $contact->email ?? '—' }}</td>
                                    <td class="px-4 py-3.5 text-sm text-white/90">{{ $contact->comercialEjecutivoLabel() }}</td>
                                    <td class="px-4 py-3.5"><span class="px-2 py-0.5 text-xs rounded-lg badge-prospect-{{ $contact->status_color ?? 'seguimiento' }}">{{ $contact->status_label ?? '—' }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-5 pt-5 border-t border-white/20">{{ $contacts->links() }}</div>
            </div>
        @elseif(isset($contacts) && $contacts->isEmpty() && !empty($filterSpecs))
            <div class="panel-card-dark p-8 text-center text-white/70 border border-white/10 rounded-2xl">No hay contactos con los filtros aplicados. Ajuste los criterios o limpie filtros.</div>
        @endif

        {{-- Resultados empresas --}}
        @if(isset($companies) && $companies->isNotEmpty())
            <div class="panel-card-dark overflow-hidden border border-white/12 ring-1 ring-[#FFE600]/10 shadow-xl shadow-black/30">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 pb-4 mb-1 border-b border-white/15">
                    <h3 class="panel-card-dark__title panel-card-dark__title--accent !mb-0 text-xl md:text-2xl font-bold tracking-tight">Empresas</h3>
                    <span class="text-sm text-white/60 font-medium tabular-nums">{{ $companies->total() }} {{ $companies->total() === 1 ? 'registro' : 'registros' }}</span>
                </div>
                <div class="crm-table-scroll-wrap crm-table-scroll w-full min-w-0 -mx-1 px-1">
                    <table class="w-full crm-table-wide divide-y divide-white/15">
                        <thead>
                            <tr class="table-header-panel-dark">
                                <th scope="col" class="crm-row-marker-head w-11 min-w-[2.75rem] px-1 py-3.5 text-center text-[10px] font-semibold uppercase tracking-wide text-[#FFE600]/90" title="Seguimiento personal (solo en este navegador)">Seg.</th>
                                <th class="min-w-[12rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Nombre</th>
                                <th class="min-w-[8.5rem] px-4 py-3.5 text-left text-xs font-semibold uppercase whitespace-nowrap">RFC</th>
                                <th class="min-w-[12rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Estatus</th>
                                <th class="min-w-[9rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Ejecutivo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($companies as $company)
                                <tr class="panel-card-dark__row odd:bg-white/5 even:bg-white/[0.02] hover:bg-white/10 transition-colors duration-150">
                                    <x-crm-row-marker entity="company" :id="$company->id" />
                                    <td class="px-4 py-3.5 text-sm align-top whitespace-normal break-words">
                                        <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $company)) }}" title="Ver ficha de la empresa" class="font-medium text-white hover:text-[#FFE600] hover:underline focus:outline-none focus:ring-2 focus:ring-[#FFE600]/35 rounded">{{ $company->nombre_comercial }}</a>
                                    </td>
                                    <td class="px-4 py-3.5 text-sm text-white/90">{{ $company->rfc ?? '—' }}</td>
                                    <td class="px-4 py-3.5 align-top max-w-[14rem]"><x-company-prospect-status-badges :company="$company" variant="filtros" /></td>
                                    <td class="px-4 py-3.5 text-sm text-white/90">{{ $company->assignedExecutive?->name ?? $company->ejecutivo_asignado ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-5 pt-5 border-t border-white/20">{{ $companies->links() }}</div>
            </div>
        @elseif(isset($companies) && $companies->isEmpty() && !empty($filterSpecs))
            <div class="panel-card-dark p-8 text-center text-white/70 border border-white/10 rounded-2xl">No hay empresas con los filtros aplicados. Ajuste los criterios o limpie filtros.</div>
        @endif
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('form-filtros');
            const resultsEl = document.getElementById('filtros-results');
            const dynamicFiltersContainer = document.getElementById('dynamic-filters-container');
            const summaryEl = document.getElementById('active-filters-summary');
            const panelEl = document.getElementById('excel-filter-panel');
            const panelTitle = document.getElementById('excel-panel-title');
            const panelApplyHeaderBtn = document.getElementById('excel-panel-apply-header');
            const panelAcceptBtn = document.getElementById('excel-panel-accept');
            const searchInput = document.getElementById('excel-option-search');
            const selectAllInput = document.getElementById('excel-select-all');
            const optionsList = document.getElementById('excel-options-list');
            if (panelEl && panelEl.parentElement !== document.body) {
                document.body.appendChild(panelEl);
            }
            if (!form) return;

            const headerLabels = @json($excelHeaders ?? []);
            const fieldOptions = @json($fieldOptions ?? []);
            const selectedByField = @json($selectedByField ?? []);
            const prospectStatusLabels = @json($prospectStatusLabels ?? []);
            const persistStateUrl = @json(route('filtros.persist-state'));
            let currentField = null;
            let persistTimer = null;

            function schedulePersistFiltrosState() {
                clearTimeout(persistTimer);
                persistTimer = setTimeout(() => {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (!token) return;
                    const fd = new FormData(form);
                    fetch(persistStateUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: fd,
                        credentials: 'same-origin',
                    }).catch(() => {});
                }, 500);
            }

            Object.keys(headerLabels).forEach((field) => {
                if (!Array.isArray(selectedByField[field])) selectedByField[field] = [];
                selectedByField[field] = selectedByField[field].map(String);
            });

            function renderCounts() {
                document.querySelectorAll('[data-count-for]').forEach((el) => {
                    const field = el.getAttribute('data-count-for');
                    const count = (selectedByField[field] || []).length;
                    el.textContent = `(${count})`;
                });
            }

            function renderSummary() {
                const entries = Object.entries(selectedByField).filter(([, values]) => Array.isArray(values) && values.length > 0);
                summaryEl.innerHTML = '';
                if (entries.length === 0) {
                    summaryEl.innerHTML = '<span class="text-white/60">Sin filtros seleccionados</span>';
                    return;
                }
                entries.forEach(([field, values]) => {
                    const chip = document.createElement('span');
                    chip.className = 'inline-flex items-center gap-1.5 max-w-full rounded-lg border border-[#FFE600]/40 bg-[#FFE600]/20 pl-2.5 pr-1 py-1 text-xs text-[#FFE600]';
                    const displayVals = values.map((v) => {
                        const key = String(v);
                        if (field === 'status_color' && prospectStatusLabels[key]) {
                            return prospectStatusLabels[key];
                        }
                        const opts = fieldOptions[field];
                        if (opts && Object.prototype.hasOwnProperty.call(opts, key)) {
                            return opts[key];
                        }
                        return key;
                    });
                    const label = document.createElement('span');
                    label.className = 'min-w-0 break-words';
                    label.textContent = `${headerLabels[field]}: ${displayVals.join(', ')}`;
                    chip.appendChild(label);

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className =
                        'shrink-0 inline-flex h-6 w-6 items-center justify-center rounded-md text-[#FFE600] hover:bg-[#0B2C66]/30 hover:text-white focus:outline-none focus:ring-2 focus:ring-[#FFE600]/50';
                    removeBtn.setAttribute('aria-label', `Quitar filtro: ${headerLabels[field] || field}`);
                    removeBtn.title = 'Quitar este filtro';
                    removeBtn.innerHTML =
                        '<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
                    removeBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        selectedByField[field] = [];
                        if (currentField === field) {
                            closePanel();
                        }
                        renderCounts();
                        renderSummary();
                        renderHiddenInputs();
                    });
                    chip.appendChild(removeBtn);
                    summaryEl.appendChild(chip);
                });
            }

            function renderHiddenInputs() {
                dynamicFiltersContainer.innerHTML = '';
                let idx = 0;
                Object.entries(selectedByField).forEach(([field, values]) => {
                    if (!Array.isArray(values) || values.length === 0) return;

                    const fieldInput = document.createElement('input');
                    fieldInput.type = 'hidden';
                    fieldInput.name = `filters[${idx}][field]`;
                    fieldInput.value = field;
                    dynamicFiltersContainer.appendChild(fieldInput);

                    const operatorInput = document.createElement('input');
                    operatorInput.type = 'hidden';
                    operatorInput.name = `filters[${idx}][operator]`;
                    operatorInput.value = 'equals';
                    dynamicFiltersContainer.appendChild(operatorInput);

                    values.forEach((value) => {
                        const valueInput = document.createElement('input');
                        valueInput.type = 'hidden';
                        valueInput.name = `filters[${idx}][value][]`;
                        valueInput.value = String(value);
                        dynamicFiltersContainer.appendChild(valueInput);
                    });
                    idx += 1;
                });
                schedulePersistFiltrosState();
            }

            function renderPanelOptions() {
                if (!currentField) return;
                const search = (searchInput.value || '').trim().toLowerCase();
                const options = Object.entries(fieldOptions[currentField] || {});
                const selected = new Set((selectedByField[currentField] || []).map(String));
                const filtered = options.filter(([value, label]) => {
                    const txt = `${value} ${label}`.toLowerCase();
                    return txt.includes(search);
                });

                optionsList.innerHTML = '';
                filtered.forEach(([value, label]) => {
                    const id = `opt-${currentField}-${String(value).replace(/[^a-zA-Z0-9_-]/g, '_')}`;
                    const row = document.createElement('label');
                    row.className = 'flex items-center gap-2 px-2 py-1 rounded hover:bg-gray-100 text-sm cursor-pointer';
                    row.innerHTML = `
                        <input id="${id}" type="checkbox" value="${String(value).replace(/"/g, '&quot;')}" ${selected.has(String(value)) ? 'checked' : ''} class="rounded border-gray-300 text-[#0B2C66] focus:ring-[#0B2C66]">
                        <span style="color:#0B2C66; font-weight:500;">${String(label)}</span>
                    `;
                    optionsList.appendChild(row);
                });

                const visibleCheckboxes = Array.from(optionsList.querySelectorAll('input[type="checkbox"]'));
                const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every((cb) => cb.checked);
                selectAllInput.checked = allChecked;
            }

            function viewportMetrics() {
                const vv = window.visualViewport;
                if (vv) {
                    return {
                        w: vv.width,
                        h: vv.height,
                        offsetTop: vv.offsetTop || 0,
                    };
                }
                return {
                    w: window.innerWidth,
                    h: window.innerHeight,
                    offsetTop: 0,
                };
            }

            function positionFilterPanel(trigger) {
                if (!trigger || !panelEl) return;
                const btnRect = trigger.getBoundingClientRect();
                const margin = 10;
                const bottomReserve = 128;
                const vp = viewportMetrics();
                const vw = vp.w;
                const vh = vp.h;
                const panelW = Math.min(360, vw - margin * 2);
                let left = btnRect.left;
                left = Math.min(left, vw - panelW - margin);
                left = Math.max(margin, left);
                let top = btnRect.bottom + 6;
                panelEl.style.position = 'fixed';
                panelEl.style.left = `${left}px`;
                panelEl.style.top = `${top}px`;
                panelEl.style.width = `${panelW}px`;
                panelEl.style.maxWidth = `${panelW}px`;
                panelEl.style.right = 'auto';
                panelEl.style.bottom = 'auto';
                panelEl.style.zIndex = '9999';
                panelEl.style.boxSizing = 'border-box';

                requestAnimationFrame(() => {
                    const pr = panelEl.getBoundingClientRect();
                    const maxBottom = vh - margin - bottomReserve;
                    if (pr.bottom > maxBottom) {
                        const above = btnRect.top - pr.height - 6;
                        if (above >= margin + vp.offsetTop) {
                            panelEl.style.top = `${above}px`;
                            panelEl.style.maxHeight = `min(32rem, calc(100vh - ${bottomReserve}px))`;
                        } else {
                            panelEl.style.top = `${margin + vp.offsetTop}px`;
                            const maxH = Math.max(160, maxBottom - margin - vp.offsetTop);
                            panelEl.style.maxHeight = `${maxH}px`;
                        }
                    } else {
                        panelEl.style.maxHeight = `min(32rem, calc(100vh - ${bottomReserve}px))`;
                    }
                });
            }

            function openPanelFor(field) {
                currentField = field;
                panelTitle.textContent = headerLabels[field] || 'Filtro';
                searchInput.value = '';
                const trigger = document.querySelector(`.excel-filter-btn[data-field="${field}"]`);
                panelEl.classList.remove('hidden');
                renderPanelOptions();
                if (trigger) {
                    positionFilterPanel(trigger);
                    requestAnimationFrame(() => positionFilterPanel(trigger));
                }
            }

            function closePanel() {
                panelEl.classList.add('hidden');
                panelEl.style.maxHeight = '';
                panelEl.style.top = '';
                panelEl.style.left = '';
                currentField = null;
            }

            function applyPanelSelections() {
                if (!currentField) return;
                const checkedValues = Array.from(optionsList.querySelectorAll('input[type="checkbox"]:checked')).map((cb) => String(cb.value));
                selectedByField[currentField] = checkedValues;
                renderCounts();
                renderSummary();
                renderHiddenInputs();
                closePanel();
            }

            document.querySelectorAll('.excel-filter-btn').forEach((button) => {
                button.addEventListener('click', () => openPanelFor(button.getAttribute('data-field')));
            });

            panelApplyHeaderBtn?.addEventListener('click', applyPanelSelections);
            panelAcceptBtn?.addEventListener('click', applyPanelSelections);

            // Evita que el clic burbujee a document: sin esto, en móvil/scroll el "click outside"
            // puede interpretarse mal y cerrar el panel al tocar opciones o la lista.
            panelEl.addEventListener('click', (e) => e.stopPropagation());
            panelEl.addEventListener('pointerdown', (e) => e.stopPropagation());

            searchInput?.addEventListener('input', renderPanelOptions);
            selectAllInput?.addEventListener('change', () => {
                Array.from(optionsList.querySelectorAll('input[type="checkbox"]')).forEach((cb) => {
                    cb.checked = selectAllInput.checked;
                });
            });

            document.addEventListener('click', (event) => {
                if (panelEl.classList.contains('hidden')) return;
                const target = event.target;
                if (!(target instanceof Element)) return;
                if (panelEl.contains(target)) return;
                if (typeof event.composedPath === 'function' && event.composedPath().includes(panelEl)) return;
                if (target.closest('.excel-filter-btn')) return;
                closePanel();
            });

            window.addEventListener(
                'scroll',
                (event) => {
                    if (panelEl.classList.contains('hidden')) return;
                    const t = event.target;
                    // No cerrar si el scroll ocurre dentro del panel (lista, etc.); sí si se mueve la página.
                    if (t instanceof Node && (t === panelEl || panelEl.contains(t))) {
                        return;
                    }
                    closePanel();
                },
                true
            );
            window.addEventListener('resize', () => {
                if (panelEl.classList.contains('hidden') || !currentField) return;
                const trigger = document.querySelector(`.excel-filter-btn[data-field="${CSS.escape(currentField)}"]`);
                if (trigger) {
                    positionFilterPanel(trigger);
                }
            });

            renderCounts();
            renderSummary();
            renderHiddenInputs();

            form.addEventListener('submit', function () {
                // Si el panel está abierto, primero consolida las selecciones visibles.
                if (currentField && !panelEl.classList.contains('hidden')) {
                    applyPanelSelections();
                }
                // Garantiza que los inputs hidden estén actualizados antes del envío GET.
                renderHiddenInputs();
            });
        })();
    </script>

</x-app-layout>
