<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">{{ $company->nombre_comercial }}</h2>
            <p class="page-header-card__subtitle">Detalle de empresa</p>
        </div>
        <div class="flex gap-2 ml-auto">
                @can('companies.edit')
                <a href="{{ route('companies.edit', $company) }}" class="btn-amber-app">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                @endcan
                <a href="{{ route('companies.index') }}" class="btn-panel-dark bg-white/10 text-white border-2 border-[#FFE600] hover:bg-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Volver
                </a>
        </div>
    </x-slot>

    <div class="space-y-8">
            <!-- Información Principal -->
            <div class="panel-card-dark p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-0">Información de la Empresa</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded-full dot-prospect-{{ $company->status_color }}"></div>
                        <span class="text-sm font-medium text-white/90 badge-prospect-{{ $company->status_color }} px-2 py-0.5 rounded">{{ $company->status_label }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-white/70">Nombre Comercial</p>
                        <p class="text-lg font-medium text-white">{{ $company->nombre_comercial }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-white/70">RFC</p>
                        <p class="text-lg font-medium text-white">{{ $company->rfc ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-white/70">Sector/Giro</p>
                        <p class="text-lg font-medium text-white">{{ $company->sector ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-white/70">Ejecutivo Asignado</p>
                        <p class="text-lg font-medium text-white">{{ $company->ejecutivo_asignado ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-white/70">Ubicación</p>
                        <p class="text-lg font-medium text-white">{{ $company->municipio ?? '' }}{{ $company->estado ? ', ' . $company->estado : '' }}</p>
                    </div>
                    @if($company->datos_fiscales)
                    <div class="md:col-span-2">
                        <p class="text-sm text-white/70">Datos Fiscales</p>
                        <p class="text-white">{{ $company->datos_fiscales }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contactos -->
            <div class="panel-card-dark p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-0">Contactos</h3>
                    @can('contacts.create')
                    <a href="{{ route('contacts.create', ['company_id' => $company->id]) }}" class="btn-primary-app">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nuevo Contacto
                    </a>
                    @endcan
                </div>

                <div class="space-y-3">
                    @forelse($company->contacts as $contact)
                    <div class="p-4 bg-white/5 rounded-lg hover:bg-white/10 transition-colors">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-white">{{ $contact->nombre_completo }}</p>
                                <p class="text-sm text-white/80">{{ $contact->puesto_de_trabajo ?? '-' }}</p>
                                <p class="text-sm text-white/80">{{ $contact->email }}</p>
                                @if($contact->celular)
                                <p class="text-sm text-white/80">{{ $contact->celular }}</p>
                                @endif
                            </div>
                            <a href="{{ route('contacts.show', $contact) }}" class="text-[#FFE600] hover:text-white btn-icon-text">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-white/80 text-center py-4">No hay contactos registrados</p>
                    @endforelse
                </div>
            </div>

            <!-- Historial de Ventas -->
            @can('sales.view')
            <div class="panel-card-dark p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-0">Historial de Ventas</h3>
                    @can('sales.create')
                    <a href="{{ route('user.sales.create', ['company_id' => $company->id]) }}" class="btn-primary-app">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nueva Venta
                    </a>
                    @endcan
                </div>
                <div class="space-y-3">
                    @forelse($company->sales as $sale)
                    <div class="p-4 bg-white/5 rounded-lg flex justify-between items-start hover:bg-white/10 transition-colors">
                        <div>
                            <p class="font-medium text-white">{{ $sale->nombre_servicio }}</p>
                            <p class="text-sm text-white/80">{{ $sale->fecha_venta->format('d/m/Y') }} · {{ $sale->monto_formateado }}</p>
                            @if($sale->participantes)
                            <p class="text-sm text-white/80">{{ $sale->participantes }} participantes</p>
                            @endif
                        </div>
                        <a href="{{ route('user.sales.show', $sale) }}" class="text-[#FFE600] hover:text-white btn-icon-text">Ver</a>
                    </div>
                    @empty
                    <p class="text-white/80 text-center py-4">No hay ventas registradas</p>
                    @endforelse
                </div>
            </div>
            @endcan

            <!-- Seguimientos -->
            <div class="panel-card-dark p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-0">Seguimientos</h3>
                    @can('follow-ups.create')
                    <a href="{{ route('follow-ups.create', ['company_id' => $company->id]) }}" class="btn-primary-app">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nuevo Seguimiento
                    </a>
                    @endcan
                </div>

                <div class="space-y-3">
                    @forelse($company->followUps as $followUp)
                    <div class="p-4 bg-white/5 rounded-lg hover:bg-white/10 transition-colors">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="px-2 py-1 text-xs font-semibold rounded badge-followup-{{ $followUp->completado ? 'completado' : ($followUp->estaVencido() ? 'vencido' : 'pendiente') }}">
                                    {{ ucfirst($followUp->tipo_accion) }}
                                </span>
                                <p class="text-sm text-white/80 mt-2">{{ $followUp->fecha_alarma->format('d/m/Y H:i') }}</p>
                                @if($followUp->bitacora_notas)
                                <p class="text-sm text-white mt-2">{{ $followUp->bitacora_notas }}</p>
                                @endif
                            </div>
                            @if(!$followUp->completado)
                            <span class="text-xs badge-followup-{{ $followUp->estaVencido() ? 'vencido' : 'pendiente' }} px-2 py-1 rounded">{{ $followUp->estaVencido() ? 'Vencido' : 'Pendiente' }}</span>
                            @else
                            <span class="text-xs badge-followup-completado px-2 py-1 rounded">Completado</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-white/80 text-center py-4">No hay seguimientos registrados</p>
                    @endforelse
                </div>
            </div>
    </div>
</x-app-layout>
