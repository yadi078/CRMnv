<section>
    <header class="flex items-start gap-5 mb-8 sm:mb-10">
        <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-amarillo/20 flex items-center justify-center ring-2 ring-amarillo/40">
            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-amarillo" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <h2 class="text-xl sm:text-2xl font-bold text-amarillo tracking-tight">
                Actualizar contraseña
            </h2>
            <p class="mt-2 text-sm sm:text-base text-white leading-relaxed">
                Usa una contraseña larga y aleatoria para mantener tu cuenta segura.
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6 sm:space-y-8">
        @csrf
        @method('put')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <div class="space-y-2 lg:col-span-2">
                <x-input-label for="update_password_current_password" :value="'Contraseña actual'" class="!text-white font-medium" />
                <x-text-input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="block w-full rounded-xl bg-white text-gray-900 border-gray-300 shadow-sm focus:border-azul-fuerte focus:ring-2 focus:ring-azul-fuerte/20 py-2.5 transition"
                    autocomplete="current-password"
                />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
            </div>

            <div class="space-y-2">
                <x-input-label for="update_password_password" :value="'Nueva contraseña'" class="!text-white font-medium" />
                <x-text-input
                    id="update_password_password"
                    name="password"
                    type="password"
                    class="block w-full rounded-xl bg-white text-gray-900 border-gray-300 shadow-sm focus:border-azul-fuerte focus:ring-2 focus:ring-azul-fuerte/20 py-2.5 transition"
                    autocomplete="new-password"
                />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
            </div>

            <div class="space-y-2">
                <x-input-label for="update_password_password_confirmation" :value="'Confirmar contraseña'" class="!text-white font-medium" />
                <x-text-input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="block w-full rounded-xl bg-white text-gray-900 border-gray-300 shadow-sm focus:border-azul-fuerte focus:ring-2 focus:ring-azul-fuerte/20 py-2.5 transition"
                    autocomplete="new-password"
                />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4 pt-2">
            <button
                type="submit"
                class="inline-flex items-center px-6 py-2.5 bg-azul-fuerte text-white font-semibold rounded-xl shadow-lg shadow-azul-fuerte/25 hover:bg-azul hover:shadow-xl hover:shadow-azul-fuerte/20 focus:outline-none focus:ring-2 focus:ring-amarillo focus:ring-offset-2 transition duration-200"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Actualizar contraseña
            </button>
        </div>
    </form>
</section>
