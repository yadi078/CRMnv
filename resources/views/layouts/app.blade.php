<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>C&CE CRM - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="sidebar-layout">
            {{-- Admin: panel completo. Usuario: Inicio, Empresas, Contactos, Seguimientos, Configuración --}}
            @if(auth()->user()->esAdmin())
                <x-sidebar-nav />
            @else
                <x-sidebar-nav-user />
            @endif

            {{-- Área principal: fondo oscuro tipo dashboard --}}
            <div class="sidebar-layout__main">
                {{-- Barra superior: logo + usuario --}}
                <header class="flex items-center justify-between h-14 px-4 sm:px-6 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <a href="{{ auth()->user()->esAdmin() ? route('dashboard') : route('user.dashboard') }}" class="flex items-center gap-2 no-underline group">
                            <img
                                src="{{ asset('img/logo-empresa.png') }}"
                                onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';"
                                alt=""
                                class="h-8 w-8 rounded-full object-contain flex-shrink-0 ring-1 ring-white/20 group-hover:ring-white/40 transition"
                                width="32"
                                height="32"
                            />
                            <span class="text-lg font-semibold text-white group-hover:text-white/90">C&CE CRM</span>
                        </a>
                    </div>
                    <div class="flex items-center">
                        <x-dropdown align="right" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-white/5 border border-white/10 hover:bg-white/10 hover:border-amber-400/40 focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:ring-offset-2 focus:ring-offset-[#1e293b] transition-all duration-200 shadow-sm">
                                    <span class="w-7 h-7 rounded-lg bg-amber-400/20 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </span>
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="w-4 h-4 text-white/70 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')" class="dropdown-item-pro">
                                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Perfil</span>
                                </x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="dropdown-item-pro dropdown-item-pro--danger">
                                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>Cerrar Sesión</span>
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                {{-- Encabezado de página opcional (texto claro sobre fondo oscuro) --}}
                @isset($header)
                    <div class="sidebar-layout__header px-4 sm:px-6 py-4">
                        <div class="max-w-7xl mx-auto text-gray-100">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                {{-- Mensajes flash (éxito / error) para feedback tras crear, editar, eliminar --}}
                @if(session('success') || session('error'))
                    <div class="mx-4 sm:mx-6 mt-4">
                        <div class="max-w-7xl mx-auto">
                            @if(session('success'))
                                <div class="rounded-lg px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="rounded-lg px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 mt-2" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Contenido: las vistas internas usan su propio fondo (p. ej. bg-white) --}}
                <main class="p-4 sm:p-6">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
