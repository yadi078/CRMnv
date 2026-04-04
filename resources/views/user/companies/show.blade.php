<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">{{ $company->nombre_comercial }}</h2>
            <p class="page-header-card__subtitle">Detalle de empresa</p>
        </div>
        <div class="flex flex-wrap gap-2 ml-auto justify-end items-center">
                @can('companies.edit')
                <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.edit', $company)) }}" class="btn-amber-app">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                @endcan
                @can('requestDeletion', $company)
                <form method="POST" action="{{ route('companies.request-deletion', $company) }}" class="inline-flex" onsubmit="return confirm('¿Enviar solicitud para eliminar esta empresa? Un administrador deberá aprobarlo.');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold bg-red-600/90 text-white hover:bg-red-500 border border-red-400/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Solicitar eliminación
                    </button>
                </form>
                @endcan
                @if($company->deletion_pending ?? false)
                <span class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-semibold bg-amber-500/20 text-amber-200 border border-amber-400/40">
                    Eliminación pendiente de aprobación
                </span>
                @endif
                @can('companies.delete')
                <form action="{{ route('companies.destroy', $company) }}" method="POST" class="inline-flex" onsubmit="return confirm('¿Seguro que deseas eliminar esta empresa? Esta acción no se puede deshacer.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-red-400/55 bg-red-500/20 text-red-100 text-sm font-medium px-3 sm:px-4 py-2 hover:bg-red-500/35 focus:outline-none focus:ring-2 focus:ring-red-400/50">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h10a1 1 0 00-1-1h-3V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v2H9a1 1 0 00-1 1z" />
                        </svg>
                        Eliminar empresa
                    </button>
                </form>
                @endcan
        </div>
    </x-slot>

    <div class="company-show company-show__sections">
            <x-pending-approval-notice :model="$company" entity-label="empresa" />

            @if(($company->deletion_resolution ?? '') === 'denied'
                && (int) ($company->deletion_decision_user_id ?? 0) === (int) auth()->id()
                && filled($company->deletion_resolution_note))
                <x-deletion-denied-alert
                    class="mb-6"
                    :note="$company->deletion_resolution_note"
                    :resolvedAt="$company->deletion_resolved_at"
                    entity-label="empresa"
                />
            @endif
            <!-- Información Principal -->
            <section class="company-show__card company-show__card--info">
                <div class="company-show__card-header">
                    <div>
                        <h3 class="company-show__card-title">Información de la Empresa</h3>
                        <p class="company-show__card-subtitle">Datos generales del prospecto</p>
                    </div>
                    <span class="company-show__badge badge-prospect-{{ $company->status_color }}">{{ $company->status_label }}</span>
                </div>
                <div class="company-show__info-resumen">
                    <span>{{ $company->contacts->count() }} contacto{{ $company->contacts->count() !== 1 ? 's' : '' }}</span>
                    <span class="company-show__info-resumen-sep">·</span>
                    <span>{{ $company->followUps->count() }} seguimiento{{ $company->followUps->count() !== 1 ? 's' : '' }}</span>
                    @if(isset($company->created_at) && $company->created_at)
                    <span class="company-show__info-resumen-sep">·</span>
                    <span>Registrado el {{ $company->created_at->format('d/m/Y') }}</span>
                    @endif
                </div>
                <div class="company-show__info-grid">
                    <div class="company-show__info-item">
                        <span class="company-show__info-label">
                            <svg class="company-show__info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            Nombre Comercial
                        </span>
                        <span class="company-show__info-value">{{ $company->nombre_comercial }}</span>
                    </div>
                    <div class="company-show__info-item">
                        <span class="company-show__info-label">
                            <svg class="company-show__info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            RFC
                        </span>
                        <span class="company-show__info-value">{{ $company->rfc ?? '-' }}</span>
                    </div>
                    <div class="company-show__info-item">
                        <span class="company-show__info-label">
                            <svg class="company-show__info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            Sector/Giro
                        </span>
                        <span class="company-show__info-value">{{ $company->sector ?? '-' }}</span>
                    </div>
                    <div class="company-show__info-item">
                        <span class="company-show__info-label">
                            <svg class="company-show__info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Ejecutivo Asignado
                        </span>
                        <span class="company-show__info-value">{{ $company->ejecutivo_asignado ?? '-' }}</span>
                    </div>
                    <div class="company-show__info-item">
                        <span class="company-show__info-label">
                            <svg class="company-show__info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            Ubicación
                        </span>
                        <span class="company-show__info-value">{{ trim(($company->municipio ?? '') . ($company->estado ? ', ' . $company->estado : '')) ?: '-' }}</span>
                    </div>
                    @if($company->telefono || $company->celular)
                    <div class="company-show__info-item">
                        <span class="company-show__info-label">
                            <svg class="company-show__info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            Teléfono / Celular
                        </span>
                        <span class="company-show__info-value">
                            @if($company->telefono)<span class="block sm:inline">Tel. {{ $company->telefono }}</span>@endif
                            @if($company->telefono && $company->celular)<span class="hidden sm:inline"> · </span>@endif
                            @if($company->celular)<span class="block sm:inline">Cel. {{ $company->celular }}</span>@endif
                        </span>
                    </div>
                    @endif
                    @if($company->datos_fiscales)
                    <div class="company-show__info-item">
                        <span class="company-show__info-label">
                            <svg class="company-show__info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2V5a2 2 0 00-2-2h-2.5" /></svg>
                            Datos Fiscales
                        </span>
                        <span class="company-show__info-value">{{ $company->datos_fiscales }}</span>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Contactos -->
            <section class="company-show__card company-show__card--contacts">
                <div class="company-show__card-header">
                    <h3 class="company-show__card-title">Contactos</h3>
                    @can('contacts.create')
                    <a href="{{ route('contacts.create', ['company_id' => $company->id]) }}" class="company-show__btn-action">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Nuevo Contacto
                    </a>
                    @endcan
                </div>
                <div class="company-show__list">
                    @forelse($company->contacts as $contact)
                    <div class="company-show__list-item company-show__list-item--contact">
                        <div class="company-show__list-body">
                            <a href="{{ \App\Support\CrmNavigation::withReturn(route('contacts.show', $contact)) }}" class="company-show__list-title text-white hover:text-[#FFE600] hover:underline focus:outline-none focus:ring-2 focus:ring-[#FFE600]/35 rounded inline-block">{{ $contact->nombre_completo }}</a>
                            <p class="company-show__list-meta">{{ $contact->puesto_de_trabajo ?? '-' }}</p>
                            <p class="company-show__list-meta">{{ $contact->email }}</p>
                            @if($contact->celular)
                            <p class="company-show__list-meta">{{ $contact->celular }}</p>
                            @endif
                        </div>
                        @can('sales.view')
                        <a href="{{ route('user.sales.by-contact', $contact) }}" class="company-show__list-link shrink-0 text-xs font-semibold uppercase tracking-wide">Ventas</a>
                        @endcan
                    </div>
                    @empty
                    <p class="company-show__empty">No hay contactos registrados</p>
                    @endforelse
                </div>
            </section>

            <!-- Historial de Ventas -->
            @can('sales.view')
            <section class="company-show__card company-show__card--sales">
                <div class="company-show__card-header">
                    <h3 class="company-show__card-title">Historial de Ventas</h3>
                    @can('sales.create')
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('user.sales.create', ['company_id' => $company->id]) }}" class="company-show__btn-action">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Nueva Venta
                        </a>
                    </div>
                    @endcan
                </div>
                <div class="company-show__list">
                    @forelse($company->sales as $sale)
                    <div class="company-show__list-item company-show__list-item--sale">
                        <div class="company-show__list-body">
                            <p class="company-show__list-title">{{ $sale->nombre_servicio }}</p>
                            <p class="company-show__list-meta">{{ $sale->fecha_venta->format('d/m/Y') }} · {{ $sale->monto_formateado }}</p>
                            @if($sale->participantes)
                            <p class="company-show__list-meta">{{ $sale->participantes }} participantes</p>
                            @endif
                        </div>
                        @can('view', $sale)
                        <a href="{{ route('user.sales.ficha-pdf', $sale) }}" target="_blank" rel="noopener noreferrer" class="company-show__list-link" title="Descargar ficha de inscripción (PDF)" aria-label="Descargar ficha de inscripción (PDF)">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </a>
                        @endcan
                    </div>
                    @empty
                    <p class="company-show__empty">No hay ventas registradas</p>
                    @endforelse
                </div>
            </section>
            @endcan

            <!-- Seguimientos -->
            <section class="company-show__card company-show__card--followups">
                <div class="company-show__card-header">
                    <h3 class="company-show__card-title">Seguimientos</h3>
                    @can('follow-ups.create')
                    <a href="{{ route('follow-ups.create', ['company_id' => $company->id]) }}" class="company-show__btn-action">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Nuevo Seguimiento
                    </a>
                    @endcan
                </div>
                <div class="company-show__list">
                    @forelse($company->followUps as $followUp)
                    <div class="company-show__list-item company-show__list-item--followup">
                        <div class="company-show__list-body">
                            <span class="company-show__badge-inline badge-followup-{{ $followUp->completado ? 'completado' : ($followUp->estaVencido() ? 'vencido' : 'pendiente') }}">{{ ucfirst($followUp->tipo_accion) }}</span>
                            <p class="company-show__list-meta mt-2">{{ $followUp->fecha_alarma->format('d/m/Y H:i') }}</p>
                            @if($followUp->bitacora_notas)
                            <p class="company-show__list-text mt-2">{{ $followUp->bitacora_notas }}</p>
                            @endif
                        </div>
                        @if(!$followUp->completado)
                        <span class="company-show__status badge-followup-{{ $followUp->estaVencido() ? 'vencido' : 'pendiente' }}">{{ $followUp->estaVencido() ? 'Vencido' : 'Pendiente' }}</span>
                        @else
                        <span class="company-show__status badge-followup-completado">Completado</span>
                        @endif
                    </div>
                    @empty
                    <p class="company-show__empty">No hay seguimientos registrados</p>
                    @endforelse
                </div>
            </section>
    </div>
</x-app-user-layout>
