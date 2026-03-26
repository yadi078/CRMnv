@if (!auth()->user()?->esAdmin())
    @php return; @endphp
@endif

@php
    /** @var \App\Models\User|null $managedUser */
    $tempPassword = session('admin_generated_password');
    $passwordForUserId = session('admin_password_user_id');
    $managedUsersSuggestions = $managedUsersSuggestions ?? collect();
@endphp

<section class="space-y-5">
    <header class="flex items-start gap-4">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-[#FFE600]/20 flex items-center justify-center text-[#FFE600]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.567 3-3.5S13.657 4 12 4 9 5.567 9 7.5 10.343 11 12 11zm0 0c-3.314 0-6 1.79-6 4v1h12v-1c0-2.21-2.686-4-6-4zm6.5-2.5h3m-1.5-1.5v3" />
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Asistencia de contraseñas de usuarios</h2>
            <p class="mt-1 text-sm text-white/75 leading-relaxed">
                Busca un usuario por nombre o correo y restablece su contraseña cuando la olvide.
            </p>
        </div>
    </header>

    <div class="rounded-2xl border border-white/20 bg-white/5 p-4 sm:p-5 space-y-4">
        <form method="GET" action="{{ route('profile.edit') }}" class="flex flex-col sm:flex-row gap-3 sm:items-end">
            <div class="flex-1">
                <x-input-label for="user_search" :value="'Nombre o correo del usuario'" class="font-medium text-white/90" />
                <x-text-input
                    id="user_search"
                    name="user_search"
                    type="text"
                    list="admin-users-suggestions"
                    class="mt-1 block w-full rounded-xl border-[#E2E8F0] shadow-sm focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 py-2.5 transition"
                    :value="$userSearch ?? ''"
                    placeholder="Ej. Juan Pérez o correo@dominio.com"
                />
                <datalist id="admin-users-suggestions">
                    @foreach ($managedUsersSuggestions as $suggestedUser)
                        <option value="{{ $suggestedUser->name }}">{{ $suggestedUser->email }}</option>
                        <option value="{{ $suggestedUser->email }}">{{ $suggestedUser->name }}</option>
                    @endforeach
                </datalist>
            </div>
            <button
                type="submit"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-[#FFE600] text-[#003366] font-semibold rounded-xl shadow-lg shadow-[#FFE600]/25 hover:bg-[#e6cf00] focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-transparent transition duration-200"
            >
                Visualiza perfil de usuario
            </button>
        </form>

        @if (($userSearch ?? '') !== '' && ! $managedUser)
            <p class="text-sm text-amber-200">
                No se encontró ningún usuario con ese criterio.
            </p>
        @endif

        @if ($managedUser)
            <div class="rounded-2xl border border-[#FFE600]/40 bg-[#0f2744]/65 p-4 sm:p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-[#FFE600] font-semibold text-xs uppercase tracking-wide">Nombre de usuario</p>
                        <p class="text-white font-medium mt-0.5">{{ $managedUser->name }}</p>
                    </div>
                    <div>
                        <p class="text-[#FFE600] font-semibold text-xs uppercase tracking-wide">Correo electrónico</p>
                        <p class="text-white font-medium mt-0.5 break-all">{{ $managedUser->email }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.admin-reset-user-password') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="managed_user_id" value="{{ $managedUser->id }}">
                    <div class="space-y-2">
                        <x-input-label for="new_password" :value="'Nueva contraseña (opcional)'" class="font-medium text-white/90" />
                        <x-text-input
                            id="new_password"
                            name="new_password"
                            type="text"
                            class="block w-full rounded-xl border-[#E2E8F0] shadow-sm focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 py-2.5 transition"
                            placeholder="Déjalo vacío para generar una temporal segura"
                        />
                        <x-input-error class="mt-1" :messages="$errors->get('new_password')" />
                    </div>
                    <button
                        type="submit"
                        class="inline-flex items-center px-5 py-2.5 bg-[#FFE600] text-[#003366] font-semibold rounded-xl shadow-lg shadow-[#FFE600]/25 hover:bg-[#e6cf00] focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-transparent transition duration-200"
                    >
                        Restablecer contraseña del usuario
                    </button>
                </form>

                @if ($tempPassword && (int) $passwordForUserId === (int) $managedUser->id)
                    <div class="rounded-xl border border-emerald-300/40 bg-emerald-500/10 p-3">
                        <p class="text-xs text-emerald-200 uppercase tracking-wide font-semibold">Contraseña temporal visible una sola vez</p>
                        <p class="text-lg text-white font-bold tracking-wide mt-1 break-all">{{ $tempPassword }}</p>
                        <p class="text-xs text-white/75 mt-1">Compártela con el usuario y pídele que la cambie al iniciar sesión.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
