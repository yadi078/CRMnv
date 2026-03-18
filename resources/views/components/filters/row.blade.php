@props([
    'index' => 0,
    'fields' => [],
    'operatorLabels' => [],
    'suggestions' => [],
    'showRemove' => true,
    'currentField' => '',
    'currentOperator' => '',
    'currentValue' => ''
])

@php
    $fieldConfig = $fields[$currentField] ?? null;
    $operatorsForField = $fieldConfig['operators'] ?? \App\Services\FilterConfig::defaultOperators();
    $valueHidden = in_array($currentOperator ?? '', ['is_empty', 'is_not_empty', 'has_value', 'no_value'], true);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-end filter-row" data-index="{{ $index }}">
    <div class="sm:col-span-3">
        <select
            name="filters[{{ $index }}][field]"
            class="filter-field-select w-full rounded-xl border border-gray-200 bg-white text-[#0B2C66] py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40 [&>option]:bg-white [&>option]:text-[#0B2C66]"
                @if(isset($attributes['x-model'])) {{ $attributes->except('class') }} @endif
        >
            <option value="">— Campo —</option>
            @foreach($fields as $key => $config)
                <option value="{{ $key }}" {{ (string)$currentField === (string)$key ? 'selected' : '' }}>{{ $config['label'] ?? $key }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-3">
        <select
            name="filters[{{ $index }}][operator]"
            class="filter-operator-select w-full rounded-xl border border-gray-200 bg-white text-[#0B2C66] py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40 [&>option]:bg-white [&>option]:text-[#0B2C66]"
        >
            @foreach($operatorsForField as $op)
                <option value="{{ $op }}" {{ (string)$currentOperator === (string)$op ? 'selected' : '' }}>{{ $operatorLabels[$op] ?? $op }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-6 filter-value-wrap" data-value-wrap="{{ $index }}">
        @if($fieldConfig && ($fieldConfig['type'] ?? '') === 'select' && isset($fieldConfig['options']))
            @if(!empty($fieldConfig['multiple']))
                <select
                    name="filters[{{ $index }}][value][]"
                    multiple
                    class="w-full rounded-xl border border-gray-200 bg-white text-[#0B2C66] py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40 [&>option]:bg-white [&>option]:text-[#0B2C66] h-28"
                    @if($valueHidden) style="visibility: hidden; min-width: 0;" @endif
                >
                    @foreach($fieldConfig['options'] as $optVal => $optLabel)
                        @php
                            $currArr = is_array($currentValue) ? $currentValue : [];
                            $isSelected = is_array($currentValue)
                                ? in_array((string)$optVal, array_map('strval', $currArr), true)
                                : (string)($currentValue ?? '') === (string)$optVal;
                        @endphp
                        <option value="{{ $optVal }}" {{ $isSelected ? 'selected' : '' }}>{{ $optLabel }}</option>
                    @endforeach
                </select>
            @else
                <select
                    name="filters[{{ $index }}][value]"
                    class="w-full rounded-xl border border-gray-200 bg-white text-[#0B2C66] py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40 [&>option]:bg-white [&>option]:text-[#0B2C66]"
                    @if($valueHidden) style="visibility: hidden; min-width: 0;" @endif
                >
                    @foreach($fieldConfig['options'] as $optVal => $optLabel)
                        <option value="{{ $optVal }}" {{ (string)($currentValue ?? '') === (string)$optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                    @endforeach
                </select>
            @endif
        @elseif($fieldConfig && ($fieldConfig['type'] ?? '') === 'boolean' && isset($fieldConfig['options']))
            <select
                name="filters[{{ $index }}][value]"
                class="w-full rounded-xl border border-gray-200 bg-white text-[#0B2C66] py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40 [&>option]:bg-white [&>option]:text-[#0B2C66]"
            >
                @foreach($fieldConfig['options'] as $optVal => $optLabel)
                    <option value="{{ $optVal }}" {{ (string)($currentValue ?? '') === (string)$optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                @endforeach
            </select>
        @elseif($fieldConfig && ($fieldConfig['type'] ?? '') === 'checkbox')
            @php
                $checked = $currentValue === '1' || $currentValue === 1 || $currentValue === true;
            @endphp
            <label class="inline-flex items-center gap-2 text-sm text-white/90 cursor-pointer">
                <input
                    type="checkbox"
                    name="filters[{{ $index }}][value]"
                    value="1"
                    class="rounded border-gray-300 bg-white text-[#0B2C66] focus:ring-[#FFE600]"
                    {{ $checked ? 'checked' : '' }}
                    @if($valueHidden) style="visibility: hidden; min-width: 0;" @endif
                >
                <span>{{ $fieldConfig['options']['1'] ?? $fieldConfig['options'][1] ?? 'Sí' }}</span>
            </label>
        @else
            @php
                $suggestionsForField = $suggestions[(string)($currentField ?? '')] ?? [];
                $listId = 'datalist-' . (string)$index . '-' . (string)($currentField ?? '');
            @endphp
            <input
                type="text"
                name="filters[{{ $index }}][value]"
                value="{{ is_array($currentValue) ? '' : ($currentValue ?? '') }}"
                @if(!empty($suggestionsForField)) list="{{ $listId }}" @endif
                placeholder="Valor..." autocomplete="off"
                class="w-full rounded-xl border border-gray-200 bg-white text-[#0B2C66] py-2 px-3 text-sm placeholder-gray-400 filter-value-input focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40"
                @if($valueHidden) style="visibility: hidden; min-width: 0;" @endif
            >
            @if(!empty($suggestionsForField))
                <datalist id="{{ $listId }}">
                    @foreach($suggestionsForField as $opt)
                        <option value="{{ $opt }}"></option>
                    @endforeach
                </datalist>
            @endif
        @endif
    </div>
    @if($showRemove)
        <div class="sm:col-span-1 flex justify-end">
            <button
                type="button"
                class="filter-remove p-2 rounded-lg text-white/60 hover:text-red-400 hover:bg-white/10 transition-colors"
                title="Quitar filtro"
                aria-label="Quitar filtro"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    @endif
 </div>
