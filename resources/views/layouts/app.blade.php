<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>C&CE CRM - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-fluid-base">
        <div class="sidebar-layout sidebar-layout--expanded" x-data="{ mobileMenuOpen: false }">
            {{-- Barra superior móvil/tablet: logo + notificaciones + menú hamburguesa (solo < lg) --}}
            <header class="mobile-header lg:hidden fixed top-0 left-0 right-0 z-30 flex items-center justify-between min-h-touch px-4 bg-[#000836] shadow-lg safe-area-inset">
                <a href="{{ auth()->user()->esAdmin() ? route('dashboard') : route('user.dashboard') }}" class="flex items-center gap-2 shrink-0" aria-label="Ir al inicio">
                    <img src="{{ asset('img/logo-empresa.png') }}" onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';" alt="C&amp;CE" class="w-9 h-9 rounded-full object-cover border-2 border-[#FFE600]" />
                    <span class="font-semibold text-white text-fluid-lg">C&CE CRM</span>
                </a>
                <div class="flex items-center gap-1">
                    @if(auth()->user()->esAdmin())
                    <a href="{{ route('notifications.index') }}" class="relative flex items-center justify-center min-w-[44px] min-h-[44px] rounded-xl text-[#FFE600] hover:bg-white/10 transition-colors" aria-label="Notificaciones">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="js-header-notification-badge-wrap absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-xs font-bold bg-red-500 text-white" style="{{ (auth()->user()->unreadNotifications->count() > 0) ? '' : 'display: none;' }}">
                            <span class="js-header-notification-badge">{{ auth()->user()->unreadNotifications->count() > 0 ? min(auth()->user()->unreadNotifications->count(), 99) . (auth()->user()->unreadNotifications->count() > 99 ? '+' : '') : '' }}</span>
                        </span>
                    </a>
                    @endif
                    <button type="button" @click="mobileMenuOpen = true" class="flex items-center justify-center min-w-[44px] min-h-[44px] rounded-xl text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-[#000836] transition-colors" aria-label="Abrir menú">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </header>

            {{-- Overlay cuando el drawer está abierto (solo móvil/tablet) --}}
            <div x-show="mobileMenuOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm" @click="mobileMenuOpen = false" aria-hidden="true"></div>

            {{-- Sidebar: drawer en móvil/tablet, fijo en desktop. Cerrar al hacer clic en un enlace --}}
            <div class="sidebar-drawer" :class="mobileMenuOpen ? 'sidebar-drawer--open' : ''" @click="if ($event.target.closest('a')) mobileMenuOpen = false">
                @if(auth()->user()->esAdmin())
                    <x-sidebar-nav />
                @else
                    <x-sidebar-nav-user />
                @endif
                <button type="button" @click="mobileMenuOpen = false" class="lg:hidden absolute top-4 right-4 flex items-center justify-center w-11 h-11 rounded-xl text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#FFE600]" aria-label="Cerrar menú">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Área principal: fondo blanco/gris muy claro como en diseño C&CE CRM --}}
            <div class="sidebar-layout__main bg-white">
                {{-- Encabezado: recuadro único (page-header-card) con título + icono notificaciones DENTRO --}}
                @isset($header)
                    <div class="sidebar-layout__header px-4 sm:px-6 py-5 border-b border-[#1a3d6b]/40">
                        <div class="max-w-7xl mx-auto min-w-0 w-full">
                            <div class="page-header-card flex justify-between items-center gap-4">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    {{ $header }}
                                </div>
                                @if(auth()->user()?->esAdmin())
                                <a href="{{ route('notifications.index') }}" class="flex-shrink-0 relative flex items-center justify-center w-11 h-11 rounded-xl text-[#FFE600] hover:bg-white/10 transition-colors" aria-label="Notificaciones">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    <span class="js-header-notification-badge-wrap absolute top-0 right-0 flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-xs font-bold bg-red-500 text-white" style="{{ (auth()->user()->unreadNotifications->count() > 0) ? '' : 'display: none;' }}">
                                        <span class="js-header-notification-badge">{{ auth()->user()->unreadNotifications->count() > 0 ? min(auth()->user()->unreadNotifications->count(), 99) . (auth()->user()->unreadNotifications->count() > 99 ? '+' : '') : '' }}</span>
                                    </span>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endisset

                {{-- Mensajes flash flotantes (éxito, error, warning, info, status) --}}
                @if(session('success'))
                    <x-alert type="success" :message="session('success')" />
                @elseif(session('error'))
                    <x-alert type="error" :message="session('error')" />
                @elseif(session('warning'))
                    <x-alert type="warning" :message="session('warning')" />
                @elseif(session('info'))
                    <x-alert type="info" :message="session('info')" />
                @elseif(session('status'))
                    @php
                        $statusMsg = match(session('status')) {
                            'profile-updated' => 'Perfil actualizado correctamente.',
                            'password-updated' => 'Contraseña actualizada correctamente.',
                            'verification-link-sent' => 'Se ha enviado un nuevo enlace de verificación a tu correo.',
                            default => session('status'),
                        };
                    @endphp
                    <x-alert type="success" :message="$statusMsg" />
                @endif

                {{-- Contenido: padding-top en móvil para no quedar bajo la barra fija --}}
                <main class="flex-1 p-4 sm:p-6 md:p-8 pt-[calc(2.75rem+1rem)] lg:pt-8 min-w-0 overflow-x-hidden">
                    <div class="max-w-7xl mx-auto w-full min-w-0">
                        {{ $slot }}
                    </div>
                </main>

                {{-- Barra inferior fija: slogan centrado, no se mueve con el scroll --}}
                <div class="slogan-bar-fixed">
                    <div class="top-bar-gradient slogan-bar-fixed__inner">
                        <h1 class="top-bar-gradient__slogan">INVERTIR EN VALOR ¡ATRAE VALOR!</h1>
                    </div>
                </div>
            </div>
        </div>
        @stack('scripts')
        @if(auth()->user()?->esAdmin())
        <script>
        (function() {
            var url = '{{ route("notifications.unread-count") }}';
            function updateBadge() {
                fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        var count = data.unread_count || 0;
                        var display = data.display || (count > 99 ? '99+' : String(count));
                        document.querySelectorAll('.js-header-notification-badge').forEach(function(el) { el.textContent = display; });
                        document.querySelectorAll('.js-header-notification-badge-wrap').forEach(function(el) { el.style.display = count > 0 ? '' : 'none'; });
                        var sidebarWrap = document.getElementById('sidebar-notification-badge-wrap');
                        var sidebarBadge = document.getElementById('sidebar-notification-badge');
                        if (sidebarWrap && sidebarBadge) {
                            sidebarBadge.textContent = display;
                            sidebarWrap.style.display = count > 0 ? '' : 'none';
                        }
                    })
                    .catch(function() {});
            }
            updateBadge();
            setInterval(updateBadge, 25000);
            window.updateNotificationBadge = updateBadge;
        })();
        </script>
        @endif
    </body>
</html>
