<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1">
            <x-page-header-avatar :user="$adminUser" :fallback-initials="true" :compact="true">
                <svg class="text-[#FFE600]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </x-page-header-avatar>
            <div class="min-w-0 flex-1">
                <h2 class="page-header-card__title">Administrador</h2>
                <p class="page-header-card__subtitle truncate sm:whitespace-normal">{{ $adminUser->name }} — cuenta con permisos de administración del CRM</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 ml-auto justify-end items-center shrink-0">
            <a href="{{ route('executives.index') }}" class="btn-amber-app inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver a Ejecutivos
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl space-y-6">
        <div class="panel-card-dark p-6 space-y-4">
            <p class="text-sm text-white/80">
                Los administradores no tienen cartera de empresas/contactos en este módulo. Use el listado de <strong class="text-[#FFE600]">Ejecutivos</strong> para gestionar cuentas comerciales y asignaciones.
            </p>
            <dl class="grid gap-3 text-sm">
                <div>
                    <dt class="text-[#FFE600]/90 font-semibold text-xs uppercase tracking-wide">Nombre</dt>
                    <dd class="mt-0.5 text-white">{{ $adminUser->name }}</dd>
                </div>
                <div>
                    <dt class="text-[#FFE600]/90 font-semibold text-xs uppercase tracking-wide">Correo</dt>
                    <dd class="mt-0.5 text-white break-all">{{ $adminUser->email }}</dd>
                </div>
                <div>
                    <dt class="text-[#FFE600]/90 font-semibold text-xs uppercase tracking-wide">Roles</dt>
                    <dd class="mt-0.5 flex flex-wrap gap-2">
                        @forelse($adminUser->roles as $role)
                            <span class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-medium bg-[#FFE600]/20 text-[#FFE600] border border-[#FFE600]/35">{{ $role->name }}</span>
                        @empty
                            <span class="text-white/60">—</span>
                        @endforelse
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</x-app-layout>
