{{-- Aviso visible para el usuario al que se denegó una solicitud de eliminación --}}
@props([
    'note' => '',
    'resolvedAt' => null,
    'entityLabel' => 'registro',
])
@if(filled($note))
<div {{ $attributes->merge(['class' => 'rounded-2xl border-2 border-red-400/50 bg-red-950/40 p-4 sm:p-5 text-white']) }} role="status">
    <h3 class="text-base font-bold text-[#FFE600] flex items-center gap-2">
        <svg class="w-5 h-5 shrink-0 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        Eliminación no aprobada ({{ $entityLabel }})
    </h3>
    <p class="text-sm text-white/85 mt-2">Un administrador rechazó la solicitud de eliminación e indicó lo siguiente:</p>
    <p class="mt-3 text-sm sm:text-base text-white bg-black/20 rounded-xl px-3 py-2 border border-white/10 whitespace-pre-wrap">{{ $note }}</p>
    @if($resolvedAt)
        <p class="text-xs text-white/60 mt-3">Resuelto el {{ $resolvedAt instanceof \Carbon\Carbon ? $resolvedAt->format('d/m/Y H:i') : $resolvedAt }}</p>
    @endif
</div>
@endif
