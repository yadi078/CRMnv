<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">Aprobaciones Pendientes - Usuarios</h2>
                    <p class="view-header__subtitle">Usuarios en espera de aprobación</p>
                </div>
            </div>
            <a href="{{ route('approvals.companies') }}" class="btn-primary-app">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Aprobaciones de empresas
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="view-card p-6">
                @if($users->count() > 0)
                <div class="space-y-4">
                    @foreach($users as $user)
                    <div class="p-4 bg-yellow-50 border-l-4 border-amarillo rounded-lg">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-600">Correo: {{ $user->email }}</p>
                                <p class="text-sm text-gray-500">Registrado el {{ $user->created_at->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-gray-500">
                                    Rol:
                                    @if($user->roles->isNotEmpty())
                                        @foreach($user->roles as $role)
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400 italic">Sin rol</span>
                                    @endif
                                </p>
                            </div>
                            <form method="POST" action="{{ route('approvals.users.approve', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="btn-icon-text bg-green-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-green-700 transition shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Aceptar
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
                @else
                <p class="text-center text-gray-500 py-8">No hay usuarios pendientes de aprobación</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
