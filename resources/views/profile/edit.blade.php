@auth
@if(!auth()->user()->esAdmin())
<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Perfil</h2>
            <p class="page-header-card__subtitle">Configuración de tu cuenta</p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 space-y-10">
            {{-- Tarjeta: Información del perfil --}}
            <div class="panel-card-dark !p-0 overflow-hidden rounded-2xl">
                <div class="p-8 sm:p-10 lg:p-12">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Ejecutivos (rol usuario): la contraseña y la baja de cuenta las gestiona administración --}}
            <div class="rounded-2xl border border-white/20 bg-white/5 p-6 text-sm text-white/85">
                <p class="font-semibold text-[#FFE600] mb-2">Contraseña y cuenta</p>
                <p class="leading-relaxed">
                    Por política del sistema, los ejecutivos no pueden cambiar su contraseña ni eliminar su cuenta desde aquí.
                    Si necesita una nueva contraseña o dar de baja su usuario, contacte a un <span class="text-white font-medium">administrador</span>.
                </p>
            </div>
        </div>
    </div>
</x-app-user-layout>
@else
<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Perfil</h2>
            <p class="page-header-card__subtitle">Configuración de tu cuenta</p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 space-y-10">
            {{-- Tarjeta: Información del perfil --}}
            <div class="panel-card-dark !p-0 overflow-hidden rounded-2xl">
                <div class="p-8 sm:p-10 lg:p-12">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Tarjeta: Actualizar contraseña --}}
            <div class="panel-card-dark !p-0 overflow-hidden rounded-2xl">
                <div class="p-8 sm:p-10 lg:p-12">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="panel-card-dark !p-0 overflow-hidden rounded-2xl">
                <div class="p-8 sm:p-10 lg:p-12">
                    @include('profile.partials.manage-work-areas')
                </div>
            </div>

            {{-- Tarjeta: Eliminar cuenta (zona de peligro) --}}
            <div class="panel-card-dark !p-0 overflow-hidden rounded-2xl border-l-4 border-l-red-400">
                <div class="p-8 sm:p-10 lg:p-12">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@endif
@endauth
