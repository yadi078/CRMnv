@props(['model', 'entityLabel' => 'registro'])

@if(($model->approval_status ?? '') === 'pendiente')
<div {{ $attributes->merge(['class' => 'rounded-xl border-2 border-amber-400/50 bg-amber-500/10 px-4 py-3 text-sm text-white/95 mb-6']) }}>
    <p class="font-semibold text-[#FFE600] mb-1">Pendiente de aprobación</p>
    <p class="text-white/90">
        Este {{ $entityLabel }} solo será visible para el resto del equipo cuando un administrador lo acepte. Mientras tanto solo usted (y la administración en solicitudes) lo verá en el flujo habitual.
    </p>
</div>
@endif
