@php
    $emptyExtra = $emptyExtra ?? null;
    $suppressGlobalCreateLink = $suppressGlobalCreateLink ?? false;
    $showFichaInscripcionColumn = $showFichaInscripcionColumn ?? false;
    $showActionsColumn = $showActionsColumn ?? true;
    $salesTableColCount = 3 + ($showFichaInscripcionColumn ? 1 : 0) + ($showActionsColumn ? 1 : 0);
@endphp
<div class="crm-responsive-x">
    <table class="min-w-full border-collapse">
        <thead>
            <tr class="border-b border-[#5b8fc7]/50">
                @if($showFichaInscripcionColumn)
                <th class="text-left py-3 px-4 sm:px-6 text-[#FFE600] font-semibold text-sm uppercase tracking-wide whitespace-nowrap">Ficha de inscripción</th>
                @endif
                <th class="text-left py-3 px-6 text-[#FFE600] font-semibold text-sm uppercase tracking-wide">Empresa</th>
                <th class="text-left py-3 px-6 text-[#FFE600] font-semibold text-sm uppercase tracking-wide">Contacto</th>
                <th class="text-left py-3 px-6 text-[#FFE600] font-semibold text-sm uppercase tracking-wide">Curso vendido</th>
                @if($showActionsColumn)
                <th class="text-left py-3 px-4 sm:px-6 text-[#FFE600] font-semibold text-sm uppercase tracking-wide whitespace-nowrap">Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
            <tr class="border-b border-[#5b8fc7]/30 hover:bg-white/5 transition-colors">
                @if($showFichaInscripcionColumn)
                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">
                    @can('view', $sale)
                        <a href="{{ route('user.sales.ficha-pdf', $sale) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#FFE600] hover:text-white underline underline-offset-2" title="Descargar ficha de inscripción (PDF)">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            PDF
                        </a>
                    @else
                        <span class="text-white/40 text-sm">—</span>
                    @endcan
                </td>
                @endif
                <td class="py-4 px-6 align-top whitespace-normal break-words">
                    <span class="text-[#FFE600] font-medium">{{ $sale->company->nombre_comercial }}</span>
                </td>
                <td class="py-4 px-6 align-top whitespace-normal break-words text-white">
                    {{ $sale->contact?->nombre_completo ?? '—' }}
                </td>
                <td class="py-4 px-6 align-top whitespace-normal break-words text-white/95">
                    @php
                        $nombreCurso = $sale->nombre_curso_ficha;
                        $tipo = trim((string) ($sale->tipo_curso ?? ''));
                    @endphp
                    @if($nombreCurso !== '—')
                        <div class="font-medium text-white">{{ $nombreCurso }}</div>
                        @if($tipo !== '')
                            <div class="mt-0.5 text-sm text-white/75">{{ $tipo }}</div>
                        @endif
                    @elseif($tipo !== '')
                        <div class="font-medium text-white">{{ $tipo }}</div>
                    @else
                        <span class="text-white/60">—</span>
                    @endif
                </td>
                @if($showActionsColumn)
                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">
                    <div class="flex flex-wrap items-center gap-2">
                        @can('view', $sale)
                        <a href="{{ \App\Support\CrmNavigation::withReturn(route('user.sales.show', $sale)) }}" class="text-sm font-semibold text-[#FFE600] hover:text-white underline underline-offset-2">Ver</a>
                        @endcan
                        @can('delete', $sale)
                        <form method="POST" action="{{ route('user.sales.destroy', $sale) }}" class="inline" onsubmit="return confirm('¿Eliminar esta venta del historial? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-semibold text-red-300 hover:text-red-200 underline underline-offset-2">Eliminar</button>
                        </form>
                        @endcan
                    </div>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $salesTableColCount }}" class="py-8 px-6 text-center text-white/80">
                    No hay ventas registradas.
                    @if($emptyExtra)
                        {!! $emptyExtra !!}
                    @elseif(! $suppressGlobalCreateLink)
                        @can('sales.create')
                        <a href="{{ route('contacts.index') }}" class="text-[#FFE600] hover:text-white underline ml-1">Generar ficha de inscripción desde Contactos</a>
                        @endcan
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 pt-4 border-t border-white/20">{{ $sales->links() }}</div>
