{{--
  $d: array datos de la notificación (ya enriquecido con reminder_detalle si aplica)
  $mensaje: texto resumen (título — descripción)
--}}
@php
    $det = isset($d['reminder_detalle']) && is_array($d['reminder_detalle']) ? $d['reminder_detalle'] : [];
    $labels = [
        'titulo' => 'Título del recordatorio',
        'descripcion' => 'Detalles',
        'nombre_cliente' => 'Cliente',
        'empresa' => 'Empresa',
        'correo_electronico' => 'Correo electrónico',
        'numero_telefonico' => 'Teléfono',
        'extension' => 'Extensión',
        'area' => 'Área',
        'puesto_trabajo' => 'Puesto de trabajo',
        'fecha_inicio' => 'Fecha y hora',
        'fecha_limite' => 'Fecha límite',
        'repeticion' => 'Repetición',
    ];
    $hasDetalle = false;
    foreach ($labels as $key => $_) {
        if (! empty($det[$key])) {
            $hasDetalle = true;
            break;
        }
    }
@endphp
@if($hasDetalle)
    <dl class="text-left space-y-3 text-sm max-w-lg mx-auto text-white">
        @foreach($labels as $key => $label)
            @if(!empty($det[$key]))
                <div>
                    <dt class="text-[#FFE600] font-semibold text-xs uppercase tracking-wide">{{ $label }}</dt>
                    {{-- !text-white fuerza legibilidad sobre el color del body (#1F2937) --}}
                    <dd class="!text-white mt-0.5 whitespace-pre-wrap break-words font-medium">{{ $det[$key] }}</dd>
                </div>
            @endif
        @endforeach
    </dl>
@else
    <p class="!text-white whitespace-pre-wrap break-words">{{ $mensaje ?: 'Sin contenido adicional.' }}</p>
    @if(!empty($d['fecha_prevista']))
        <p class="!text-white/80 text-sm mt-3"><span class="text-[#FFE600] font-medium">Fecha prevista:</span> {{ $d['fecha_prevista'] }}</p>
    @endif
@endif
