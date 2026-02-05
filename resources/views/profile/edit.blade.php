@auth
@if(!auth()->user()->esAdmin())
<x-app-user-layout>
@else
<x-app-layout>
@endif
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-xl bg-white/10">
                <svg class="w-6 h-6 text-amarillo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl text-white leading-tight tracking-tight">
                    Perfil
                </h2>
                <p class="text-sm text-white/70 mt-0.5">Configuración de tu cuenta</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Tarjeta: Información del perfil --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 border border-gray-100 border-l-4 border-l-amarillo overflow-hidden transition hover:shadow-2xl hover:shadow-gray-200/50">
                <div class="p-6 sm:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            {{-- Tarjeta: Actualizar contraseña --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 border border-gray-100 border-l-4 border-l-amarillo overflow-hidden transition hover:shadow-2xl hover:shadow-gray-200/50">
                <div class="p-6 sm:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            {{-- Tarjeta: Eliminar cuenta (zona de peligro) --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 border border-amber-100 border-l-4 border-l-amarillo overflow-hidden transition hover:shadow-2xl hover:shadow-red-100/30">
                <div class="p-6 sm:p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@if(!auth()->user()->esAdmin())
</x-app-user-layout>
@else
</x-app-layout>
@endif
@endauth
