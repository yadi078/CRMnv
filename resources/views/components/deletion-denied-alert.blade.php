{{-- Aviso visible para el usuario al que se denegó una solicitud de eliminación --}}
@props([
    'note' => '',
    'resolvedAt' => null,
    'entityLabel' => 'registro',
    'dismissStorageKey' => null,
])
@php
    $resolvedStr = $resolvedAt instanceof \Carbon\Carbon
        ? $resolvedAt->toIso8601String()
        : (string) $resolvedAt;
    $storageKey = $dismissStorageKey ?? 'crm_deletion_denied_v1_' . md5($entityLabel . '|' . $note . '|' . $resolvedStr);
@endphp
@if(filled($note))
<div
    {{ $attributes->merge(['class' => 'rounded-2xl border-2 border-red-400/50 bg-red-950/40 p-4 sm:p-5 text-white']) }}
    x-data="{
        dismissed: false,
        storageKey: @js($storageKey),
        init() {
            try {
                if (localStorage.getItem(this.storageKey) === '1') {
                    this.dismissed = true;
                }
            } catch (e) {}
        },
        dismiss() {
            this.dismissed = true;
            try {
                localStorage.setItem(this.storageKey, '1');
            } catch (e) {}
        }
    }"
    x-show="!dismissed"
    x-cloak
    x-transition.opacity.duration.200ms
    role="status"
>
    <div class="flex items-start justify-between gap-3">
        <h3 class="text-base font-bold text-[#FFE600] flex items-center gap-2 min-w-0">
            <svg class="w-5 h-5 shrink-0 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>Eliminación no aprobada ({{ $entityLabel }})</span>
        </h3>
        <button
            type="button"
            @click="dismiss()"
            class="shrink-0 flex h-9 w-9 items-center justify-center rounded-full border border-emerald-400/60 bg-emerald-600/30 text-emerald-200 hover:bg-emerald-500/40 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400/50"
            title="Entendido, ocultar aviso"
            aria-label="Entendido, ocultar este aviso"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </button>
    </div>
    <p class="text-sm text-white/85 mt-2">Un administrador rechazó la solicitud de eliminación e indicó lo siguiente:</p>
    <p class="mt-3 text-sm sm:text-base text-white bg-black/20 rounded-xl px-3 py-2 border border-white/10 whitespace-pre-wrap">{{ $note }}</p>
    @if($resolvedAt)
        <p class="text-xs text-white/60 mt-3">Resuelto el {{ $resolvedAt instanceof \Carbon\Carbon ? $resolvedAt->format('d/m/Y H:i') : $resolvedAt }}</p>
    @endif
</div>
@endif
