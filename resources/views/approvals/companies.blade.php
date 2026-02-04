<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-azul-fuerte leading-tight">
            Aprobaciones Pendientes - Empresas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                @if($companies->count() > 0)
                <div class="space-y-4">
                    @foreach($companies as $company)
                    <div class="p-4 bg-yellow-50 border-l-4 border-amarillo rounded-lg">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $company->nombre_comercial }}</h3>
                                <p class="text-sm text-gray-600">RFC: {{ $company->rfc }}</p>
                                <p class="text-sm text-gray-500">Creado por: {{ $company->creator->name ?? 'N/A' }} el {{ $company->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <form method="POST" action="{{ route('approvals.companies.approve', $company) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-green-700 transition inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Aprobar
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
