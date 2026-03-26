<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Seguimiento - {{ ucfirst($followUp->tipo_accion) }}</h2>
            <p class="page-header-card__subtitle">Detalle del seguimiento</p>
        </div>
        <div class="flex gap-2 ml-auto">
            @can('follow-ups.edit')
            <a href="{{ route('follow-ups.edit', $followUp) }}" class="btn-amber-app">Editar</a>
            @endcan
            <a href="{{ route('follow-ups.index') }}" class="btn-panel-dark bg-white/10 text-white border-2 border-[#FFE600] hover:bg-white/20">Volver</a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-6 space-y-6">
            <div>
                <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Información del Seguimiento</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-white/70">Tipo de Acción</p>
                        <span class="px-3 py-1 text-sm font-semibold rounded badge-followup-{{ $followUp->completado ? 'completado' : ($followUp->estaVencido() ? 'vencido' : 'pendiente') }}">{{ ucfirst($followUp->tipo_accion) }}</span>
                    </div>
                    <div>
                        <p class="text-sm text-white/70">Estado</p>
                        @if($followUp->completado)
                        <span class="px-3 py-1 text-sm font-semibold rounded badge-followup-completado">Completado</span>
                        @elseif($followUp->estaVencido())
                        <span class="px-3 py-1 text-sm font-semibold rounded badge-followup-vencido">Vencido</span>
                        @else
                        <span class="px-3 py-1 text-sm font-semibold rounded badge-followup-pendiente">Pendiente</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-white/70">Fecha Programada</p>
                        <p class="text-lg font-medium text-white">{{ $followUp->fecha_alarma->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($followUp->asignado)
                    <div>
                        <p class="text-sm text-white/70">Asignado a</p>
                        <p class="text-lg font-medium text-white">{{ $followUp->asignado->name }}</p>
                    </div>
                    @endif
                    @if($followUp->company)
                    <div>
                        <p class="text-sm text-white/70">Empresa</p>
                        <p class="text-lg font-medium text-white">
                            <a href="{{ route('companies.show', $followUp->company) }}" class="text-[#FFE600] hover:text-white">{{ $followUp->company->nombre_comercial }}</a>
                        </p>
                    </div>
                    @endif
                    @if($followUp->contact)
                    <div>
                        <p class="text-sm text-white/70">Contacto</p>
                        <p class="text-lg font-medium text-white">
                            <a href="{{ route('contacts.show', $followUp->contact) }}" class="text-[#FFE600] hover:text-white">{{ $followUp->contact->nombre_completo }}</a>
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @if($followUp->bitacora_notas)
            <div>
                <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Bitácora de Notas</h3>
                <div class="bg-white/5 p-4 rounded-xl">
                    <p class="text-white whitespace-pre-wrap">{{ $followUp->bitacora_notas }}</p>
                </div>
            </div>
            @endif
            @if(!$followUp->completado)
            <div class="flex justify-end">
                <form method="POST" action="{{ route('follow-ups.complete', $followUp) }}">
                    @csrf
                    <button type="submit" class="btn-primary-app">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Marcar como Completado
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-app-user-layout>
