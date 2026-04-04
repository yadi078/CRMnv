@props([
    'title' => 'Cartera',
    'companiesCount' => 0,
    'contactsCount' => 0,
    'footnote' => null,
])

<div {{ $attributes->merge(['class' => 'panel-card-dark p-4 sm:p-5']) }}>
    @if ($title)
        <h4 class="panel-card-dark__title panel-card-dark__title--accent mb-3 text-sm sm:text-base">{{ $title }}</h4>
    @endif
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="metric-card-dark metric-card-dark--compact h-full">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="metric-card-dark__label uppercase tracking-wide text-[0.6875rem] sm:text-xs">Empresas</p>
                    <p class="metric-card-dark__value tabular-nums">{{ number_format(max(0, (int) $companiesCount)) }}</p>
                </div>
                <div class="metric-card-dark__icon-wrap shrink-0 mt-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="metric-card-dark metric-card-dark--compact h-full">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="metric-card-dark__label uppercase tracking-wide text-[0.6875rem] sm:text-xs">Contactos</p>
                    <p class="metric-card-dark__value tabular-nums">{{ number_format(max(0, (int) $contactsCount)) }}</p>
                </div>
                <div class="metric-card-dark__icon-wrap shrink-0 mt-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
    @if ($footnote)
        <p class="text-[10px] sm:text-xs text-white/50 mt-3 leading-snug">{{ $footnote }}</p>
    @endif
</div>
