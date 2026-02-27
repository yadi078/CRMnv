@auth
@if(!auth()->user()->esAdmin())
<x-app-user-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Perfil</h2>
            <p class="page-header-card__subtitle">Configuración de tu cuenta</p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Tarjeta: Información del perfil --}}
            <div class="panel-card-dark overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            {{-- Tarjeta: Actualizar contraseña --}}
            <div class="panel-card-dark overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            {{-- Tarjeta: Eliminar cuenta (zona de peligro) --}}
            <div class="panel-card-dark overflow-hidden border-l-4 border-l-red-400">
                <div class="p-6 sm:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-user-layout>
@else
<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Perfil</h2>
            <p class="page-header-card__subtitle">Configuración de tu cuenta</p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Tarjeta: Información del perfil --}}
            <div class="panel-card-dark overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            {{-- Tarjeta: Actualizar contraseña --}}
            <div class="panel-card-dark overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            {{-- Tarjeta: Eliminar cuenta (zona de peligro) --}}
            <div class="panel-card-dark overflow-hidden border-l-4 border-l-red-400">
                <div class="p-6 sm:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@endif
@endauth
