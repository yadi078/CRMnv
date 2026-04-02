{{-- Resultados contactos --}}
@if(isset($contacts) && $contacts->isNotEmpty())
    <div class="panel-card-dark overflow-hidden border border-white/12 ring-1 ring-[#FFE600]/10 shadow-xl shadow-black/30">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 pb-4 mb-1 border-b border-white/15">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent !mb-0 text-xl md:text-2xl font-bold tracking-tight">Contactos</h3>
            <span class="text-sm text-white/60 font-medium tabular-nums">{{ $contacts->total() }} {{ $contacts->total() === 1 ? 'registro' : 'registros' }}</span>
        </div>
        <div class="crm-table-scroll-wrap crm-table-scroll w-full min-w-0 -mx-1 px-1">
            <table class="w-full crm-table-wide divide-y divide-white/15">
                <thead>
                    <tr class="table-header-panel-dark">
                        <th scope="col" class="crm-row-marker-head w-11 min-w-[2.75rem] px-1 py-3.5 text-center text-[10px] font-semibold uppercase tracking-wide text-[#FFE600]/90" title="Seguimiento personal (solo en este navegador)">Seg.</th>
                        <th class="min-w-[11rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Nombre</th>
                        <th class="min-w-[10rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Empresa</th>
                        <th class="min-w-[10.5rem] px-4 py-3.5 text-left text-xs font-semibold uppercase whitespace-nowrap">Tel / Cel</th>
                        <th class="min-w-[12rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Email</th>
                        <th class="min-w-[9rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Ejecutivo</th>
                        <th class="min-w-[8.5rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Estatus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($contacts as $contact)
                        <tr class="panel-card-dark__row odd:bg-white/5 even:bg-white/[0.02] hover:bg-white/10 transition-colors duration-150">
                            <x-crm-row-marker entity="contact" :id="$contact->id" />
                            <td class="px-4 py-3.5 text-sm align-top whitespace-normal break-words">
                                <a href="{{ \App\Support\CrmNavigation::withReturn(route('contacts.show', $contact)) }}" title="Ver ficha del contacto" class="font-medium text-white hover:text-[#FFE600] hover:underline focus:outline-none focus:ring-2 focus:ring-[#FFE600]/35 rounded">{{ $contact->nombre_completo }}</a>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-white/90 align-top whitespace-normal break-words">
                                @if($contact->company)
                                    <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $contact->company)) }}" title="Ver ficha de la empresa" class="hover:text-[#FFE600] hover:underline focus:outline-none focus:ring-2 focus:ring-[#FFE600]/35 rounded">{{ $contact->company->nombre_comercial }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-sm text-white/90 whitespace-nowrap tabular-nums">{{ $contact->telefono ?? $contact->celular ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-sm text-white/90 [overflow-wrap:anywhere]">{{ $contact->email ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-sm text-white/90">{{ $contact->comercialEjecutivoLabel() }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 text-xs rounded-lg badge-prospect-{{ $contact->status_color ?? 'seguimiento' }}">
                                    {{ $contact->status_label ?? '—' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5 pt-5 border-t border-white/20">{{ $contacts->links() }}</div>
    </div>
@elseif(isset($contacts) && $contacts->isEmpty() && !empty($filterSpecs))
    <div class="panel-card-dark p-8 text-center text-white/70 border border-white/10 rounded-2xl">No hay contactos con los filtros aplicados. Ajuste los criterios o limpie filtros.</div>
@endif

{{-- Resultados empresas --}}
@if(isset($companies) && $companies->isNotEmpty())
    <div class="panel-card-dark overflow-hidden border border-white/12 ring-1 ring-[#FFE600]/10 shadow-xl shadow-black/30">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 pb-4 mb-1 border-b border-white/15">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent !mb-0 text-xl md:text-2xl font-bold tracking-tight">Empresas</h3>
            <span class="text-sm text-white/60 font-medium tabular-nums">{{ $companies->total() }} {{ $companies->total() === 1 ? 'registro' : 'registros' }}</span>
        </div>
        <div class="crm-table-scroll-wrap crm-table-scroll w-full min-w-0 -mx-1 px-1">
            <table class="w-full crm-table-wide divide-y divide-white/15">
                <thead>
                    <tr class="table-header-panel-dark">
                        <th scope="col" class="crm-row-marker-head w-11 min-w-[2.75rem] px-1 py-3.5 text-center text-[10px] font-semibold uppercase tracking-wide text-[#FFE600]/90" title="Seguimiento personal (solo en este navegador)">Seg.</th>
                        <th class="min-w-[12rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Nombre</th>
                        <th class="min-w-[8.5rem] px-4 py-3.5 text-left text-xs font-semibold uppercase whitespace-nowrap">RFC</th>
                        <th class="min-w-[12rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Estatus</th>
                        <th class="min-w-[9rem] px-4 py-3.5 text-left text-xs font-semibold uppercase">Ejecutivo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($companies as $company)
                        <tr class="panel-card-dark__row odd:bg-white/5 even:bg-white/[0.02] hover:bg-white/10 transition-colors duration-150">
                            <x-crm-row-marker entity="company" :id="$company->id" />
                            <td class="px-4 py-3.5 text-sm align-top whitespace-normal break-words">
                                <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $company)) }}" title="Ver ficha de la empresa" class="font-medium text-white hover:text-[#FFE600] hover:underline focus:outline-none focus:ring-2 focus:ring-[#FFE600]/35 rounded">{{ $company->nombre_comercial }}</a>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-white/90">{{ $company->rfc ?? '—' }}</td>
                            <td class="px-4 py-3.5 align-top max-w-[14rem]">
                                <x-company-prospect-status-badges :company="$company" variant="filtros" />
                            </td>
                            <td class="px-4 py-3.5 text-sm text-white/90">{{ $company->assignedExecutive?->name ?? $company->ejecutivo_asignado ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5 pt-5 border-t border-white/20">{{ $companies->links() }}</div>
    </div>
@elseif(isset($companies) && $companies->isEmpty() && !empty($filterSpecs))
    <div class="panel-card-dark p-8 text-center text-white/70 border border-white/10 rounded-2xl">No hay empresas con los filtros aplicados. Ajuste los criterios o limpie filtros.</div>
@endif
