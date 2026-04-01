<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>CE CRM - {{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-fluid-base">
        <div class="sidebar-layout sidebar-layout--expanded" x-data="{ mobileMenuOpen: false }">
            <header class="mobile-header lg:hidden fixed top-0 left-0 right-0 z-30 flex items-center justify-between min-h-touch px-4 bg-[#000836] shadow-lg safe-area-inset">
                <a href="{{ auth()->user()->esAdmin() ? route('dashboard') : route('user.dashboard') }}" class="flex items-center gap-2 shrink-0" aria-label="Ir al inicio">
                    <img src="{{ asset('img/logo-empresa.png') }}" onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';" alt="CE" class="w-9 h-9 rounded-full object-cover border-2 border-[#FFE600]" />
                    <span class="font-semibold text-white text-fluid-lg">CE CRM</span>
                </a>
                <div class="flex items-center gap-1">
                    <button type="button" @click="mobileMenuOpen = true" class="flex items-center justify-center min-w-[44px] min-h-[44px] rounded-xl text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-[#000836] transition-colors" aria-label="Abrir menú">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </header>

            <div x-show="mobileMenuOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm" @click="mobileMenuOpen = false" aria-hidden="true"></div>

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

            <div class="sidebar-layout__main bg-white">
                @isset($header)
                    <div class="sidebar-layout__header px-4 sm:px-6 py-5 border-b border-[#1a3d6b]/40">
                        <div class="max-w-7xl mx-auto min-w-0 w-full">
                            <div class="page-header-card flex justify-between items-center gap-4">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    {{ $header }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset

                {{-- Mensajes flash (pueden mostrarse varios: éxito + aviso) --}}
                @if(session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif
                @if(session('warning'))
                    <x-alert type="warning" :message="session('warning')" />
                @endif
                @if(session('info'))
                    <x-alert type="info" :message="session('info')" />
                @endif
                @if(session('error'))
                    <x-alert type="error" :message="session('error')" />
                @endif
                @if(session('status'))
                    <x-alert type="success" :message="match (session('status')) {
                        'profile-updated' => 'Perfil actualizado correctamente.',
                        'profile-photo-removed' => 'Foto de perfil eliminada.',
                        'password-updated' => 'Contraseña actualizada correctamente.',
                        'verification-link-sent' => 'Se ha enviado un nuevo enlace de verificación a tu correo.',
                        default => session('status'),
                    }" />
                @endif

                <main class="flex-1 p-4 sm:p-6 md:p-8 pt-[calc(2.75rem+1rem)] lg:pt-8 min-w-0 overflow-x-hidden">
                    <div class="max-w-7xl mx-auto w-full min-w-0">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
