@props([
    'executiveUsers',
    'isAdmin',
    'selectedAssignedUserId',
    'readonlyExecutiveName',
    'inputId' => 'assigned_user_id_field',
    'labelClass' => '',
    'selectClass' => '',
    'readonlyClass' => '',
    'hint' => null,
])
@php
    $sel = old('assigned_user_id', $selectedAssignedUserId);
    $sel = $sel !== null && $sel !== '' ? (int) $sel : null;
    $defaultSelect = 'mt-1 block w-full rounded-md border-gray-300 dark:border-white/20 dark:bg-white/10 dark:text-white';
    $defaultReadonly = 'mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-900 dark:bg-white/10 dark:text-white select-text cursor-default';
@endphp
<div {{ $attributes->merge(['class' => '']) }}>
    <x-input-label :for="$inputId" value="Ejecutivo asignado" class="{{ $labelClass }}" />
    @if($isAdmin)
        <select
            id="{{ $inputId }}"
            name="assigned_user_id"
            class="{{ $selectClass !== '' ? $selectClass : $defaultSelect }}"
        >
            @forelse($executiveUsers as $u)
                <option value="{{ $u->id }}" @selected($sel === (int) $u->id)>
                    {{ $u->name }}@if($u->email) — {{ $u->email }} @endif
                </option>
            @empty
                <option value="" disabled selected>No hay ejecutivos (rol usuario) dados de alta</option>
            @endforelse
        </select>
        @if($hint)
            <p class="mt-1 text-xs text-white/60">{{ $hint }}</p>
        @endif
    @else
        <input type="hidden" name="assigned_user_id" value="{{ $sel ?? auth()->id() }}" />
        <input
            type="text"
            id="{{ $inputId }}"
            class="{{ $readonlyClass !== '' ? $readonlyClass . ' select-text cursor-default' : $defaultReadonly }}"
            value="{{ $readonlyExecutiveName }}"
            readonly
            title="Solo lectura: puede seleccionar y copiar el texto"
        />
    @endif
</div>
