<section>
    <header class="flex items-start gap-5 mb-8 sm:mb-10">
        <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-amarillo/20 flex items-center justify-center ring-2 ring-amarillo/40">
            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-amarillo" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <h2 class="text-xl sm:text-2xl font-bold text-amarillo tracking-tight">
                Información del perfil
            </h2>
            <p class="mt-2 text-sm sm:text-base text-white leading-relaxed">
                Actualiza la información de tu perfil y tu correo electrónico.
            </p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6 sm:space-y-8">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <div class="space-y-2">
                <x-input-label for="name" :value="'Nombre'" class="!text-white font-medium" />
                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="block w-full rounded-xl bg-white text-gray-900 border-[#E2E8F0] shadow-sm focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 py-2.5 transition"
                    :value="old('name', $user->name)"
                    required
                    autofocus
                    autocomplete="name"
                />
                <x-input-error class="mt-1" :messages="$errors->get('name')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="email" :value="'Correo electrónico'" class="!text-white font-medium" />
                <x-text-input
                    id="email"
                    name="email"
                    type="email"
                    class="block w-full rounded-xl bg-white text-gray-900 border-[#E2E8F0] shadow-sm focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 py-2.5 transition"
                    :value="old('email', $user->email)"
                    required
                    autocomplete="username"
                />
                <x-input-error class="mt-1" :messages="$errors->get('email')" />
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="p-4 rounded-xl bg-amarillo/15 border border-amarillo/40">
                <p class="text-sm text-white">
                    Tu correo electrónico no está verificado.
                    <button form="send-verification" type="submit" class="font-semibold text-amarillo hover:underline underline-offset-2 rounded focus:outline-none focus:ring-2 focus:ring-amarillo focus:ring-offset-2 focus:ring-offset-[#1a3d6b]">
                        Reenviar correo de verificación
                    </button>
                </p>
            </div>
        @endif

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
        </div>
    </form>
</section>
