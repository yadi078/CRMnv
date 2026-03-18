@props(['filters' => [], 'clearUrl' => ''])

@if(count($filters) > 0)
<div class="flex flex-wrap items-center gap-2 mb-4">
    <span class="text-sm text-white/70 mr-1">Filtros activos ({{ count($filters) }}):</span>
    @foreach($filters as $filter)
        @if(!empty($filter['field']) && !empty($filter['operator']))
            @php
                $fieldLabel = $filter['field_label'] ?? $filter['field'];
                $opLabel = $filter['operator_label'] ?? $filter['operator'];
                $valDisplay = isset($filter['value']) && $filter['value'] !== '' ? (is_array($filter['value']) ? implode(', ', $filter['value']) : $filter['value']) : '';
                $chipLabel = $fieldLabel . ' ' . $opLabel . ($valDisplay !== '' ? ' "' . \Illuminate\Support\Str::limit($valDisplay, 25) . '"' : '');
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#FFE600]/20 text-[#FFE600] text-xs font-medium border border-[#FFE600]/40">{{ $chipLabel }}</span>
        @endif
    @endforeach
    @if($clearUrl)
    <a href="{{ $clearUrl }}" class="text-xs text-white/60 hover:text-[#FFE600] transition-colors">Limpiar todos</a>
    @endif
</div>
@endif
