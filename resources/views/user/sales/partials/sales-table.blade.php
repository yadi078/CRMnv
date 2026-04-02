@php
    $emptyExtra = $emptyExtra ?? null;
    $suppressGlobalCreateLink = $suppressGlobalCreateLink ?? false;
@endphp
<div class="crm-responsive-x">
    <table class="min-w-full border-collapse">
        <thead>
            <tr class="border-b border-[#5b8fc7]/50">
                <th class="text-left py-3 px-6 text-[#FFE600] font-semibold text-sm uppercase tracking-wide">Empresa</th>
                <th class="text-left py-3 px-6 text-[#FFE600] font-semibold text-sm uppercase tracking-wide">Contacto</th>
                <th class="text-left py-3 px-6 text-[#FFE600] font-semibold text-sm uppercase tracking-wide">Curso vendido</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
            <tr class="border-b border-[#5b8fc7]/30 hover:bg-white/5 transition-colors">
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
            </tr>
            @empty
            <tr>
                <td colspan="3" class="py-8 px-6 text-center text-white/80">
                    No hay ventas registradas.
                    @if($emptyExtra)
                        {!! $emptyExtra !!}
                    @elseif(! $suppressGlobalCreateLink)
                        @can('sales.create')
                        <a href="{{ route('contacts.index') }}" class="text-[#FFE600] hover:text-white underline ml-1">Registrar desde Contactos (Nueva venta)</a>
                        @endcan
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 pt-4 border-t border-white/20">{{ $sales->links() }}</div>
