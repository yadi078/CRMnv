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
            <div class="rounded-2xl border-2 border-[#FFE600] bg-gradient-to-br from-amber-50 via-amber-50/90 to-[#fff8e1] shadow-[0_4px_24px_rgba(7,26,61,0.12)] overflow-hidden"
                role="note"
                aria-labelledby="profile-exec-policy-title">
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-5 p-5 sm:p-6 lg:p-7">
                    <div class="flex-shrink-0 flex sm:block justify-center">
                        <span class="inline-flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-full bg-[#071A3D] text-[#FFE600] ring-4 ring-[#FFE600]/35 shadow-md" aria-hidden="true">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </span>
                    </div>
                    <div class="min-w-0 text-center sm:text-left">
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#b45309] mb-1">Aviso importante</p>
                        <h3 id="profile-exec-policy-title" class="text-lg sm:text-xl font-bold text-[#071A3D] leading-snug">
                            Contraseña y cuenta
                        </h3>
                        <p class="mt-3 text-sm sm:text-base text-gray-900 leading-relaxed font-medium">
                            Por política del sistema, los ejecutivos <span class="text-[#071A3D] font-semibold">no pueden cambiar su contraseña ni eliminar su cuenta</span> desde aquí.
                            Si necesita una nueva contraseña o dar de baja su usuario, contacte a un <span class="text-[#0b4a8a] font-bold underline decoration-[#FFE600] decoration-2 underline-offset-2">administrador</span>.
                        </p>
                    </div>
                </div>
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
