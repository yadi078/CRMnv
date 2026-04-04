@php
    $tz = config('app.timezone');
    $start = $reminder->start_at ?? $reminder->scheduled_for;
    $startLocal = $start ? $start->copy()->timezone($tz) : null;
    $startAtOld = old('start_at');
    $startAtValue = $startAtOld !== null && $startAtOld !== ''
        ? $startAtOld
        : ($startLocal ? $startLocal->format('Y-m-d\TH:i') : '');
@endphp

<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Reprogramar recordatorio</h2>
            <p class="page-header-card__subtitle">Cambia fecha, hora o datos del recordatorio</p>
        </div>
    </x-slot>

    <div class="panel-card-dark p-5 sm:p-8 max-w-3xl mx-auto">
        <form method="POST" action="{{ route('reminders.update', $reminder) }}" class="space-y-6 text-white">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1.5">Título del recordatorio</label>
                <input type="text" name="title" required maxlength="255" value="{{ old('title', $reminder->title) }}"
                    class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-2.5 px-3 text-sm focus:ring-2 focus:ring-[#FFE600]/60" />
                @error('title')
                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-white/15">
                <p class="text-xs font-semibold uppercase tracking-wider text-[#FFE600]/90 mb-3">Datos del contacto</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-white/90 mb-1">Nombre del cliente</label>
                        <input type="text" name="nombre_cliente" maxlength="255" value="{{ old('nombre_cliente', $reminder->nombre_cliente) }}"
                            class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-2.5 px-3 text-sm focus:ring-2 focus:ring-[#FFE600]/60" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Empresa</label>
                        <input type="text" name="empresa" maxlength="255" value="{{ old('empresa', $reminder->empresa) }}"
                            class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-2.5 px-3 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Correo electrónico</label>
                        <input type="email" name="correo_electronico" maxlength="255" value="{{ old('correo_electronico', $reminder->correo_electronico) }}"
                            class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-2.5 px-3 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Número telefónico</label>
                        <input type="text" name="numero_telefonico" maxlength="50" value="{{ old('numero_telefonico', $reminder->numero_telefonico) }}"
                            class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-2.5 px-3 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Extensión</label>
                        <input type="text" name="extension" maxlength="20" value="{{ old('extension', $reminder->extension) }}"
                            class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-2.5 px-3 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1">Área</label>
                        <input type="text" name="area" maxlength="255" value="{{ old('area', $reminder->area) }}"
                            class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-2.5 px-3 text-sm" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-white/90 mb-1">Puesto de trabajo</label>
                        <input type="text" name="puesto_trabajo" maxlength="255" value="{{ old('puesto_trabajo', $reminder->puesto_trabajo) }}"
                            class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-2.5 px-3 text-sm" />
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-white/15 space-y-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-[#FFE600]/90">Seguimiento</p>
                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1">Tipo de acción *</label>
                    <select name="tipo_accion" required class="w-full rounded-xl border-2 border-[#FFE600]/90 bg-[#0b386a] text-white py-2.5 px-3 text-sm [&>option]:bg-white [&>option]:text-gray-900">
                        @foreach (\App\Models\Reminder::TIPO_ACCION_OPCIONES as $val => $etiqueta)
                            <option value="{{ $val }}" @selected(old('tipo_accion', $reminder->tipo_accion) === $val)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                    @error('tipo_accion')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1">Bitácora de notas</label>
                    <textarea name="description" rows="5" class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-2.5 px-3 text-sm">{{ old('description', $reminder->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1">Fecha y hora programada</label>
                    <input type="datetime-local" name="start_at" value="{{ $startAtValue }}"
                        class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-3 px-4 text-sm min-h-[44px]" />
                    @error('start_at')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-white/85">
                    <input type="checkbox" name="all_day" value="1" @checked(old('all_day', $reminder->all_day)) class="rounded border-white/30 text-[#FFE600] bg-white focus:ring-[#FFE600]" />
                    Recordatorio todo el día
                </label>
                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1">Repetir</label>
                    <select name="repeat" class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-2.5 px-3 text-sm">
                        <option value="">No repetir</option>
                        <option value="daily" @selected(old('repeat', $reminder->repeat) === 'daily')>Diario</option>
                        <option value="weekly" @selected(old('repeat', $reminder->repeat) === 'weekly')>Semanal</option>
                        <option value="monthly" @selected(old('repeat', $reminder->repeat) === 'monthly')>Mensual</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1">Fecha límite</label>
                    <input type="date" name="deadline_at" value="{{ old('deadline_at', $reminder->deadline_at?->format('Y-m-d')) }}"
                        class="w-full rounded-xl border border-gray-300 bg-white text-gray-900 py-3 px-4 text-sm min-h-[44px]" />
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3 pt-4 border-t border-white/15">
                <a href="{{ url()->previous() === url()->current() ? route('notifications.index') : url()->previous() }}" class="px-5 py-2.5 text-sm rounded-xl border border-white/30 text-white/90 hover:bg-white/10">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 text-sm rounded-xl bg-[#FFE600] text-[#003366] font-semibold hover:bg-[#e6cf00]">Guardar cambios</button>
            </div>
        </form>
    </div>
</x-app-layout>
