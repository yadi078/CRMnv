@props(['type' => 'info', 'message', 'secondaryUrl' => null, 'secondaryLabel' => null])

@if($message)
@php
    $config = match($type) {
        'success' => [
            'title' => 'Éxito',
            'icon' => 'M5 13l4 4L19 7',
            'iconBg' => 'bg-emerald-500',
        ],
        'error' => [
            'title' => 'Error',
            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'iconBg' => 'bg-red-500',
        ],
        'warning' => [
            'title' => 'Advertencia',
            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'iconBg' => 'bg-amber-500',
        ],
        default => [
            'title' => 'Información',
            'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconBg' => 'bg-blue-500',
        ],
    };
@endphp
<div x-data="{ show: true }"
     x-show="show"
     x-cloak
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
     @keydown.escape.window="show = false"
     role="alertdialog"
     aria-modal="true">
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="w-full max-w-md bg-[#1a3d6b] rounded-2xl shadow-xl p-8 text-center border-4 border-[#FFE600]"
         @click.outside="show = false">
        <div class="mx-auto w-16 h-16 rounded-full {{ $config['iconBg'] }} flex items-center justify-center shadow-lg mb-6">
            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $config['icon'] }}" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">{{ $config['title'] }}</h3>
        <p class="text-white/90 text-sm mb-6">{{ $message }}</p>
        @if($secondaryUrl && $secondaryLabel)
            <a href="{{ $secondaryUrl }}"
               class="mb-4 w-full inline-flex items-center justify-center py-3 px-4 rounded-xl font-semibold text-[#FFE600] border-2 border-[#FFE600]/90 hover:bg-[#FFE600]/10 focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-[#1a3d6b] transition-colors text-sm">
                {{ $secondaryLabel }}
            </a>
        @endif
        <button type="button"
                @click="show = false"
                class="w-full py-3 px-4 rounded-xl font-semibold text-gray-900 bg-[#FFE600] hover:bg-[#E6CF00] focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-[#1a3d6b] transition-colors">
            Aceptar
        </button>
    </div>
</div>
@endif
