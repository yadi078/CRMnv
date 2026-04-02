<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased leading-normal bg-[#808080]">
        {{-- Alertas flotantes (login, forgot-password, verify-email) --}}
        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @elseif(session('error'))
            <x-alert type="error" :message="session('error')" />
        @elseif(session('status'))
            <x-alert type="success" :message="match (session('status')) {
                'verification-link-sent' => 'Se ha enviado un nuevo enlace de verificación a tu correo.',
                default => is_string(session('status')) ? session('status') : 'Listo.',
            }" />
        @endif
        <div class="min-h-screen flex items-center justify-center px-4 py-8">
            <div class="w-full max-w-5xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
