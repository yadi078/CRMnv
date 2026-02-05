<section class="space-y-6">
    <header class="flex items-start gap-4">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                Eliminar cuenta
            </h2>
            <p class="mt-1 text-sm text-gray-500 leading-relaxed">
                Al eliminar tu cuenta, todos sus recursos y datos se borrarán de forma permanente. Descarga antes la información que quieras conservar.
            </p>
        </div>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center px-5 py-2.5 bg-red-600 text-white font-semibold rounded-xl shadow-lg shadow-red-600/20 hover:bg-red-700 hover:shadow-xl transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
    >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Eliminar cuenta
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-gray-900">
                ¿Estás seguro de que quieres eliminar tu cuenta?
            </h2>
            <p class="mt-2 text-sm text-gray-500">
                Al eliminar tu cuenta se borrarán todos sus datos de forma permanente. Introduce tu contraseña para confirmar.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Contraseña" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 py-2.5"
                    placeholder="Contraseña"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'confirm-user-deletion')" class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-300 bg-white font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-amarillo focus:ring-offset-2 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center px-5 py-2 bg-red-600 text-white font-semibold rounded-xl shadow-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Sí, eliminar cuenta
                </button>
            </div>
        </form>
    </x-modal>
</section>
