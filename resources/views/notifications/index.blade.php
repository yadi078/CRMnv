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

    <div class="space-y-8" x-data="{ detailId: null }">
        {{-- Tarjeta de filtros + iconos refrescar y menú alineados a la derecha --}}
        <div class="panel-card-dark">
            <form method="GET" action="{{ route('notifications.index') }}" id="notifications-filter-form" class="flex flex-wrap items-center justify-between gap-4">
                <input type="hidden" name="orden" value="{{ $sort }}">
                <input type="hidden" name="filtro" id="notif-filtro-input" value="{{ $filter }}">
                <div class="flex flex-wrap items-center gap-4">
                    <span class="text-sm font-semibold text-[#FFE600]">Mostrar:</span>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="filtro_no_leidas" value="1" class="rounded border-white/40 text-[#FFE600] bg-transparent focus:ring-[#FFE600]"
                            {{ $filter === 'no_leidas' ? 'checked' : '' }} data-filtro-value="no_leidas">
                        <span class="text-sm text-white">No leídas</span>
                        @if($unreadCount > 0)
                            <span class="text-xs font-semibold text-[#FFE600]">({{ $unreadCount }})</span>
                        @endif
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="filtro_leidas" value="1" class="rounded border-white/40 text-[#FFE600] bg-transparent focus:ring-[#FFE600]"
                            {{ $filter === 'leidas' ? 'checked' : '' }} data-filtro-value="leidas">
                        <span class="text-sm text-white">Leídas</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="filtro_todas" value="1" class="rounded border-white/40 text-[#FFE600] bg-transparent focus:ring-[#FFE600]"
                            {{ $filter === 'todas' ? 'checked' : '' }} data-filtro-value="todas">
                        <span class="text-sm text-white">Todas</span>
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

        {{-- Tarjeta lista de notificaciones (mismo estilo que dashboard, fondo azul) --}}
        <div class="panel-card-dark p-0 overflow-hidden divide-y divide-white/10">
            @if($notifications->total() > 0)
            <div class="flex items-center justify-between text-sm text-white/80 px-4 sm:px-5 py-3 border-b border-white/10 bg-white/5 rounded-t-[var(--radius-card)]">
                <span>
                    {{ $notifications->firstItem() }}-{{ $notifications->lastItem() }} de {{ $notifications->total() }}
                </span>
                <div class="flex gap-1">
                    @if($notifications->onFirstPage())
                        <span class="px-2 py-1 rounded text-white/40 cursor-not-allowed">←</span>
                    @else
                        <a href="{{ $notifications->previousPageUrl() }}" class="px-2 py-1 rounded hover:bg-white/10 text-[#FFE600]">←</a>
                    @endif
                    @if($notifications->hasMorePages())
                        <a href="{{ $notifications->nextPageUrl() }}" class="px-2 py-1 rounded hover:bg-white/10 text-[#FFE600]">→</a>
                    @else
                        <span class="px-2 py-1 rounded text-white/40 cursor-not-allowed">→</span>
                    @endif
                </div>
            </div>
            @endif
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
                    {{-- Icono tipo --}}
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                        @if($tipo === 'registro') bg-indigo-100 text-indigo-700
                        @elseif($tipo === 'contacto') bg-emerald-100 text-emerald-700
                        @else bg-white/10 text-white @endif">
                        @if($tipo === 'registro')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        @elseif($tipo === 'contacto')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        @endif
                    </div>
                    {{-- Estrella (destacada) --}}
                    <div class="flex-shrink-0 pt-1.5" @click.stop>
                        @if($isStarred)
                            <form method="POST" action="{{ route('notifications.unstar', $notification) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-[#FFE600] hover:opacity-80" title="Quitar de destacadas">★</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('notifications.star', $notification) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-[#9CA3AF] hover:text-[#003366]" title="Destacar">☆</button>
                            </form>
                        @endif
                    </div>
                    {{-- Contenido --}}
                    <div class="flex-1 min-w-0">
                        <p class="notification-row-title text-sm {{ $isUnread ? 'font-semibold text-white' : 'text-white' }}">{{ $titulo }}</p>
                        <p class="text-sm text-white/80 truncate mt-0.5">{{ $mensaje ?: 'Sin descripción' }}</p>
                        <p class="text-xs text-white/60 mt-1">{{ $notification->created_at->format('d M Y H:i') }}</p>
                    </div>
                    {{-- Acciones rápidas (evitar que abran el detalle) --}}
                    <div class="flex-shrink-0 flex items-center gap-1" @click.stop>
                        @if($isUnread)
                            <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="inline js-mark-read-form" data-notification-id="{{ $notification->id }}">
                                @csrf
                                <button type="submit" class="text-xs font-medium text-white hover:underline">Leer</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="inline" onsubmit="return confirm('¿Eliminar esta notificación?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="mx-4 my-8 rounded-2xl border-2 border-[#FFE600]/70 border-l-4 border-l-[#FFE600] bg-white/5 px-6 py-12 flex items-center justify-center min-h-[140px]">
                    <p class="text-white font-medium text-center">No hay notificaciones todavía.</p>
                </div>
            @endforelse
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
            @endphp
            <div x-show="detailId === '{{ $notification->id }}'"
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                 x-transition
                 @keydown.escape.window="detailId = null">
                <div @click.outside="detailId = null"
                     class="bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[80vh] overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-[#E2E8F0]">
                        <h3 class="text-lg font-semibold text-[#1F2937]">{{ $titulo }}</h3>
                        <p class="text-sm text-[#6B7280] mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="p-6 overflow-y-auto flex-1">
                        <p class="text-[#374151]">{{ $mensaje ?: 'Sin contenido.' }}</p>
                    </div>
                    <div class="p-4 border-t border-[#E2E8F0] flex flex-wrap gap-2">
                        @if(!$notification->read_at)
                            <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="inline">
                                @csrf
                                <button type="submit" class="btn-primary-app text-sm">Marcar como leída</button>
                            </form>
                        @endif
                        @if(!empty($notification->starred))
                            <form method="POST" action="{{ route('notifications.unstar', $notification) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm rounded-xl border border-[#E2E8F0] bg-white hover:bg-gray-50">Quitar destacada</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('notifications.star', $notification) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm rounded-xl border border-amber-200 bg-amber-50 text-amber-800 hover:bg-amber-100">★ Destacar</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="inline" onsubmit="return confirm('¿Eliminar?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-sm rounded-xl border border-red-200 text-red-600 hover:bg-red-50">Eliminar</button>
                        </form>
                        <button type="button" @click="detailId = null" class="px-4 py-2 text-sm rounded-xl border border-[#E2E8F0] bg-white hover:bg-gray-50">Cerrar</button>
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
    })();
    </script>
    @endpush
</x-app-layout>
