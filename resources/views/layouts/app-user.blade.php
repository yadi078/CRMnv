<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>C&CE CRM - {{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="sidebar-layout sidebar-layout--expanded">
            <x-sidebar-nav-user />

            <div class="sidebar-layout__main bg-white">
                {{-- Barra superior: mismo diseño que los contenedores, centrada y con esquinas redondeadas --}}
                <div class="px-6 sm:px-8 pt-6 sm:pt-8 relative z-[100]">
                    <header class="top-bar-gradient max-w-7xl mx-auto flex items-center justify-center min-h-[70px] py-4 px-6 rounded-[var(--radius-card)]">
                        <h1 class="top-bar-gradient__slogan">INVERTIR EN VALOR ¡ATRAE VALOR!</h1>
                    </header>
                </div>

                @isset($header)
                    <div class="sidebar-layout__header px-4 sm:px-6 py-5 border-b border-[#1a3d6b]/40">
                        <div class="max-w-7xl mx-auto">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                @if(session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif
                @if(session('error'))
                    <x-alert type="error" :message="session('error')" />
                @endif
                @if(session('warning'))
                    <x-alert type="warning" :message="session('warning')" />
                @endif
                @if(session('info'))
                    <x-alert type="info" :message="session('info')" />
                @endif

                <main class="flex-1 p-6 sm:p-8">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
