<section>
    <header class="flex items-start gap-4 mb-8">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-azul-fuerte/10 flex items-center justify-center">
            <svg class="w-6 h-6 text-azul-fuerte" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                Actualizar contraseña
            </h2>
            <p class="mt-1 text-sm text-gray-500 leading-relaxed">
                Usa una contraseña larga y aleatoria para mantener tu cuenta segura.
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div class="space-y-2">
            <x-input-label for="update_password_current_password" :value="'Contraseña actual'" class="text-gray-700 font-medium" />
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-azul-fuerte focus:ring-2 focus:ring-azul-fuerte/20 py-2.5 transition"
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password" :value="'Nueva contraseña'" class="text-gray-700 font-medium" />
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-azul-fuerte focus:ring-2 focus:ring-azul-fuerte/20 py-2.5 transition"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password_confirmation" :value="'Confirmar contraseña'" class="text-gray-700 font-medium" />
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-azul-fuerte focus:ring-2 focus:ring-azul-fuerte/20 py-2.5 transition"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
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
            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="inline-flex items-center text-sm font-medium text-green-700 bg-green-50 px-3 py-1.5 rounded-lg border border-green-200"
                >
                    <svg class="w-4 h-4 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Contraseña actualizada
                </p>
            @endif
        </div>
    </form>
</section>
