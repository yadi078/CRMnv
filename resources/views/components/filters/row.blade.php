@props(['index' => 0, 'fields' => [], 'operatorLabels' => [], 'currentField' => '', 'currentOperator' => '', 'currentValue' => ''])

@php
    $fieldConfig = $fields[$currentField] ?? null;
    $operatorsForField = $fieldConfig['operators'] ?? \App\Services\FilterConfig::defaultOperators();
    $valueHidden = in_array($currentOperator ?? '', ['is_empty', 'is_not_empty', 'has_value', 'no_value'], true);
@endphp

<div class="flex flex-wrap items-end gap-2 filter-row" data-index="{{ $index }}">
    <div class="flex-shrink-0" style="min-width: 160px;">
        <select name="filters[{{ $index }}][field]" class="filter-field-select w-full rounded-xl border-0 bg-white/15 text-white py-2 px-3 text-sm [&>option]:bg-[#1a3d6b]"
                @if(isset($attributes['x-model'])) {{ $attributes->except('class') }} @endif
        >
            <option value="">— Campo —</option>
            @foreach($fields as $key => $config)
                <option value="{{ $key }}" {{ (string)$currentField === (string)$key ? 'selected' : '' }}>{{ $config['label'] ?? $key }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex-shrink-0" style="min-width: 140px;">
        <select name="filters[{{ $index }}][operator]" class="filter-operator-select w-full rounded-xl border-0 bg-white/15 text-white py-2 px-3 text-sm [&>option]:bg-[#1a3d6b]">
            @foreach($operatorsForField as $op)
                <option value="{{ $op }}" {{ (string)$currentOperator === (string)$op ? 'selected' : '' }}>{{ $operatorLabels[$op] ?? $op }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex-1 min-w-[140px] filter-value-wrap">
        @if($fieldConfig && ($fieldConfig['type'] ?? '') === 'select' && isset($fieldConfig['options']))
            <select name="filters[{{ $index }}][value]" class="w-full rounded-xl border-0 bg-white/15 text-white py-2 px-3 text-sm [&>option]:bg-[#1a3d6b]">
                @foreach($fieldConfig['options'] as $optVal => $optLabel)
                    <option value="{{ $optVal }}" {{ (string)($currentValue ?? '') === (string)$optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                @endforeach
            </select>
        @elseif($fieldConfig && ($fieldConfig['type'] ?? '') === 'boolean' && isset($fieldConfig['options']))
            <select name="filters[{{ $index }}][value]" class="w-full rounded-xl border-0 bg-white/15 text-white py-2 px-3 text-sm [&>option]:bg-[#1a3d6b]">
                @foreach($fieldConfig['options'] as $optVal => $optLabel)
                    <option value="{{ $optVal }}" {{ (string)($currentValue ?? '') === (string)$optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                @endforeach
            </select>
        @else
            <input type="text" name="filters[{{ $index }}][value]" value="{{ is_array($currentValue) ? '' : ($currentValue ?? '') }}"
                   placeholder="Valor..." class="w-full rounded-xl border-0 bg-white/15 text-white py-2 px-3 text-sm placeholder-white/50 filter-value-input"
                   @if($valueHidden) style="visibility: hidden; min-width: 0;" @endif
            >
        @endif
    </div>
    <button type="button" class="filter-remove flex-shrink-0 p-2 rounded-lg text-white/60 hover:text-red-400 hover:bg-white/10 transition-colors" title="Quitar filtro" aria-label="Quitar filtro">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
    </button>
</div>
