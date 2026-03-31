@props([
    'entity',
    'id',
])
@php
    $entity = in_array($entity, ['contact', 'company'], true) ? $entity : 'contact';
@endphp
<td class="crm-row-marker-cell w-11 min-w-[2.75rem] max-w-[2.75rem] px-1 py-3 align-middle text-center border-r border-white/10">
    <button
        type="button"
        class="crm-row-marker mx-auto flex h-9 w-9 items-center justify-center rounded-lg border-2 border-white/30 bg-white/[0.06] text-[#FFE600] shadow-sm transition hover:border-[#FFE600]/70 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#FFE600]/45"
        data-crm-marker-entity="{{ $entity }}"
        data-crm-marker-id="{{ (string) $id }}"
        data-state="none"
        aria-pressed="false"
        aria-label="Seguimiento de revisión"
        title="Clic: sin marcar → en proceso → revisado (se guarda en este navegador)"
    >
        <span class="crm-row-marker__empty inline-block h-3.5 w-3.5 rounded border-2 border-white/45" aria-hidden="true"></span>
        <svg class="crm-row-marker__arrow h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
        <svg class="crm-row-marker__check h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
        </svg>
    </button>
</td>
