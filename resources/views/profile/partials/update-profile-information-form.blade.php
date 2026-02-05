<section>
    <header class="flex items-start gap-4 mb-8">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-azul-fuerte" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                Información del perfil
            </h2>
            <p class="mt-1 text-sm text-gray-500 leading-relaxed">
                Actualiza la información de tu perfil y tu correo electrónico.
            </p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="space-y-2">
            <x-input-label for="name" :value="'Nombre'" class="text-gray-700 font-medium" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amarillo focus:ring-2 focus:ring-amber-400/30 py-2.5 transition"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div class="space-y-2">
            <x-input-label for="email" :value="'Correo electrónico'" class="text-gray-700 font-medium" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amarillo focus:ring-2 focus:ring-amber-400/30 py-2.5 transition"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-4 rounded-xl bg-amber-50 border border-amber-200/80">
                    <p class="text-sm text-amber-900">
                        Tu correo electrónico no está verificado.
                        <button form="send-verification" type="submit" class="font-medium text-azul-fuerte hover:text-azul-bright underline underline-offset-2 rounded focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                            Reenviar correo de verificación
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-700">
                            Se ha enviado un nuevo enlace de verificación a tu correo.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-4 pt-2">
            <button
                type="submit"
                class="inline-flex items-center px-6 py-2.5 bg-azul-fuerte text-white font-semibold rounded-xl shadow-lg shadow-azul-fuerte/25 hover:bg-azul hover:shadow-xl hover:shadow-azul-fuerte/20 focus:outline-none focus:ring-2 focus:ring-amarillo focus:ring-offset-2 transition duration-200"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Guardar cambios
            </button>
            @if (session('status') === 'profile-updated')
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
                    Guardado correctamente
                </p>
            @endif
        </div>
    </form>
</section>
