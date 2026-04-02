@props([
    /** @var \Illuminate\Support\Collection<string, int>|iterable<string, int> */
    'counts',
])

@php
    $items = $counts instanceof \Illuminate\Support\Collection ? $counts : collect($counts);
@endphp

@if($items->isNotEmpty())
    <div class="panel-card-dark p-5 md:p-6">
        <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4 text-base md:text-lg">
            Empresas por entidad federativa
        </h3>
        <p class="text-sm text-white/75 mb-4">
            Cantidad de empresas según el estado (Aguascalientes, San Luis Potosí, etc.) en su vista actual.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($items as $entidad => $total)
                <div class="metric-card-dark metric-card-dark--compact h-full">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="metric-card-dark__label text-[0.8125rem] leading-snug line-clamp-3" title="{{ $entidad }}">
                                {{ $entidad }}
                            </p>
                            <p class="metric-card-dark__value tabular-nums">{{ number_format((int) $total) }}</p>
                        </div>
                        <div class="metric-card-dark__icon-wrap shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
