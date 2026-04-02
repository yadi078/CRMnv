@props(['model', 'entityLabel' => 'registro'])

{{-- Solo mientras approval_status === 'pendiente'; al aprobar, el modelo pasa a aprobado y este bloque no se renderiza. --}}
@if($model->estaPendiente())
<div {{ $attributes->merge(['class' => 'rounded-xl border-2 border-[#FFE600] bg-[#071A3D] px-4 py-3.5 text-sm shadow-lg shadow-black/15 mb-6']) }}>
    <p class="font-semibold text-[#FFE600] mb-1.5 tracking-tight">Pendiente de aprobación</p>
    <p class="text-white/95 leading-relaxed">
        Este {{ $entityLabel }} solo será visible para el resto del equipo cuando un administrador lo apruebe. Mientras tanto solo usted (y la administración en solicitudes) lo verá en el flujo habitual.
    </p>
</div>
@endif
