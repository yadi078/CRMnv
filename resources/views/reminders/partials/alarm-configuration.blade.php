@props([
    'reminder' => null,
])

@php
    $enabled = (bool) old('alarm_repeat_enabled', $reminder?->alarm_repeat_enabled);
    $intervalDb = $reminder?->alarm_repeat_interval_minutes;
    $intervalOld = old('alarm_repeat_interval_minutes');
    $interval = $intervalOld !== null && $intervalOld !== '' ? (int) $intervalOld : $intervalDb;
    $preset = old('alarm_interval_preset');
    if ($preset === null) {
        if ($interval && in_array((int) $interval, [5, 10, 15], true)) {
            $preset = (string) (int) $interval;
        } elseif ($interval) {
            $preset = 'custom';
        } else {
            $preset = '10';
        }
    }
    $type = old('alarm_repeat_type', $reminder?->alarm_repeat_type ?? \App\Models\Reminder::ALARM_REPEAT_UNTIL_CONFIRMED);
    $value = old('alarm_repeat_value', $reminder?->alarm_repeat_value);
@endphp

<div
    class="pt-4 border-t border-white/15"
    x-data="{ alarmOn: {{ $enabled ? 'true' : 'false' }} }"
>
    <p class="text-xs font-semibold uppercase tracking-wider text-[#FFE600]/90 mb-3">Configuración de alarma</p>

    <input type="hidden" name="alarm_repeat_enabled" value="0">
    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
        <input
            type="checkbox"
            name="alarm_repeat_enabled"
            value="1"
            class="rounded border-white/30 text-[#FFE600] bg-white focus:ring-[#FFE600] w-5 h-5 shrink-0"
            x-model="alarmOn"
            @checked($enabled)
        >
        <span class="text-sm font-medium text-white/90">Repetir alarma hasta confirmar o según reglas abajo</span>
    </label>
    <p class="mt-1.5 text-xs text-white/60 ml-8 max-w-xl">
        Si está activo, recibirás avisos cada cierto tiempo después de la hora programada. <strong class="text-white/80">Confirmar</strong> en el modal detiene las repeticiones. <strong class="text-white/80">Aplazar</strong> reinicia el ciclo respecto a la nueva hora.
    </p>

    <div
        x-show="alarmOn"
        x-cloak
        class="mt-4 space-y-4 rounded-xl border border-[#FFE600]/35 bg-[#071A3D]/40 p-4"
    >
        <div>
            <p class="text-xs font-semibold text-[#FFE600]/90 mb-2">Intervalo entre avisos</p>
            <div class="flex flex-wrap gap-3 text-sm text-white/90">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="alarm_interval_preset" value="5" class="text-[#FFE600] focus:ring-[#FFE600]" @checked($preset === '5')>
                    Cada 5 min
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="alarm_interval_preset" value="10" class="text-[#FFE600] focus:ring-[#FFE600]" @checked($preset === '10')>
                    Cada 10 min
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="alarm_interval_preset" value="15" class="text-[#FFE600] focus:ring-[#FFE600]" @checked($preset === '15')>
                    Cada 15 min
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="alarm_interval_preset" value="custom" class="text-[#FFE600] focus:ring-[#FFE600]" @checked($preset === 'custom')>
                    Personalizado
                </label>
            </div>
            <div class="mt-2 max-w-xs">
                <label class="block text-xs text-white/70 mb-1">Minutos (si personalizado)</label>
                <input
                    type="number"
                    name="alarm_interval_custom"
                    min="1"
                    max="10080"
                    placeholder="Ej. 20"
                    value="{{ old('alarm_interval_custom', ($preset === 'custom' && $interval) ? $interval : '') }}"
                    style="color: #111827;"
                    class="w-full rounded-xl border border-gray-300 bg-white py-2 px-3 text-sm"
                >
                @error('alarm_interval_custom')
                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold text-[#FFE600]/90 mb-2">Tipo de duración de la repetición</p>
            <select
                name="alarm_repeat_type"
                style="color: #111827;"
                class="w-full max-w-md rounded-xl border border-gray-300 bg-white py-2.5 px-3 text-sm focus:ring-2 focus:ring-[#FFE600]/60"
            >
                <option value="{{ \App\Models\Reminder::ALARM_REPEAT_UNTIL_CONFIRMED }}" @selected($type === \App\Models\Reminder::ALARM_REPEAT_UNTIL_CONFIRMED)>
                    Sonar hasta que confirmes el recordatorio
                </option>
                <option value="{{ \App\Models\Reminder::ALARM_REPEAT_TIMES }}" @selected($type === \App\Models\Reminder::ALARM_REPEAT_TIMES)>
                    Repetir un número fijo de veces (después del aviso inicial)
                </option>
                <option value="{{ \App\Models\Reminder::ALARM_REPEAT_DURATION }}" @selected($type === \App\Models\Reminder::ALARM_REPEAT_DURATION)>
                    Sonar durante X minutos desde la hora programada
                </option>
            </select>
            @error('alarm_repeat_type')
                <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="max-w-xs">
            <label class="block text-xs text-white/70 mb-1">
                Valor numérico (veces o minutos, según el tipo elegido)
            </label>
            <input
                type="number"
                name="alarm_repeat_value"
                min="1"
                max="525600"
                placeholder="Solo si aplica al tipo"
                value="{{ old('alarm_repeat_value', $value) }}"
                style="color: #111827;"
                class="w-full rounded-xl border border-gray-300 bg-white py-2 px-3 text-sm"
            >
            @error('alarm_repeat_value')
                <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
