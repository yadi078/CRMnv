@props([
    'fallback' => null,
    'compact' => false,
])
@php
    $crmBackFallback = $fallback ?? (auth()->user()->esAdmin() ? route('dashboard') : route('user.dashboard'));
@endphp
<button
    type="button"
    @class([
        'inline-flex items-center justify-center gap-2 rounded-xl border-2 border-[#FFE600] bg-white/10 text-white text-sm font-medium hover:bg-white/20 shrink-0',
        'min-w-[44px] min-h-[44px] p-2.5' => $compact,
        'px-3 sm:px-4 py-2' => ! $compact,
    ])
    data-crm-back="{{ $crmBackFallback }}"
    aria-label="Volver a la página anterior"
>
    <svg class="shrink-0 {{ $compact ? 'w-6 h-6' : 'w-5 h-5' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
    </svg>
    @if($compact)
        <span class="sr-only">Volver</span>
    @else
        <span>Volver</span>
    @endif
</button>
