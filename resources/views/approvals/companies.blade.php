<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">Aprobaciones Pendientes - Empresas</h2>
                    <p class="view-header__subtitle">Empresas en espera de aprobación</p>
                </div>
            </div>
            @can('users.approve')
            <a href="{{ route('approvals.users') }}" class="btn-primary-app">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Aprobaciones de usuarios
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="view-card p-6">
                @if($companies->count() > 0)
                <div class="space-y-4">
                    @foreach($companies as $company)
                    <div class="p-4 bg-yellow-50 border-l-4 border-amarillo rounded-lg">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $company->nombre_comercial }}</h3>
                                <p class="text-sm text-gray-600">RFC: {{ $company->rfc }}</p>
                                <p class="text-sm text-gray-500">Creado por: {{ $company->creator?->name ?? 'N/A' }} el {{ $company->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <form method="POST" action="{{ route('approvals.companies.approve', $company) }}" class="inline">
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
                    {{ $companies->links() }}
                </div>
                @else
                <p class="text-center text-gray-500 py-8">No hay empresas pendientes de aprobación</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
