<x-app-user-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">{{ $sale->nombre_servicio }}</h2>
            <p class="page-header-card__subtitle">Detalle de la venta</p>
        </div>
        <div class="flex gap-2 ml-auto">
            @can('sales.edit')
            <a href="{{ route('user.sales.edit', $sale) }}" class="btn-amber-app">Editar</a>
            @endcan
            <a href="{{ route('user.sales.index') }}" class="btn-panel-dark bg-white/10 text-white border-2 border-[#FFE600] hover:bg-white/20">Volver</a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-6">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Información de la Venta</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm text-white/70">Empresa</dt>
                    <dd class="mt-1">
                        <a href="{{ route('companies.show', $sale->company) }}" class="text-[#FFE600] hover:text-white font-medium">{{ $sale->company->nombre_comercial }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-white/70">Servicio / Curso</dt>
                    <dd class="mt-1 text-white">{{ $sale->nombre_servicio }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-white/70">Fecha de venta</dt>
                    <dd class="mt-1 text-white">{{ $sale->fecha_venta->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-white/70">Monto</dt>
                    <dd class="mt-1 text-white">{{ $sale->monto_formateado }}</dd>
                </div>
                @if($sale->tipo_pago)
                <div>
                    <dt class="text-sm text-white/70">Tipo de pago</dt>
                    <dd class="mt-1 text-white">{{ $sale->tipo_pago_label }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm text-white/70">Participantes</dt>
                    <dd class="mt-1 text-white">{{ $sale->participantes ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-white/70">Registrado por</dt>
                    <dd class="mt-1 text-white">{{ $sale->creator?->name ?? '—' }}</dd>
                </div>
                @if($sale->notas)
                <div class="md:col-span-2">
                    <dt class="text-sm text-white/70">Notas</dt>
                    <dd class="mt-1 text-white whitespace-pre-wrap">{{ $sale->notas }}</dd>
                </div>
                @endif
            </dl>

            @can('sales.delete')
            <div class="mt-8 pt-6 border-t border-white/20">
                <form action="{{ route('user.sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro de venta?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-300 text-sm font-medium">Eliminar registro</button>
                </form>
            </div>
            @endcan
        </div>
    </div>
</x-app-user-layout>
