<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg></x-page-header-avatar>
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

    <div class="space-y-8" x-data="{
        detailId: null,
        selectedNotificationIds: [],
        notificationPageIds: @js($notifications->pluck('id')->values()->all()),
        bulkReadUrl: @js(route('notifications.bulk-read')),
        bulkDeleteUrl: @js(route('notifications.bulk-delete')),
        isSelectedNotification(id) {
            return this.selectedNotificationIds.includes(String(id));
        },
        toggleNotificationSelection(id) {
            const key = String(id);
            if (this.selectedNotificationIds.includes(key)) {
                this.selectedNotificationIds = this.selectedNotificationIds.filter(v => v !== key);
            } else {
                this.selectedNotificationIds.push(key);
            }
        },
        toggleSelectAllNotifications() {
            const pageIds = this.notificationPageIds.map(String);
            const allSelected = pageIds.length > 0 && pageIds.every(id => this.selectedNotificationIds.includes(id));
            if (allSelected) {
                this.selectedNotificationIds = this.selectedNotificationIds.filter(id => !pageIds.includes(id));
            } else {
                this.selectedNotificationIds = Array.from(new Set([...this.selectedNotificationIds, ...pageIds]));
            }
        },
        areAllNotificationsSelected() {
            const pageIds = this.notificationPageIds.map(String);
            return pageIds.length > 0 && pageIds.every(id => this.selectedNotificationIds.includes(id));
        },
        csrfToken() {
            var m = document.querySelector('meta[name=\'csrf-token\']');
            return m ? (m.getAttribute('content') || '') : '';
        },
        bulkMarkReadSubmit() {
            if (this.selectedNotificationIds.length === 0) return;
            var tok = this.csrfToken();
            var fd = new FormData();
            fd.append('_token', tok);
            this.selectedNotificationIds.forEach(function (id) { fd.append('notification_ids[]', id); });
            var self = this;
            fetch(this.bulkReadUrl, {
                method: 'POST',
                body: fd,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': tok
                },
                credentials: 'same-origin'
            }).then(function (r) {
                return r.text().then(function (text) {
                    var j = {};
                    try { j = text ? JSON.parse(text) : {}; } catch (e) {}
                    return { ok: r.ok, j: j };
                });
            }).then(function (res) {
                if (res.ok && res.j && res.j.success) {
                    self.selectedNotificationIds.forEach(function (id) {
                        var row = document.querySelector('.notification-row[data-notification-id=\'' + String(id).replace(/'/g, '') + '\']');
                        if (row && window.__crmSetNotificationRowRead) window.__crmSetNotificationRowRead(row);
                    });
                    self.selectedNotificationIds = [];
                    if (res.j.unread_count !== undefined) {
                        var el = document.getElementById('notifications-header-subtitle');
                        if (el) el.textContent = res.j.unread_count === 0 ? 'Centro de notificaciones' : res.j.unread_count + ' sin leer';
                    }
                    if (typeof window.updateNotificationBadge === 'function') window.updateNotificationBadge();
                    return;
                }
                alert('No se pudieron marcar como leídas.');
            }).catch(function () { window.location.reload(); });
        },
        bulkDeleteSubmit() {
            if (this.selectedNotificationIds.length === 0) return;
            if (!confirm('¿Eliminar las notificaciones seleccionadas?')) return;
            var tok = this.csrfToken();
            var fd = new FormData();
            fd.append('_token', tok);
            this.selectedNotificationIds.forEach(function (id) { fd.append('notification_ids[]', id); });
            fetch(this.bulkDeleteUrl, {
                method: 'POST',
                body: fd,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': tok
                },
                credentials: 'same-origin'
            }).then(function (r) {
                return r.text().then(function (text) {
                    var j = {};
                    try { j = text ? JSON.parse(text) : {}; } catch (e) {}
                    return { ok: r.ok, j: j };
                });
            }).then(function (res) {
                if (res.ok && res.j && res.j.success) {
                    window.location.reload();
                    return;
                }
                alert((res.j && res.j.message) ? res.j.message : 'No se pudieron eliminar las notificaciones.');
            }).catch(function () { window.location.reload(); });
        }
    }">
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
                            <a href="{{ route('notifications.index', ['filtro' => request('filtro'), 'orden' => 'recientes']) }}" class="block px-4 py-2 text-sm text-[#1F2937] hover:bg-gray-100">Ordenar por más recientes</a>
                            <a href="{{ route('notifications.index', ['filtro' => request('filtro'), 'orden' => 'antiguas']) }}" class="block px-4 py-2 text-sm text-[#1F2937] hover:bg-gray-100">Ordenar por más antiguas</a>
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

        <div class="panel-card-dark" x-show="selectedNotificationIds.length > 0" x-cloak>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="inline-flex items-center gap-3">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-white">
                        <input type="checkbox" class="rounded border-white/40 text-[#FFE600] bg-transparent focus:ring-[#FFE600]"
                               :checked="areAllNotificationsSelected()"
                               @change="toggleSelectAllNotifications()">
                        Seleccionar todas
                    </label>
                    <span class="text-sm text-[#FFE600] font-semibold" x-text="selectedNotificationIds.length + ' seleccionadas'"></span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="bulkMarkReadSubmit()"
                            class="inline-flex items-center px-4 py-2 text-sm rounded-xl font-semibold text-[#003366] bg-[#FFE600] hover:bg-[#E6CF00] transition-colors">
                        Marcar seleccionadas como leídas
                    </button>
                    <button type="button" @click="bulkDeleteSubmit()"
                            class="inline-flex items-center px-4 py-2 text-sm rounded-xl font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors">
                        Eliminar seleccionadas
                    </button>
                </div>
            </div>
        </div>

        {{-- Tarjeta lista de notificaciones --}}
        <div class="panel-card-dark p-0 overflow-hidden">
            @if($notifications->total() > 0)
            <div class="flex items-center justify-between text-sm px-4 sm:px-5 py-3 m-3 rounded-xl border-4 border-[#FFE600] bg-white text-[#003366]">
                <div class="inline-flex items-center gap-3">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-[#003366]/40 text-[#003366] bg-white focus:ring-[#003366]"
                               :checked="areAllNotificationsSelected()"
                               @change="toggleSelectAllNotifications()">
                        <span class="font-medium">Seleccionar todas</span>
                    </label>
                    <span class="font-medium">
                        {{ $notifications->firstItem() }}-{{ $notifications->lastItem() }} de {{ $notifications->total() }}
                    </span>
                </div>
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
                    $previewMensaje = $tipo === 'recordatorio'
                        ? (\App\Notifications\ReminderDueNotification::reminderSummaryLine($d) ?: ($mensaje ?: 'Sin descripción'))
                        : ($mensaje ?: 'Sin descripción');
                    $isUnread = !$notification->read_at;
                    $isStarred = !empty($notification->starred);
                @endphp
                <div class="notification-row flex items-start gap-3 px-4 sm:px-5 py-3 hover:bg-white/5 transition-colors {{ $isUnread ? 'bg-white/10' : 'bg-transparent' }}"
                     role="button"
                     data-notification-id="{{ $notification->id }}"
                     data-unread="{{ $isUnread ? '1' : '0' }}"
                     @click="detailId = '{{ $notification->id }}'">
                    <div class="flex-shrink-0 pt-2" @click.stop>
                        <input
                            type="checkbox"
                            class="rounded border-white/40 text-[#FFE600] bg-transparent focus:ring-[#FFE600]"
                            :checked="isSelectedNotification('{{ $notification->id }}')"
                            @change="toggleNotificationSelection('{{ $notification->id }}')"
                        >
                    </div>
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
                        @elseif($tipo === 'cumpleanos') bg-pink-100 text-pink-700
                        @elseif($tipo === 'aprobacion') bg-emerald-100 text-emerald-700
                        @elseif($tipo === 'recordatorio') bg-amber-100 text-amber-700
                        @elseif($tipo === 'eliminacion_solicitud') bg-slate-200 text-slate-800
                        @else bg-white/10 text-white @endif">
                        @if($tipo === 'registro')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        @elseif($tipo === 'contacto')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        @elseif($tipo === 'cumpleanos')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112-2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" /></svg>
                        @elseif($tipo === 'aprobacion')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @elseif($tipo === 'recordatorio')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @elseif($tipo === 'eliminacion_solicitud')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        @endif
                    </div>
                    {{-- Contenido --}}
                    <div class="flex-1 min-w-0">
                        <p class="notification-row-title text-sm {{ $isUnread ? 'font-semibold text-white' : 'text-white' }}">{{ $titulo }}</p>
                        <p class="text-sm text-white/80 truncate mt-0.5">{{ $previewMensaje }}</p>
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
                $contactUrl = null;
                if (!empty($d['contact_id'])) {
                    try {
                        $contactUrl = \App\Support\CrmNavigation::withReturn(route('contacts.show', (int) $d['contact_id']));
                    } catch (\Throwable) {
                        $contactUrl = null;
                    }
                }
                $actionUrl = $entrarUrl ?: $contactUrl;
                $solicitudesPendientesUrl = null;
                if (auth()->user()->can('companies.approve')) {
                    if ($tipo === 'contacto' || ($d['type'] ?? '') === 'new_client') {
                        $solicitudesPendientesUrl = route('approvals.index', ['tab' => 'contactos']);
                    } elseif ($tipo === 'empresa' || ($d['type'] ?? '') === 'new_company') {
                        $solicitudesPendientesUrl = route('approvals.index', ['tab' => 'empresas']);
                    }
                }
            @endphp
            <div x-show="detailId === '{{ $notification->id }}'"
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                 x-transition
                 @keydown.escape.window="detailId = null">
                <div @click.outside="detailId = null"
                     class="relative w-full max-w-md bg-[#1a3d6b] text-white rounded-2xl shadow-xl border-4 border-[#FFE600] max-h-[80vh] overflow-hidden flex flex-col [color-scheme:dark]">
                    <div class="p-8 text-center">
                        <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center mb-4 bg-white text-[#FFE600]">
                            @if($tipo === 'registro')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                            @elseif($tipo === 'contacto')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            @elseif($tipo === 'cumpleanos')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112-2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" /></svg>
                            @elseif($tipo === 'aprobacion')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @elseif($tipo === 'recordatorio')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @elseif($tipo === 'eliminacion_solicitud')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-[#FFE600]">{{ $titulo }}</h3>
                        <p class="text-sm text-white/70 mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="px-8 pb-6 overflow-y-auto flex-1 text-center">
                        @if($tipo === 'recordatorio')
                            @include('notifications.partials.reminder-notification-detail', ['d' => $d, 'mensaje' => $mensaje])
                        @elseif($tipo === 'cumpleanos')
                            <p class="!text-white whitespace-pre-wrap break-words font-medium">{{ $mensaje ?: 'Sin contenido.' }}</p>
                            @if(!empty($d['fecha_cumpleanos']))
                                <p class="text-white/80 text-sm mt-4 text-left">Fecha de nacimiento: <span class="text-[#FFE600] font-medium">{{ $d['fecha_cumpleanos'] }}</span></p>
                            @endif
                            @if(isset($d['edad']))
                                <p class="text-white/80 text-sm mt-1 text-left">Edad que cumple hoy: <span class="text-[#FFE600] font-medium">{{ $d['edad'] }} años</span></p>
                            @endif
                        @elseif($tipo === 'eliminacion_solicitud')
                            <p class="!text-white whitespace-pre-wrap break-words text-left">{{ $mensaje ?: 'Sin contenido.' }}</p>
                            @if(!empty($d['entity_name']))
                                <p class="text-white/80 text-sm mt-4 text-left"><span class="text-[#FFE600] font-semibold">{{ ($d['entity'] ?? '') === 'company' ? 'Empresa' : 'Contacto' }}:</span> {{ $d['entity_name'] }}</p>
                            @endif
                            @if(($d['outcome'] ?? '') === 'denied' && !empty($d['nota_admin']))
                                <p class="text-white/90 text-sm mt-3 text-left font-semibold text-[#FFE600]">Motivo del administrador</p>
                                <p class="text-white/90 text-sm mt-1 text-left whitespace-pre-wrap border border-white/15 rounded-xl p-3 bg-black/20">{{ $d['nota_admin'] }}</p>
                            @endif
                        @else
                            <p class="!text-white whitespace-pre-wrap break-words">{{ $mensaje ?: 'Sin contenido.' }}</p>
                        @endif
                    </div>
                    <div class="p-6 pt-4 flex flex-wrap gap-3 justify-end">
                        @if($actionUrl)
                            {{-- Aprobación de cuenta: con sesión activa, ir al panel sin URL firmada (evita 403 por firma vieja o APP_URL distinta). Incluye registros antiguos solo con tipo=aprobacion. --}}
                            @if(($d['type'] ?? '') === 'user_approved' || ($d['tipo'] ?? '') === 'aprobacion')
                                <a href="{{ auth()->user()->esAdmin() ? route('dashboard') : route('user.dashboard') }}" class="px-4 py-2 text-sm rounded-xl font-semibold text-gray-900 bg-[#FFE600] hover:bg-[#E6CF00] transition-colors">Entrar a mi panel</a>
                            @else
                                <a href="{{ $actionUrl }}" class="px-4 py-2 text-sm rounded-xl font-semibold text-gray-900 bg-[#FFE600] hover:bg-[#E6CF00] transition-colors">{{ in_array($tipo, ['cumpleanos', 'contacto'], true) ? 'Mostrar contacto' : 'Entrar a mi panel' }}</a>
                            @endif
                        @endif
                        @if($solicitudesPendientesUrl)
                            <a href="{{ $solicitudesPendientesUrl }}" class="px-4 py-2 text-sm rounded-xl font-semibold text-gray-900 bg-[#FFE600] hover:bg-[#E6CF00] transition-colors">Solicitudes pendientes</a>
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
    (function() {
        var csrfToken = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var unreadCountUrl = '{{ route("notifications.unread-count") }}';

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
        window.__crmSetNotificationRowRead = setRowAsRead;

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
    })();
    </script>
    @endpush
</x-app-layout>
