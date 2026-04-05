@if (!auth()->user()?->esAdmin())
    @php return; @endphp
@endif

@php
    /** @var \App\Models\User|null $managedUser */
    $tempPassword = session('admin_generated_password');
    $passwordForUserId = session('admin_password_user_id');
    $managedUsersSuggestions = $managedUsersSuggestions ?? collect();
@endphp

@php
    $salirAsistenciaQuery = collect(request()->query())
        ->except(['user_search'])
        ->merge(['cerrar_asistencia_contrasenas' => 1])
        ->filter(fn ($v) => $v !== null && $v !== '')
        ->all();
@endphp

<section id="asistencia-contrasenas" class="scroll-mt-24 space-y-3 lg:scroll-mt-8">
    <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">
        <div class="flex min-w-0 items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#FFE600]/15 text-[#FFE600] ring-1 ring-[#FFE600]/25">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.567 3-3.5S13.657 4 12 4 9 5.567 9 7.5 10.343 11 12 11zm0 0c-3.314 0-6 1.79-6 4v1h12v-1c0-2.21-2.686-4-6-4zm6.5-2.5h3m-1.5-1.5v3" />
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-lg font-bold leading-tight text-white tracking-tight">Asistencia de contraseñas</h2>
                <p class="mt-0.5 text-xs leading-snug text-white/65">
                    Busca por nombre o correo y restablece la contraseña si hace falta.
                </p>
            </div>
        </div>
        <a
            href="{{ route('executives.index', $salirAsistenciaQuery) }}#asistencia-contrasenas"
            class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-lg border border-white/30 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#FFE600]/45"
        >
            <svg class="h-4 w-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Salir
        </a>
    </div>

    <div class="rounded-xl border border-white/15 bg-white/[0.04] p-3 sm:p-4">
        <form method="GET" action="{{ route('executives.index') }}#asistencia-contrasenas" class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
            @foreach (['empresa_id', 'estado', 'contacto_id'] as $filterKey)
                @if (request()->filled($filterKey))
                    <input type="hidden" name="{{ $filterKey }}" value="{{ request($filterKey) }}">
                @endif
            @endforeach
            <div class="min-w-0">
                <x-input-label for="user_search" :value="'Nombre o correo'" class="text-sm font-medium text-white/90" />
                <x-text-input
                    id="user_search"
                    name="user_search"
                    type="text"
                    list="admin-users-suggestions"
                    class="mt-1.5 block w-full rounded-lg border-[#E2E8F0] py-2 shadow-sm transition focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 sm:py-2.5"
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
                class="inline-flex h-[42px] w-full shrink-0 items-center justify-center rounded-lg bg-[#FFE600] px-4 text-sm font-semibold text-[#003366] shadow-md shadow-[#FFE600]/20 transition hover:bg-[#e6cf00] focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-[#071A3D] lg:w-auto"
            >
                Buscar usuario
            </button>
        </form>

        @if (($userSearch ?? '') !== '' && ! $managedUser)
            <p class="mt-3 text-sm text-amber-200">
                No se encontró ningún usuario con ese criterio.
            </p>
        @endif

        @if ($managedUser)
            <div class="mt-4 space-y-3 rounded-xl border border-[#FFE600]/35 bg-[#0f2744]/60 p-3 sm:p-4">
                <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-[#FFE600]/90">Nombre de usuario</p>
                        <p class="mt-0.5 font-medium text-white">{{ $managedUser->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-[#FFE600]/90">Correo electrónico</p>
                        <p class="mt-0.5 break-all font-medium text-white">{{ $managedUser->email }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.admin-reset-user-password') }}" class="space-y-2 border-t border-white/10 pt-3">
                    @csrf
                    <input type="hidden" name="managed_user_id" value="{{ $managedUser->id }}">
                    <div class="space-y-1.5">
                        <x-input-label for="new_password" :value="'Nueva contraseña (opcional)'" class="text-xs font-medium text-white/85" />
                        <x-text-input
                            id="new_password"
                            name="new_password"
                            type="text"
                            class="block w-full rounded-lg border-[#E2E8F0] py-2 shadow-sm transition focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 sm:py-2.5"
                            placeholder="Vacío = generar una temporal segura"
                        />
                        <x-input-error class="mt-1" :messages="$errors->get('new_password')" />
                    </div>
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-lg bg-[#FFE600] px-4 py-2 text-sm font-semibold text-[#003366] shadow-md shadow-[#FFE600]/20 transition hover:bg-[#e6cf00] focus:outline-none focus:ring-2 focus:ring-[#FFE600] sm:w-auto"
                    >
                        Restablecer contraseña
                    </button>
                </form>

                @if ($tempPassword && (int) $passwordForUserId === (int) $managedUser->id)
                    <div class="rounded-lg border border-emerald-300/35 bg-emerald-500/10 p-2.5 sm:p-3">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-200">Contraseña temporal (una sola vez)</p>
                        <p class="mt-1 break-all text-base font-bold tracking-wide text-white">{{ $tempPassword }}</p>
                        <p class="mt-1 text-[11px] text-white/70">Compártela con el usuario; debería cambiarla al entrar.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if (request()->filled('user_search'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!window.location.hash) {
                    document.getElementById('asistencia-contrasenas')?.scrollIntoView({ block: 'start' });
                }
            });
        </script>
    @endif
</section>
