<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Seguimientos</h2>
            <p class="page-header-card__subtitle">Listado de seguimientos</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Filtros</h3>
            <form method="GET" action="{{ route('follow-ups.index') }}" class="flex flex-wrap items-end gap-3 sm:gap-4 mb-0">
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-white/90 mb-1">Estado</label>
                    <select name="completado" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-white [&>option]:text-gray-900">
                        <option value="">Todos</option>
                        <option value="0" {{ request('completado') === '0' ? 'selected' : '' }}>Pendientes</option>
                        <option value="1" {{ request('completado') === '1' ? 'selected' : '' }}>Completados</option>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm font-medium text-white/90 mb-1">Tipo</label>
                    <select name="tipo_accion" class="w-full rounded-xl border-0 bg-white/15 text-white focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50 py-2.5 px-3 [&>option]:bg-white [&>option]:text-gray-900">
                        <option value="">Todos los tipos</option>
                        <option value="llamada" {{ request('tipo_accion') === 'llamada' ? 'selected' : '' }}>Llamada</option>
                        <option value="reunión" {{ request('tipo_accion') === 'reunión' ? 'selected' : '' }}>Reunión</option>
                        <option value="cierre" {{ request('tipo_accion') === 'cierre' ? 'selected' : '' }}>Cierre</option>
                    </select>
                </div>
                <button type="submit" class="btn-panel-dark">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
                    Filtrar
                </button>
                @can('follow-ups.create')
                <a href="{{ route('follow-ups.create') }}" class="btn-amber-app flex-shrink-0">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Nuevo Seguimiento
                </a>
                @endcan
            </form>
        </div>

        <div class="panel-card-dark p-0 overflow-hidden divide-y divide-white/10">
            @forelse($followUps as $followUp)
            <div class="flex items-start gap-3 px-4 sm:px-5 py-3 hover:bg-white/5 transition-colors border-l-4 @if($followUp->completado) border-l-[#15803D] @elseif($followUp->estaVencido()) border-l-[#B91C1C] @else border-l-[#B45309] @endif">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-lg badge-followup-{{ $followUp->completado ? 'completado' : ($followUp->estaVencido() ? 'vencido' : 'pendiente') }}">
                            {{ ucfirst($followUp->tipo_accion) }}
                        </span>
                        @if($followUp->completado)
                        <span class="text-xs font-medium badge-followup-completado px-2.5 py-1 rounded-lg">Completado</span>
                        @elseif($followUp->estaVencido())
                        <span class="text-xs font-medium badge-followup-vencido px-2.5 py-1 rounded-lg">Vencido</span>
                        @else
                        <span class="text-xs font-medium badge-followup-pendiente px-2.5 py-1 rounded-lg">Pendiente</span>
                        @endif
                    </div>
                    <p class="text-sm font-medium text-white">
                        @if($followUp->company)
                            Empresa: <a href="{{ route('companies.show', $followUp->company) }}" class="text-[#FFE600] hover:text-white">{{ $followUp->company->nombre_comercial }}</a>
                        @elseif($followUp->contact)
                            Contacto: <a href="{{ route('contacts.show', $followUp->contact) }}" class="text-[#FFE600] hover:text-white">{{ $followUp->contact->nombre_completo }}</a>
                        @else
                            <span class="text-white/70">Sin empresa/contacto asignado</span>
                        @endif
                    </p>
                    <p class="text-sm text-white/80 mt-0.5">Fecha: {{ $followUp->fecha_alarma->format('d/m/Y H:i') }}</p>
                    @if($followUp->bitacora_notas)
                    <p class="text-sm text-white/90 mt-1">{{ Str::limit($followUp->bitacora_notas, 100) }}</p>
                    @endif
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <a href="{{ route('follow-ups.show', $followUp) }}" class="text-[#FFE600] hover:text-white inline-flex items-center gap-1 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        Ver
                    </a>
                    @if(!$followUp->completado)
                    <form method="POST" action="{{ route('follow-ups.complete', $followUp) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-emerald-300 hover:text-emerald-200 inline-flex items-center gap-1 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Completar
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-4 py-12 text-center">
                <p class="text-white font-medium">No se encontraron seguimientos</p>
            </div>
            @endforelse
        </div>
        <div class="mt-6">
            {{ $followUps->links() }}
        </div>
    </div>
</x-app-layout>
