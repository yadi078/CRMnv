@props(['type' => 'success', 'message' => '', 'autoClose' => true, 'duration' => 7000])

@php
    $typeClasses = [
        'success' => [
            'bg' => 'bg-[#F0FDF4]',
            'border' => 'border-[#15803D]/30',
            'text' => 'text-[#15803D]',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconColor' => 'text-[#15803D]'
        ],
        'error' => [
            'bg' => 'bg-[#FEF2F2]',
            'border' => 'border-[#B91C1C]/30',
            'text' => 'text-[#B91C1C]',
            'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconColor' => 'text-[#B91C1C]'
        ],
        'warning' => [
            'bg' => 'bg-[#FFFBEB]',
            'border' => 'border-[#B45309]/30',
            'text' => 'text-[#B45309]',
            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'iconColor' => 'text-[#B45309]'
        ],
        'info' => [
            'bg' => 'bg-[rgba(0,51,102,0.06)]',
            'border' => 'border-[#003366]/20',
            'text' => 'text-[#003366]',
            'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconColor' => 'text-[#003366]'
        ],
    ];
    
    $classes = $typeClasses[$type] ?? $typeClasses['success'];
@endphp

<div 
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    @if($autoClose)
    x-init="setTimeout(() => show = false, {{ $duration }})"
    @endif
    class="fixed top-20 right-6 z-50 max-w-md w-full mx-4"
    style="display: block;"
>
    <div class="rounded-2xl {{ $classes['bg'] }} border {{ $classes['border'] }} shadow-[0_10px_30px_rgba(0,0,0,0.06)] px-4 py-3 flex items-start gap-3">
        <div class="flex-shrink-0 mt-0.5">
            <svg class="w-5 h-5 {{ $classes['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $classes['icon'] }}" />
            </svg>
        </div>
        <div class="flex-1 {{ $classes['text'] }} text-sm font-medium">
            {{ $message }}
        </div>
        <button 
            @click="show = false"
            class="flex-shrink-0 text-[#6B7280] hover:text-[#1F2937] transition-colors p-1 rounded-lg hover:bg-black/5"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
