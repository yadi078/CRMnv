<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Aprobaciones Pendientes - Usuarios</h2>
            <p class="page-header-card__subtitle">Usuarios en espera de aprobación</p>
        </div>
        <a href="{{ route('approvals.companies') }}" class="btn-panel-dark ml-auto">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Aprobaciones de empresas
            </a>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-0 overflow-hidden divide-y divide-white/10">
                @if($users->count() > 0)
                    @foreach($users as $user)
                    <div class="px-4 sm:px-5 py-4 flex flex-wrap items-start justify-between gap-4 hover:bg-white/5">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-white">{{ $user->name }}</h3>
                            <p class="text-sm text-white/80">Correo: {{ $user->email }}</p>
                            <p class="text-sm text-white/70">Registrado el {{ $user->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-sm text-white/70">
                                Rol:
                                @if($user->roles->isNotEmpty())
                                    @foreach($user->roles as $role)
                                        <span class="px-2 py-1 bg-white/20 text-white rounded text-xs">{{ ucfirst($role->name) }}</span>
                                    @endforeach
                                @else
                                    <span class="text-white/50 italic">Sin rol</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('approvals.users.approve', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="btn-panel-dark bg-emerald-600 hover:bg-emerald-500 text-white border-0">Aceptar</button>
                            </form>
                            <form method="POST" id="approval-legacy-us-den-{{ $user->id }}" action="{{ route('approvals.users.deny', $user) }}" class="inline">
                                @csrf
                                <button type="button" class="px-4 py-2 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-500 transition js-approval-confirm-trigger"
                                    data-form-id="approval-legacy-us-den-{{ $user->id }}"
                                    data-title="Denegar registro"
                                    data-message="¿Denegar y eliminar este registro? El usuario deberá registrarse nuevamente si desea intentarlo."
                                    data-variant="danger"
                                    data-confirm-text="Sí, denegar">Denegar</button>
                            </form>
                        </div>
                    </div>
                    @endforeach

                <div class="px-4 py-4 border-t border-white/10">
                    {{ $users->links() }}
                </div>
                @else
                <p class="text-center text-white py-12 px-4">No hay usuarios pendientes de aprobación</p>
                @endif
        </div>
    </div>

    @include('approvals.partials.approval-confirm-modal')
</x-app-layout>
