<x-app-layout>
    <x-slot name="header">
        <div class="view-header">
            <div class="view-header__icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h2 class="view-header__title">Solicitudes pendientes</h2>
                <p class="view-header__subtitle">Autorizar o denegar solicitudes de usuarios y empresas</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Pestañas --}}
            <div class="flex gap-1 p-1 rounded-xl bg-gray-100 mb-6">
                @can('companies.approve')
                <a href="{{ route('approvals.index', ['tab' => 'empresas']) }}"
                   class="flex-1 sm:flex-none px-4 py-2.5 rounded-lg text-sm font-medium transition {{ $tab === 'empresas' ? 'bg-white text-[#003366] shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    Empresas
                    @if($companiesCount > 0)
                        <span class="ml-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-bold bg-amber-500 text-white">{{ $companiesCount }}</span>
                    @endif
                </a>
                @endcan
                @can('users.approve')
                <a href="{{ route('approvals.index', ['tab' => 'usuarios']) }}"
                   class="flex-1 sm:flex-none px-4 py-2.5 rounded-lg text-sm font-medium transition {{ $tab === 'usuarios' ? 'bg-white text-[#003366] shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    Usuarios
                    @if($usersCount > 0)
                        <span class="ml-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-bold bg-amber-500 text-white">{{ $usersCount }}</span>
                    @endif
                </a>
                @endcan
            </div>

            <div class="view-card p-6">
                @if($tab === 'empresas')
                    @can('companies.approve')
                        @if($companies->count() > 0)
                            <div class="space-y-4">
                                @foreach($companies as $company)
                                    <div class="p-4 bg-amber-50/80 border-l-4 border-amber-500 rounded-lg flex flex-wrap items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900">{{ $company->nombre_comercial }}</h3>
                                            <p class="text-sm text-gray-600">RFC: {{ $company->rfc ?? '-' }}</p>
                                            <p class="text-sm text-gray-500">Solicitado por <strong>{{ $company->creator?->name ?? 'N/D' }}</strong> el {{ $company->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <form method="POST" action="{{ route('approvals.companies.approve', $company) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn-icon-text bg-green-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-green-700 transition shadow">
                                                    Aprobar
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('approvals.companies.deny', $company) }}" class="inline" onsubmit="return confirm('¿Denegar esta solicitud de empresa?');">
                                                @csrf
                                                <button type="submit" class="btn-icon-text bg-red-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-red-700 transition shadow">
                                                    Denegar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                {{ $companies->withQueryString()->links() }}
                            </div>
                        @else
                            <p class="text-center text-gray-500 py-8">No hay empresas pendientes de aprobación.</p>
                        @endif
                    @endcan
                @endif

                @if($tab === 'usuarios')
                    @can('users.approve')
                        @if($users->count() > 0)
                            <div class="space-y-4">
                                @foreach($users as $user)
                                    <div class="p-4 bg-amber-50/80 border-l-4 border-amber-500 rounded-lg flex flex-wrap items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                                            <p class="text-sm text-gray-600">Correo: {{ $user->email }}</p>
                                            <p class="text-sm text-gray-500">Registrado el {{ $user->created_at->format('d/m/Y H:i') }}</p>
                                            @if($user->roles->isNotEmpty())
                                                <p class="text-sm text-gray-500">
                                                    Rol: @foreach($user->roles as $role)<span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs">{{ ucfirst($role->name) }}</span> @endforeach
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <form method="POST" action="{{ route('approvals.users.approve', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn-icon-text bg-green-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-green-700 transition shadow">
                                                    Aprobar
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('approvals.users.deny', $user) }}" class="inline" onsubmit="return confirm('¿Denegar el registro de este usuario?');">
                                                @csrf
                                                <button type="submit" class="btn-icon-text bg-red-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-red-700 transition shadow">
                                                    Denegar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                {{ $users->withQueryString()->links() }}
                            </div>
                        @else
                            <p class="text-center text-gray-500 py-8">No hay usuarios pendientes de aprobación.</p>
                        @endif
                    @endcan
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
