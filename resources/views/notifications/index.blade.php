<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">Notificaciones</h2>
                    <p id="notifications-header-subtitle" class="view-header__subtitle">
                        @if($unreadCount > 0)
                            <span class="font-semibold text-amber-600" data-unread-badge>{{ $unreadCount }} sin leer</span>
                        @else
                            Centro de notificaciones
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Botón refrescar: blanco, forma redondeada, sombra suave, ícono azul --}}
                <a href="{{ request()->fullUrl() }}" class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-white text-[#003366] shadow-md hover:shadow-lg hover:bg-gray-50 transition-all" title="Refrescar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </a>
                {{-- Botón menú: azul pastel, misma forma y sombra, ícono azul oscuro --}}
                <div class="relative overflow-visible" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-blue-100 text-[#003366] shadow-md hover:shadow-lg hover:bg-blue-200/80 transition-all" aria-label="Más opciones" aria-haspopup="true" :aria-expanded="open">
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
        </div>
    </x-slot>

    <div class="space-y-0" x-data="{ detailId: null }">
        {{-- Tabs (filtros) --}}
        <div class="flex flex-wrap gap-1 border-b border-[#E2E8F0] mb-4">
            @php
                $tabs = [
                    'todas' => ['label' => 'Todas', 'count' => $notifications->total()],
                    'no_leidas' => ['label' => 'No leídas', 'count' => $unreadCount],
                    'leidas' => ['label' => 'Leídas', 'count' => null],
                    'destacadas' => ['label' => 'Destacadas', 'count' => $starredCount],
                    'sin_destacar' => ['label' => 'Sin destacar', 'count' => null],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                @php
                    $params = ['orden' => $sort];
                    if ($key !== 'todas') {
                        $params['filtro'] = $key;
                    }
                @endphp
                <a href="{{ route('notifications.index', $params) }}"
                   class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $filter === $key ? 'border-amber-400 text-[#003366]' : 'border-transparent text-[#6B7280] hover:text-[#1F2937] hover:border-gray-300' }}">
                    {{ $tab['label'] }}
                    @if($tab['count'] !== null && $tab['count'] > 0)
                        <span class="ml-1 text-xs font-semibold text-amber-600">({{ $tab['count'] }})</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Paginación superior --}}
        @if($notifications->total() > 0)
        <div class="flex items-center justify-between text-sm text-[#6B7280] mb-3">
            <span>
                {{ $notifications->firstItem() }}-{{ $notifications->lastItem() }} de {{ $notifications->total() }}
            </span>
            <div class="flex gap-1">
                @if($notifications->onFirstPage())
                    <span class="px-2 py-1 rounded text-gray-400 cursor-not-allowed">←</span>
                @else
                    <a href="{{ $notifications->previousPageUrl() }}" class="px-2 py-1 rounded hover:bg-gray-100">←</a>
                @endif
                @if($notifications->hasMorePages())
                    <a href="{{ $notifications->nextPageUrl() }}" class="px-2 py-1 rounded hover:bg-gray-100">→</a>
                @else
                    <span class="px-2 py-1 rounded text-gray-400 cursor-not-allowed">→</span>
                @endif
            </div>
        </div>
        @endif

        {{-- Lista de notificaciones --}}
        <div class="view-card divide-y divide-[#E2E8F0] p-0 overflow-hidden">
            @forelse($notifications as $notification)
                @php
                    $d = is_array($notification->data) ? $notification->data : [];
                    $titulo = $d['titulo'] ?? $d['message'] ?? $d['contact_name'] ?? 'Notificación';
                    $mensaje = $d['mensaje'] ?? $d['message'] ?? '';
                    $tipo = $d['tipo'] ?? 'general';
                    $isUnread = !$notification->read_at;
                    $isStarred = !empty($notification->starred);
                @endphp
                <div class="notification-row flex items-start gap-3 px-4 py-3 hover:bg-gray-50/80 transition-colors {{ $isUnread ? 'bg-blue-50/70' : 'bg-white' }}"
                     role="button"
                     data-notification-id="{{ $notification->id }}"
                     data-unread="{{ $isUnread ? '1' : '0' }}"
                     @click="detailId = '{{ $notification->id }}'">
                    {{-- Icono tipo --}}
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                        @if($tipo === 'registro') bg-indigo-100 text-indigo-600
                        @elseif($tipo === 'contacto') bg-emerald-100 text-emerald-600
                        @else bg-gray-100 text-gray-600 @endif">
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
                                <button type="submit" class="text-amber-500 hover:text-amber-600" title="Quitar de destacadas">★</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('notifications.star', $notification) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-300 hover:text-amber-500" title="Destacar">☆</button>
                            </form>
                        @endif
                    </div>
                    {{-- Contenido --}}
                    <div class="flex-1 min-w-0">
                        <p class="notification-row-title text-sm {{ $isUnread ? 'font-semibold text-[#1F2937]' : 'text-[#1F2937]' }}">{{ $titulo }}</p>
                        <p class="text-sm text-[#6B7280] truncate mt-0.5">{{ $mensaje ?: 'Sin descripción' }}</p>
                        <p class="text-xs text-[#9CA3AF] mt-1">{{ $notification->created_at->format('d M Y H:i') }}</p>
                    </div>
                    {{-- Acciones rápidas (evitar que abran el detalle) --}}
                    <div class="flex-shrink-0 flex items-center gap-1" @click.stop>
                        @if($isUnread)
                            <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="inline js-mark-read-form" data-notification-id="{{ $notification->id }}">
                                @csrf
                                <button type="submit" class="text-xs text-[#003366] hover:underline">Leer</button>
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
                <div class="mx-4 my-6 rounded-2xl border-2 border-[#003366]/25 border-l-4 border-l-amber-400 bg-gradient-to-br from-blue-50 via-amber-50/40 to-blue-50 px-6 py-12 shadow-inner flex items-center justify-center min-h-[120px]">
                    <p class="text-[#1F2937] font-medium text-center">No hay notificaciones todavía.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
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
            row.classList.remove('bg-blue-50/70');
            row.classList.add('bg-white');
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
