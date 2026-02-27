<x-app-user-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">{{ $sale->nombre_servicio }}</h2>
                    <p class="view-header__subtitle">Detalle de la venta</p>
                </div>
            </div>
            <div class="flex gap-2">
                @can('sales.edit')
                <a href="{{ route('user.sales.edit', $sale) }}" class="btn-amber-app">Editar</a>
                @endcan
                <a href="{{ route('user.sales.index') }}" class="btn-icon-text text-gray-600 hover:text-gray-800 px-4 py-2 rounded-xl border border-gray-300 hover:bg-gray-50">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="view-card view-card--azul p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Empresa</dt>
                        <dd class="mt-1">
                            <a href="{{ route('companies.show', $sale->company) }}" class="text-azul-fuerte font-medium hover:underline">{{ $sale->company->nombre_comercial }}</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Servicio / Curso</dt>
                        <dd class="mt-1 text-gray-900">{{ $sale->nombre_servicio }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fecha de venta</dt>
                        <dd class="mt-1 text-gray-900">{{ $sale->fecha_venta->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Monto</dt>
                        <dd class="mt-1 text-gray-900">{{ $sale->monto_formateado }}</dd>
                    </div>
                    @if($sale->tipo_pago)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tipo de pago</dt>
                        <dd class="mt-1 text-gray-900">{{ $sale->tipo_pago_label }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Participantes</dt>
                        <dd class="mt-1 text-gray-900">{{ $sale->participantes ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Registrado por</dt>
                        <dd class="mt-1 text-gray-900">{{ $sale->creator?->name ?? '—' }}</dd>
                    </div>
                    @if($sale->notas)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Notas</dt>
                        <dd class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $sale->notas }}</dd>
                    </div>
                    @endif
                </dl>

                @can('sales.delete')
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <form action="{{ route('user.sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro de venta?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar registro</button>
                    </form>
                </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-user-layout>
