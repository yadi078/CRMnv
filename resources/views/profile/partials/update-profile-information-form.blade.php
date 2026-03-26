@php
    $hasStoredProfilePhoto = filled($user->profile_photo_path);
@endphp
<section>
    <div
        x-data="profilePhotoUploader"
        data-profile-photo-initial="{{ $user->profile_photo_url }}"
    >
        <header class="flex items-start gap-4 mb-8">
            <div class="flex-shrink-0 w-14 h-14 rounded-xl overflow-hidden bg-amber-100 flex items-center justify-center ring-2 ring-white shadow relative">
                {{-- Imagen: servidor (src) + Alpine (preview) — la URL pública debe ser la del disco public --}}
                <img
                    @if ($user->profile_photo_url) src="{{ $user->profile_photo_url }}" @endif
                    alt=""
                    class="absolute inset-0 w-full h-full object-cover z-[1]"
                    width="56"
                    height="56"
                    loading="lazy"
                    decoding="async"
                    x-bind:src="(preview || initialPreview) || @json($user->profile_photo_url)"
                    x-show="{{ $hasStoredProfilePhoto ? 'true' : 'false' }} || !!(preview || initialPreview)"
                />
                <span
                    class="text-lg font-bold text-azul-fuerte relative z-10 flex items-center justify-center w-full h-full"
                    x-show="! {{ $hasStoredProfilePhoto ? 'true' : 'false' }} && !(preview || initialPreview)"
                >{{ $user->initials }}</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white tracking-tight">
                    Información del perfil
                </h2>
                <p class="mt-1 text-sm text-white/75 leading-relaxed">
                    Actualiza la información de tu perfil y tu correo electrónico.
                </p>
            </div>
        </header>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        {{-- Formulario aparte (no anidar dentro del de actualizar perfil): solo elimina la foto en /profile/photo --}}
        @if ($hasStoredProfilePhoto)
            <form
                id="remove-profile-photo-form"
                method="post"
                action="{{ route('profile.photo.destroy') }}"
                onsubmit="return confirm('¿Eliminar la foto de perfil guardada? Se mostrarán de nuevo tus iniciales.');"
            >
                @csrf
                @method('DELETE')
            </form>
        @endif

        <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="space-y-3">
            <x-input-label for="profile_photo" :value="'Foto de perfil'" class="font-medium" />
            <div
                class="profile-photo-uploader"
                @dragover.prevent="dragOver = true"
                @dragleave.prevent="dragOver = false"
                @drop.prevent="onDrop($event)"
                :class="dragOver ? 'profile-photo-uploader--drag' : ''"
            >
                <input
                    x-ref="photoInput"
                    id="profile_photo"
                    name="profile_photo"
                    type="file"
                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                    class="sr-only"
                    @change="onPick($event)"
                />
                <div class="profile-photo-uploader__label">
                    <label for="profile_photo" class="profile-photo-uploader__preview-label cursor-pointer shrink-0">
                        <div class="profile-photo-uploader__preview" aria-hidden="true">
                            <template x-if="preview || initialPreview">
                                <img :src="preview || initialPreview" alt="" class="profile-photo-uploader__thumb" />
                            </template>
                            <template x-if="!(preview || initialPreview)">
                                <span class="profile-photo-uploader__placeholder">
                                    <svg class="w-8 h-8 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 5h3l1.5-2h7L17 5h3a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 17a4 4 0 100-8 4 4 0 000 8z" />
                                    </svg>
                                </span>
                            </template>
                        </div>
                    </label>
                    <div class="profile-photo-uploader__body">
                        <label for="profile_photo" class="block cursor-pointer text-left w-full">
                            <p class="profile-photo-uploader__title">Sube tu foto de perfil</p>
                            <p class="profile-photo-uploader__hint">Arrastra una imagen aquí o pulsa el botón</p>
                        </label>
                        <div class="profile-photo-uploader__actions">
                            <label for="profile_photo" class="profile-photo-uploader__btn cursor-pointer m-0">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Elegir imagen
                            </label>
                            @if ($hasStoredProfilePhoto)
                                <button
                                    type="submit"
                                    form="remove-profile-photo-form"
                                    class="profile-photo-uploader__btn-remove"
                                >
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar foto de perfil
                                </button>
                            @endif
                        </div>
                        <p
                            class="profile-photo-uploader__filename"
                            x-text="fileLabel || (initialPreview ? 'Foto actual (elige una nueva para cambiarla)' : 'Ningún archivo seleccionado')"
                        ></p>
                    </div>
                </div>
            </div>
            <p class="text-xs text-white/70 flex items-start gap-2 leading-relaxed">
                <span class="text-[#FFE600] shrink-0 mt-0.5" aria-hidden="true">●</span>
                <span>JPEG, PNG, GIF o WebP · máximo 2 MB · se muestra en el menú lateral</span>
            </p>
            <x-input-error class="mt-1" :messages="$errors->get('profile_photo')" />
        </div>

        <div class="space-y-2">
            <x-input-label for="name" :value="'Nombre'" class="font-medium" />
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
            <x-input-label for="email" :value="'Correo electrónico'" class="font-medium" />
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
        </div>
    </form>
    </div>
</section>
