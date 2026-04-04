@props([
    /** @var \Illuminate\Support\Collection<string, int>|iterable<string, int> */
    'counts',
])

@php
    $items = $counts instanceof \Illuminate\Support\Collection ? $counts : collect($counts);
@endphp

@if($items->isNotEmpty())
    <div class="panel-card-dark p-3 md:p-4">
        <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-2 text-base md:text-lg">
            Empresas por entidad federativa
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-x-1.5 gap-y-1">
            @foreach($items as $entidad => $total)
                <div class="metric-card-dark metric-card-dark--compact metric-card-dark--estado-tile h-full">
                    <div class="flex items-start justify-between gap-1.5">
                        <div class="min-w-0 flex-1">
                            <p class="metric-card-dark__label text-[0.6875rem] leading-tight line-clamp-2 uppercase tracking-wide" title="{{ $entidad }}">
                                {{ $entidad }}
                            </p>
                            <p class="metric-card-dark__value tabular-nums">{{ number_format((int) $total) }}</p>
                        </div>
                        <div class="metric-card-dark__icon-wrap shrink-0 mt-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
