{{-- Resultados contactos --}}
@if(isset($contacts) && $contacts->isNotEmpty())
    <div class="panel-card-dark overflow-hidden">
        <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Resultados ({{ $contacts->total() }})</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/20">
                <thead>
                    <tr class="table-header-panel-dark">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Empresa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Tel / Cel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($contacts as $contact)
                        <tr class="panel-card-dark__row hover:bg-white/5">
                            <td class="px-4 py-3 text-sm text-white">{{ $contact->nombre_completo }}</td>
                            <td class="px-4 py-3 text-sm text-white/90">{{ $contact->company?->nombre_comercial ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-white/90">{{ $contact->telefono ?? $contact->celular ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-white/90">{{ $contact->email ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs rounded-lg badge-prospect-{{ $contact->status_color ?? 'seguimiento' }}">
                                    {{ $contact->status_label ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('contacts.show', $contact) }}" class="text-[#FFE600] hover:text-white text-sm">Ver</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 pt-4 border-t border-white/20">{{ $contacts->links() }}</div>
    </div>
@elseif(isset($contacts) && $contacts->isEmpty() && !empty($filterSpecs))
    <div class="panel-card-dark p-6 text-center text-white/70">No hay contactos con los filtros aplicados. Ajuste los criterios o limpie filtros.</div>
@endif

{{-- Resultados empresas --}}
@if(isset($companies) && $companies->isNotEmpty())
    <div class="panel-card-dark overflow-hidden">
        <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Resultados ({{ $companies->total() }})</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/20">
                <thead>
                    <tr class="table-header-panel-dark">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase">RFC</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Ejecutivo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($companies as $company)
                        <tr class="panel-card-dark__row hover:bg-white/5">
                            <td class="px-4 py-3 text-sm text-white">{{ $company->nombre_comercial }}</td>
                            <td class="px-4 py-3 text-sm text-white/90">{{ $company->rfc ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs rounded-lg badge-prospect-{{ $company->status_color }}">
                                    {{ $company->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-white/90">{{ $company->ejecutivo_asignado ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('companies.show', $company) }}" class="text-[#FFE600] hover:text-white text-sm">Ver</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 pt-4 border-t border-white/20">{{ $companies->links() }}</div>
    </div>
@elseif(isset($companies) && $companies->isEmpty() && !empty($filterSpecs))
    <div class="panel-card-dark p-6 text-center text-white/70">No hay empresas con los filtros aplicados. Ajuste los criterios o limpie filtros.</div>
@endif
