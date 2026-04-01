@props([
    'company',
    /** @var string 'admin' | 'user' | 'filtros' */
    'variant' => 'admin',
])
@php
    $spanClass = match ($variant) {
        'user' => 'px-2 py-1 text-xs font-semibold rounded',
        'filtros' => 'px-2 py-0.5 text-xs rounded-lg',
        default => 'px-2.5 py-1 text-xs font-medium rounded-lg',
    };
@endphp
<div {{ $attributes->class(['flex flex-wrap gap-1.5 items-center']) }}>
    @foreach($company->prospectStatusBadgesForList() as $badge)
        <span class="{{ $spanClass }} badge-prospect-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
    @endforeach
</div>
