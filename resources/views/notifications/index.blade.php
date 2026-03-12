<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Notificaciones</h2>
            <p id="notifications-header-subtitle" class="page-header-card__subtitle">
                @if($unreadCount > 0)
                    <span class="font-semibold text-[#FFE600]" data-unread-badge>{{ $unreadCount }} sin leer</span>
                @else
                    Centro de notificaciones
                @endif
            </p>
        </div>
    </x-slot>

    <div class="space-y-8" x-data="{ detailId: null, editingReminderId: null, showReminderModal: false }">
        {{-- Tarjeta de Recordatorios --}}
        <div class="panel-card-dark p-0 overflow-hidden border-2 border-[#FFE600]">
            {{-- Encabezado amarillo como en el diseño --}}
            <div class="flex items-center justify-between px-4 sm:px-6 py-3 bg-[#FFE600] text-[#003366]">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-[#003366] text-[#FFE600]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 22C7.031 22 3 17.969 3 13S7.031 4 12 4s9 4.031 9 9-4.031 9-9 9z" />
                        </svg>
                    </span>
                    <h3 class="font-semibold text-sm sm:text-base">Recordatorios</h3>
                </div>
                <button
                    type="button"
                    @click="showReminderModal = true"
                    class="hidden md:inline-flex items-center gap-2 rounded-full bg-[#003366] text-[#FFE600] px-4 py-2 text-sm font-semibold shadow hover:bg-[#001b4d] transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Agregar recordatorio
                </button>
            </div>

            {{-- Contenido azul de la tarjeta --}}
            <div class="bg-[#0b386a]">
                <div class="border-t border-[#FFE600]/60 px-3 sm:px-4 pt-2 pb-1 hidden md:flex gap-4 text-[11px] text-white/80">
                    <div class="flex-1 pl-9">Título</div>
                    <div class="w-64 text-right pr-2">Fecha y hora</div>
                </div>

                <div class="divide-y divide-white/10">
                @forelse($reminders as $reminder)
                <div class="flex items-center gap-3 px-4 sm:px-5 py-3 hover:bg-white/5 transition-colors">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center
                        {{ $reminder->is_done ? 'bg-emerald-500/20 text-emerald-300' : 'bg-[#FFE600]/15 text-[#FFE600]' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l2.5 2.5M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div x-show="editingReminderId !== {{ $reminder->id }}">
                            <p class="text-sm font-semibold text-white {{ $reminder->is_done ? 'line-through text-white/60' : '' }}">
                                {{ $reminder->title }}
                            </p>
                            @if($reminder->all_day && $reminder->start_at)
                                <p class="text-xs text-white/70 mt-0.5">
                                    Todo el día · {{ $reminder->start_at->format('d M Y') }}
                                </p>
                            @elseif($reminder->start_at)
                                <p class="text-xs text-white/70 mt-0.5">
                                    {{ $reminder->start_at->format('d M Y - H:i') }}
                                    @if($reminder->end_at)
                                        &nbsp;→&nbsp;{{ $reminder->end_at->format('H:i') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        <form
                            x-show="editingReminderId === {{ $reminder->id }}"
                            x-cloak
                            method="POST"
                            action="{{ route('reminders.update', $reminder) }}"
                            class="space-y-1"
                        >
                            @csrf
                            @method('PUT')
                            <input
                                type="text"
                                name="title"
                                value="{{ $reminder->title }}"
                                required
                                maxlength="255"
                                style="color: #111827;"
                                class="w-full rounded-xl border border-gray-300 bg-white placeholder-gray-500 focus:ring-2 focus:ring-[#FFE600]/50 py-1.5 px-3 text-sm"
                            >
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <input
                                        type="datetime-local"
                                        name="start_at"
                                        value="{{ $reminder->start_at ? $reminder->start_at->format('Y-m-d\\TH:i') : '' }}"
                                        style="color: #111827;"
                                        class="rounded-xl border border-gray-300 bg-white placeholder-gray-500 focus:ring-2 focus:ring-[#FFE600]/50 py-1.5 px-3 text-xs"
                                    >
                                    <input
                                        type="datetime-local"
                                        name="end_at"
                                        value="{{ $reminder->end_at ? $reminder->end_at->format('Y-m-d\\TH:i') : '' }}"
                                        style="color: #111827;"
                                        class="rounded-xl border border-gray-300 bg-white placeholder-gray-500 focus:ring-2 focus:ring-[#FFE600]/50 py-1.5 px-3 text-xs"
                                    >
                                    <label class="inline-flex items-center gap-1 text-[11px] text-white/80">
                                        <input type="checkbox" name="all_day" value="1" class="rounded border-white/40 text-[#FFE600] bg-transparent focus:ring-[#FFE600]" {{ $reminder->all_day ? 'checked' : '' }}>
                                        Todo el día
                                    </label>
                                </div>
                                <button type="submit" class="btn-amber-app text-xs py-1 px-3">Guardar</button>
                                <button type="button" @click="editingReminderId = null" class="text-xs text-white/80 hover:text-white">Cancelar</button>
                            </div>
                        </form>
                    </div>
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <form method="POST" action="{{ route('reminders.toggle', $reminder) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full
                                {{ $reminder->is_done ? 'bg-emerald-500 text-white hover:bg-emerald-400' : 'bg-white/10 text-emerald-300 hover:bg-white/20' }}"
                                title="{{ $reminder->is_done ? 'Marcar como pendiente' : 'Marcar como completado' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </form>
                        <button
                            type="button"
                            @click="editingReminderId = editingReminderId === {{ $reminder->id }} ? null : {{ $reminder->id }}"
                            class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20"
                            title="Editar recordatorio"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('reminders.destroy', $reminder) }}" onsubmit="return confirm('¿Eliminar este recordatorio?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full bg-red-500/10 text-red-400 hover:bg-red-500/20" title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="px-5 py-4 text-sm text-white/80">
                    Aún no tienes recordatorios. Crea el primero con el botón <span class="font-semibold text-[#FFE600]">Agregar recordatorio</span>.
                </div>
                @endforelse
                </div>

                {{-- Botón inferior centrado: abre modal --}}
                <div class="border-t border-[#FFE600]/60 px-4 py-3 flex justify-center">
                    <button
                        type="button"
                        @click="showReminderModal = true"
                        class="inline-flex items-center gap-2 rounded-full border border-[#FFE600] text-[#FFE600] px-5 py-2 text-sm font-semibold hover:bg-[#FFE600]/10 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Agregar recordatorio
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal para crear recordatorio --}}
        <div
            x-show="showReminderModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-[#000836]"
            x-transition
            @keydown.escape.window="showReminderModal = false"
        >
            <div
                class="w-full max-w-lg bg-[#0b386a] rounded-2xl shadow-2xl border-2 border-[#FFE600] overflow-hidden"
                @click.outside="showReminderModal = false"
            >
                <div class="px-5 py-3 bg-[#FFE600] flex items-center justify-between text-[#003366]">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-[#003366] text-[#FFE600]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 22C7.031 22 3 17.969 3 13S7.031 4 12 4s9 4.031 9 9-4.031 9-9 9z" />
                            </svg>
                        </span>
                        <h3 class="font-semibold text-sm sm:text-base">Nuevo recordatorio</h3>
                    </div>
                    <button type="button" @click="showReminderModal = false" class="text-[#003366] hover:text-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('reminders.store') }}" class="px-6 py-5 space-y-4 text-white">
                    @csrf
                    <div>
                        <label class="block text-sm mb-1">Título</label>
                        <input
                            type="text"
                            name="title"
                            required
                            maxlength="255"
                            style="color: #111827;"
                            class="w-full rounded-xl border border-gray-300 bg-white placeholder-gray-500 focus:bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm"
                            placeholder="Ej. Llamar al cliente el lunes"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Fecha</label>
                            <input
                                type="date"
                                name="date"
                                style="color: #111827;"
                                class="w-full rounded-xl border border-gray-300 bg-white placeholder-gray-500 focus:bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm"
                            >
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Hora</label>
                            <input
                                type="time"
                                name="time"
                                style="color: #111827;"
                                class="w-full rounded-xl border border-gray-300 bg-white placeholder-gray-500 focus:bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                        <div class="space-y-2">
                            <label class="block text-sm mb-1">Repetir</label>
                            <select
                                name="repeat"
                                style="color: #111827;"
                                class="w-full rounded-xl border border-gray-300 bg-white focus:bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm"
                            >
                                <option value="">No repetir</option>
                                <option value="daily">Diario</option>
                                <option value="weekly">Semanal</option>
                                <option value="monthly">Mensual</option>
                            </select>
                            <label class="inline-flex items-center gap-2 text-xs text-white/80 mt-1">
                            <input type="checkbox" name="all_day" value="1" class="rounded border-white/30 text-[#FFE600] bg-white focus:ring-[#FFE600]">
                                Recordatorio todo el día
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Fecha límite</label>
                            <input
                                type="date"
                                name="deadline_date"
                                style="color: #111827;"
                                class="w-full rounded-xl border border-gray-300 bg-white placeholder-gray-500 focus:bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Detalles del recordatorio</label>
                        <textarea
                            name="description"
                            rows="3"
                            style="color: #111827;"
                            class="w-full rounded-xl border border-gray-300 bg-white placeholder-gray-500 focus:bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm"
                            placeholder="Notas adicionales, contexto o instrucciones..."
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button
                            type="button"
                            @click="showReminderModal = false"
                            class="px-4 py-2 text-sm rounded-xl border border-white/30 text-white/90 hover:bg-white/10"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2 text-sm rounded-xl bg-[#FFE600] text-[#003366] font-semibold hover:bg-[#e6cf00]"
                        >
                            Guardar recordatorio
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tarjeta de filtros + iconos refrescar y menú alineados a la derecha --}}
        <div class="panel-card-dark">
            <form method="GET" action="{{ route('notifications.index') }}" id="notifications-filter-form" class="flex flex-wrap items-center justify-between gap-4">
                <input type="hidden" name="orden" value="{{ $sort }}">
                <input type="hidden" name="filtro" id="notif-filtro-input" value="{{ $filter }}">
                <div class="flex flex-wrap items-center gap-4">
                    <span class="text-sm font-semibold text-[#FFE600]">Mostrar:</span>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="filtro_todas" value="1" class="rounded border-white/40 text-[#FFE600] bg-transparent focus:ring-[#FFE600]"
                            {{ $filter === 'todas' ? 'checked' : '' }} data-filtro-value="todas">
                        <span class="text-sm text-white">Todas</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="filtro_leidas" value="1" class="rounded border-white/40 text-[#FFE600] bg-transparent focus:ring-[#FFE600]"
                            {{ $filter === 'leidas' ? 'checked' : '' }} data-filtro-value="leidas">
                        <span class="text-sm text-white">Leídas</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="filtro_no_leidas" value="1" class="rounded border-white/40 text-[#FFE600] bg-transparent focus:ring-[#FFE600]"
                            {{ $filter === 'no_leidas' ? 'checked' : '' }} data-filtro-value="no_leidas">
                        <span class="text-sm text-white">No leídas</span>
                        @if($unreadCount > 0)
                            <span class="text-xs font-semibold text-[#FFE600]">({{ $unreadCount }})</span>
                        @endif
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="filtro_destacadas" value="1" class="rounded border-white/40 text-[#FFE600] bg-transparent focus:ring-[#FFE600]"
                            {{ $filter === 'destacadas' ? 'checked' : '' }} data-filtro-value="destacadas">
                        <span class="text-sm text-white">Destacadas</span>
                        @if($starredCount > 0)
                            <span class="text-xs font-semibold text-[#FFE600]">({{ $starredCount }})</span>
                        @endif
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="filtro_sin_destacar" value="1" class="rounded border-white/40 text-[#FFE600] bg-transparent focus:ring-[#FFE600]"
                            {{ $filter === 'sin_destacar' ? 'checked' : '' }} data-filtro-value="sin_destacar">
                        <span class="text-sm text-white">Sin destacar</span>
                    </label>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ request()->fullUrl() }}" class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-white text-[#003366] shadow-md hover:shadow-lg hover:bg-white/90 border-2 border-[#FFE600] transition-all" title="Refrescar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </a>
                    <div class="relative overflow-visible" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-white text-[#003366] shadow-md hover:shadow-lg hover:bg-white/90 border-2 border-[#FFE600] transition-all" aria-label="Más opciones" aria-haspopup="true" :aria-expanded="open">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.outside="open = false"
                             class="absolute right-0 mt-1 w-56 rounded-xl shadow-lg bg-white border border-[#E2E8F0] py-1 z-[100] min-w-[14rem]">
                            <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="block js-mark-all-read-form">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-[#1F2937] hover:bg-gray-100">Marcar todas como leídas</button>
                            </form>
                            <a href="{{ route('notifications.index', ['filtro' => request('filtro'), 'orden' => 'fecha']) }}" class="block px-4 py-2 text-sm text-[#1F2937] hover:bg-gray-100">Ordenar por fecha</a>
                            <a href="{{ route('notifications.index', ['filtro' => request('filtro'), 'orden' => 'alfabetico']) }}" class="block px-4 py-2 text-sm text-[#1F2937] hover:bg-gray-100">Ordenar alfabéticamente</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('notifications-filter-form');
            var hidden = document.getElementById('notif-filtro-input');
            if (!form || !hidden) return;
            form.querySelectorAll('input[type="checkbox"][data-filtro-value]').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    var value = this.getAttribute('data-filtro-value');
                    form.querySelectorAll('input[type="checkbox"][data-filtro-value]').forEach(function(o) { o.checked = (o.getAttribute('data-filtro-value') === value); });
                    hidden.value = value;
                    form.submit();
                });
            });
        });
        </script>

        {{-- Tarjeta lista de notificaciones --}}
        <div class="panel-card-dark p-0 overflow-hidden">
            @if($notifications->total() > 0)
            <div class="flex items-center justify-between text-sm px-4 sm:px-5 py-3 m-3 rounded-xl border-4 border-[#FFE600] bg-white text-[#003366]">
                <span class="font-medium">
                    {{ $notifications->firstItem() }}-{{ $notifications->lastItem() }} de {{ $notifications->total() }}
                </span>
                <div class="flex gap-1">
                    @if($notifications->onFirstPage())
                        <span class="px-2 py-1 rounded text-gray-300 cursor-not-allowed">←</span>
                    @else
                        <a href="{{ $notifications->previousPageUrl() }}" class="px-2 py-1 rounded hover:bg-[#1a3d6b]/10 text-[#003366]">←</a>
                    @endif
                    @if($notifications->hasMorePages())
                        <a href="{{ $notifications->nextPageUrl() }}" class="px-2 py-1 rounded hover:bg-[#1a3d6b]/10 text-[#003366]">→</a>
                    @else
                        <span class="px-2 py-1 rounded text-gray-300 cursor-not-allowed">→</span>
                    @endif
                </div>
            </div>
            @endif
            <div class="m-3 rounded-xl border border-[#FFE600]/80 overflow-hidden divide-y divide-[#FFE600]/60">
            @forelse($notifications as $notification)
                @php
                    $d = is_array($notification->data) ? $notification->data : [];
                    $titulo = $d['titulo'] ?? $d['message'] ?? $d['contact_name'] ?? 'Notificación';
                    $mensaje = $d['mensaje'] ?? $d['message'] ?? '';
                    $tipo = $d['tipo'] ?? 'general';
                    $isUnread = !$notification->read_at;
                    $isStarred = !empty($notification->starred);
                @endphp
                <div class="notification-row flex items-start gap-3 px-4 sm:px-5 py-3 hover:bg-white/5 transition-colors {{ $isUnread ? 'bg-white/10' : 'bg-transparent' }}"
                     role="button"
                     data-notification-id="{{ $notification->id }}"
                     data-unread="{{ $isUnread ? '1' : '0' }}"
                     @click="detailId = '{{ $notification->id }}'">
                    {{-- Estrella (destacada) - antes del icono tipo --}}
                    <div class="flex-shrink-0 pt-1" @click.stop>
                        @if($isStarred)
                            <form method="POST" action="{{ route('notifications.unstar', $notification) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-[#FFE600] hover:text-[#FFD700] text-2xl leading-none" title="Quitar de destacadas">★</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('notifications.star', $notification) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-white/50 hover:text-amber-300 text-2xl leading-none" title="Destacar">☆</button>
                            </form>
                        @endif
                    </div>
                    {{-- Icono tipo --}}
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                        @if($tipo === 'registro') bg-indigo-100 text-indigo-700
                        @elseif($tipo === 'contacto') bg-emerald-100 text-emerald-700
                        @elseif($tipo === 'aprobacion') bg-emerald-100 text-emerald-700
                        @elseif($tipo === 'recordatorio') bg-amber-100 text-amber-700
                        @else bg-white/10 text-white @endif">
                        @if($tipo === 'registro')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        @elseif($tipo === 'contacto')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        @elseif($tipo === 'aprobacion')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @elseif($tipo === 'recordatorio')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        @endif
                    </div>
                    {{-- Contenido --}}
                    <div class="flex-1 min-w-0">
                        <p class="notification-row-title text-sm {{ $isUnread ? 'font-semibold text-white' : 'text-white' }}">{{ $titulo }}</p>
                        <p class="text-sm text-white/80 truncate mt-0.5">{{ $mensaje ?: 'Sin descripción' }}</p>
                        <p class="text-xs text-white/60 mt-1">{{ $notification->created_at->format('d M Y H:i') }}</p>
                    </div>
                    {{-- Acciones rápidas: iconos con color, ojo según estado de lectura --}}
                    <div class="flex-shrink-0 flex items-center gap-3" @click.stop>
                        @if($isUnread)
                            <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="inline js-mark-read-form" data-notification-id="{{ $notification->id }}">
                                @csrf
                                <button type="submit" class="flex items-center justify-center w-8 h-8 text-cyan-400 hover:text-cyan-300 transition-colors" title="Marcar como leída">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </button>
                            </form>
                        @else
                            <span class="flex items-center justify-center w-8 h-8 text-emerald-400" title="Leída">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                        @endif
                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="inline" onsubmit="return confirm('¿Eliminar esta notificación?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center justify-center w-8 h-8 text-red-400 hover:text-red-300 transition-colors" title="Eliminar">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="mx-4 my-8 rounded-2xl border-2 border-[#FFE600]/70 border-l-4 border-l-[#FFE600] bg-white/5 px-6 py-12 flex items-center justify-center min-h-[140px]">
                    <p class="text-white font-medium text-center">No hay notificaciones todavía.</p>
                </div>
            @endforelse
            </div>
        </div>

        <div class="mt-6">
            {{ $notifications->withQueryString()->links() }}
        </div>

        {{-- Modal detalle --}}
        @foreach($notifications as $notification)
            @php
                $d = is_array($notification->data) ? $notification->data : [];
                $titulo = $d['titulo'] ?? $d['message'] ?? $d['contact_name'] ?? 'Notificación';
                $mensaje = $d['mensaje'] ?? $d['message'] ?? '';
                $tipo = $d['tipo'] ?? 'general';
                $entrarUrl = $d['entrar_url'] ?? null;
            @endphp
            <div x-show="detailId === '{{ $notification->id }}'"
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                 x-transition
                 @keydown.escape.window="detailId = null">
                <div @click.outside="detailId = null"
                     class="relative w-full max-w-md bg-[#1a3d6b] rounded-2xl shadow-xl border-4 border-[#FFE600] max-h-[80vh] overflow-hidden flex flex-col">
                    <div class="p-8 text-center">
                        <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center mb-4 bg-white text-[#FFE600]">
                            @if($tipo === 'registro')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                            @elseif($tipo === 'contacto')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            @elseif($tipo === 'aprobacion')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @elseif($tipo === 'recordatorio')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-[#FFE600]">{{ $titulo }}</h3>
                        <p class="text-sm text-white/70 mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="px-8 pb-6 overflow-y-auto flex-1 text-center">
                        <p class="text-white/90">{{ $mensaje ?: 'Sin contenido.' }}</p>
                    </div>
                    <div class="p-6 pt-4 flex flex-wrap gap-3 justify-end">
                        @if($entrarUrl)
                            <a href="{{ $entrarUrl }}" class="px-4 py-2 text-sm rounded-xl font-semibold text-gray-900 bg-[#FFE600] hover:bg-[#E6CF00] transition-colors">Entrar a mi panel</a>
                        @endif
                        @if(!$notification->read_at)
                            <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm rounded-xl font-semibold text-gray-900 bg-[#FFE600] hover:bg-[#E6CF00] transition-colors">Marcar como leída</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="inline" onsubmit="return confirm('¿Eliminar?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-sm rounded-xl font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors">Eliminar</button>
                        </form>
                        <button type="button" @click="detailId = null" class="px-4 py-2 text-sm rounded-xl font-semibold text-gray-900 bg-[#FFE600] hover:bg-[#E6CF00] transition-colors">Cerrar</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
    <script>
    window.__reminderAlertSeenIds = @json($reminderAlertIds ?? []);
    (function() {
        var csrfToken = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var unreadCountUrl = '{{ route("notifications.unread-count") }}';
        var reminderAlertsUrl = '{{ route("notifications.reminder-alerts") }}';
        var seenReminderIds = new Set((window.__reminderAlertSeenIds || []).map(String));

        function playAlarmBeep() {
            try {
                var ctx = new (window.AudioContext || window.webkitAudioContext)();
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = 880;
                osc.type = 'sine';
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.5);
                setTimeout(function() {
                    var o2 = ctx.createOscillator();
                    var g2 = ctx.createGain();
                    o2.connect(g2);
                    g2.connect(ctx.destination);
                    o2.frequency.value = 660;
                    o2.type = 'sine';
                    g2.gain.setValueAtTime(0.25, ctx.currentTime);
                    g2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                    o2.start(ctx.currentTime);
                    o2.stop(ctx.currentTime + 0.4);
                }, 220);
            } catch (e) {}
        }

        function showReminderBrowserNotification(titulo, mensaje) {
            if (!('Notification' in window)) return;
            if (Notification.permission === 'granted') {
                new Notification(titulo || 'Recordatorio', {
                    body: mensaje || 'Tienes un recordatorio pendiente.',
                    icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23FFE600"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                    tag: 'crm-reminder'
                });
            }
        }

        function checkReminderAlerts() {
            if (document.visibilityState !== 'visible') return;
            fetch(reminderAlertsUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var items = data.items || [];
                    var hasNew = false;
                    items.forEach(function(item) {
                        var id = String(item.id);
                        if (id && !seenReminderIds.has(id)) {
                            seenReminderIds.add(id);
                            hasNew = true;
                            playAlarmBeep();
                            showReminderBrowserNotification(item.titulo, item.mensaje || item.fecha_prevista || '');
                        }
                    });
                    // Al detectar una alarma nueva, recargar para que la notificación aparezca en la lista
                    if (hasNew) {
                        setTimeout(function() { window.location.reload(); }, 1200);
                    }
                })
                .catch(function() {});
        }

        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        function updateHeaderSubtitle(count) {
            var el = document.getElementById('notifications-header-subtitle');
            if (el) el.textContent = count === 0 ? 'Centro de notificaciones' : count + ' sin leer';
        }

        function setRowAsRead(row) {
            if (!row) return;
            row.setAttribute('data-unread', '0');
            row.classList.remove('bg-white/10');
            row.classList.add('bg-transparent');
            var title = row.querySelector('.notification-row-title');
            if (title) title.classList.remove('font-semibold');
            var markReadForm = row.querySelector('.js-mark-read-form');
            if (markReadForm) markReadForm.remove();
        }

        function updateBadgeAndSubtitle() {
            if (typeof window.updateNotificationBadge === 'function') window.updateNotificationBadge();
            fetch(unreadCountUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var count = data.unread_count || 0;
                    updateHeaderSubtitle(count);
                })
                .catch(function() {});
        }

        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (form.classList.contains('js-mark-read-form')) {
                e.preventDefault();
                var row = form.closest('.notification-row');
                var action = form.getAttribute('action');
                var formData = new FormData(form);
                fetch(action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken || formData.get('_token') || ''
                    }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        setRowAsRead(row);
                        updateHeaderSubtitle(data.unread_count);
                        if (typeof window.updateNotificationBadge === 'function') window.updateNotificationBadge();
                    }
                })
                .catch(function() {
                    form.submit();
                });
                return false;
            }

            if (form.classList.contains('js-mark-all-read-form')) {
                e.preventDefault();
                var action = form.getAttribute('action');
                var formData = new FormData(form);
                fetch(action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken || formData.get('_token') || ''
                    }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        document.querySelectorAll('.notification-row[data-unread="1"]').forEach(setRowAsRead);
                        updateHeaderSubtitle(0);
                        if (typeof window.updateNotificationBadge === 'function') window.updateNotificationBadge();
                    }
                })
                .catch(function() {
                    form.submit();
                });
                return false;
            }
        });

        setInterval(function() {
            if (document.visibilityState === 'visible') {
                fetch(unreadCountUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.unread_count !== undefined) updateHeaderSubtitle(data.unread_count);
                    })
                    .catch(function() {});
            }
        }, 30000);

        setInterval(checkReminderAlerts, 30000);
        setTimeout(checkReminderAlerts, 2000);
    })();
    </script>
    @endpush
</x-app-layout>
