<section>
    <header class="flex items-start gap-4 mb-8">
        <div class="flex-shrink-0 w-14 h-14 rounded-xl overflow-hidden bg-amber-100 flex items-center justify-center ring-2 ring-white shadow">
            @if ($user->profile_photo_url)
                <img src="{{ $user->profile_photo_url }}" alt="Foto de perfil" class="w-full h-full object-cover" />
            @else
                <span class="text-lg font-bold text-azul-fuerte">{{ $user->initials }}</span>
            @endif
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

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="space-y-2">
            <x-input-label for="profile_photo" :value="'Foto de perfil'" class="text-gray-700 font-medium" />
            <input
                id="profile_photo"
                name="profile_photo"
                type="file"
                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-azul-fuerte/10 file:text-azul-fuerte file:font-medium hover:file:bg-azul-fuerte/20 file:cursor-pointer rounded-xl border border-gray-200"
            />
            <p class="text-xs text-gray-500">JPEG, PNG, GIF o WebP. Máximo 2 MB. Se mostrará en el menú lateral.</p>
            <x-input-error class="mt-1" :messages="$errors->get('profile_photo')" />
        </div>

        <div class="space-y-2">
            <x-input-label for="name" :value="'Nombre'" class="text-gray-700 font-medium" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="block w-full rounded-xl border-[#E2E8F0] shadow-sm focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 py-2.5 transition"
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
                class="block w-full rounded-xl border-[#E2E8F0] shadow-sm focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 py-2.5 transition"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-4 rounded-xl bg-amber-50 border border-amber-200/80">
                    <p class="text-sm text-amber-900">
                        Tu correo electrónico no está verificado.
                        <button form="send-verification" type="submit" class="font-medium text-azul-fuerte hover:text-azul-bright underline underline-offset-2 rounded focus:outline-none focus:ring-2 focus:ring-[#003366]/20">
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
