<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="view-header">
                <div class="view-header__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <h2 class="view-header__title">Seguimientos</h2>
                    <p class="view-header__subtitle">Listado de seguimientos</p>
                </div>
            </div>
            @can('follow-ups.create')
            <a href="{{ route('follow-ups.create') }}" class="btn-amber-app">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo Seguimiento
            </a>
            @endcan
        </div>
    </x-slot>

    <div>
            <div class="view-card">
                <!-- Filtros -->
                <form method="GET" action="{{ route('follow-ups.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <select name="completado" class="rounded-xl border-[#E2E8F0] bg-white shadow-sm focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 py-2.5 px-3 text-[#1F2937]">
                        <option value="">Todos</option>
                        <option value="0" {{ request('completado') === '0' ? 'selected' : '' }}>Pendientes</option>
                        <option value="1" {{ request('completado') === '1' ? 'selected' : '' }}>Completados</option>
                    </select>
                    <select name="tipo_accion" class="rounded-xl border-[#E2E8F0] bg-white shadow-sm focus:border-[#003366] focus:ring-2 focus:ring-[#003366]/10 py-2.5 px-3 text-[#1F2937]">
                        <option value="">Todos los tipos</option>
                        <option value="llamada" {{ request('tipo_accion') === 'llamada' ? 'selected' : '' }}>Llamada</option>
                        <option value="reunión" {{ request('tipo_accion') === 'reunión' ? 'selected' : '' }}>Reunión</option>
                        <option value="cierre" {{ request('tipo_accion') === 'cierre' ? 'selected' : '' }}>Cierre</option>
                    </select>
                    <button type="submit" class="btn-primary-app">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                        </svg>
                        Filtrar
                    </button>
                </form>

                <!-- Lista -->
                <div class="space-y-3">
                    @forelse($followUps as $followUp)
                    <div class="p-4 rounded-xl bg-fondo border-l-4 @if($followUp->completado) border-l-[#15803D] @elseif($followUp->estaVencido()) border-l-[#B91C1C] @else border-l-[#B45309] @endif">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-[rgba(0,51,102,0.08)] text-[#003366]">
                                        {{ ucfirst($followUp->tipo_accion) }}
                                    </span>
                                    @if($followUp->completado)
                                    <span class="text-xs font-medium text-[#15803D] bg-[#F0FDF4] px-2.5 py-1 rounded-lg">Completado</span>
                                    @elseif($followUp->estaVencido())
                                    <span class="text-xs font-medium text-[#B91C1C] bg-[#FEF2F2] px-2.5 py-1 rounded-lg">Vencido</span>
                                    @else
                                    <span class="text-xs font-medium text-[#B45309] bg-[#FFFBEB] px-2.5 py-1 rounded-lg">Pendiente</span>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-[#1F2937]">
                                    @if($followUp->company)
                                        Empresa: <a href="{{ route('companies.show', $followUp->company) }}" class="text-[#003366] hover:text-[#000836]">{{ $followUp->company->nombre_comercial }}</a>
                                    @elseif($followUp->contact)
                                        Contacto: <a href="{{ route('contacts.show', $followUp->contact) }}" class="text-[#003366] hover:text-[#000836]">{{ $followUp->contact->nombre_completo }}</a>
                                    @else
                                        <span class="text-[#6B7280]">Sin empresa/contacto asignado</span>
                                    @endif
                                </p>
                                <p class="text-sm text-[#6B7280] mt-1">Fecha: {{ $followUp->fecha_alarma->format('d/m/Y H:i') }}</p>
                                @if($followUp->bitacora_notas)
                                <p class="text-sm text-[#1F2937] mt-2">{{ Str::limit($followUp->bitacora_notas, 100) }}</p>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('follow-ups.show', $followUp) }}" class="text-[#003366] hover:text-[#000836] btn-icon-text">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver
                                </a>
                                @if(!$followUp->completado)
                                <form method="POST" action="{{ route('follow-ups.complete', $followUp) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[#15803D] hover:text-[#166534] btn-icon-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Completar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-[#6B7280] text-center py-8">No se encontraron seguimientos</p>
                    @endforelse
                </div>

                <!-- Paginación -->
                <div class="mt-6 pt-4 border-t border-[#E2E8F0]">
                    {{ $followUps->links() }}
                </div>
            </div>
    </div>
</x-app-layout>
