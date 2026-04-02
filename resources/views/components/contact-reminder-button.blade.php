@props(['contact'])

@php
    $empresaNombre = $contact->company?->nombre_comercial ?? '';
    $emailPrimario = collect(preg_split('/\s*,\s*/', (string) ($contact->email ?? ''), -1, PREG_SPLIT_NO_EMPTY))
        ->map(fn ($e) => trim($e))
        ->filter()
        ->first() ?? '';
    $telefonoReminder = $contact->telefono ?: $contact->celular ?: '';
@endphp

<div {{ $attributes->class(['inline-flex']) }} x-data="{ showReminderModal: {{ old('reminder_context') ? 'true' : 'false' }} }">
    <button
        type="button"
        @click="showReminderModal = true"
        class="inline-flex items-center gap-2 rounded-xl border-2 border-[#FFE600] bg-[#0b386a] text-[#FFE600] px-4 py-2 text-sm font-semibold shadow-sm hover:bg-[#082f59] transition-colors"
    >
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Recordatorios
    </button>

    <div
        x-show="showReminderModal"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center px-4 bg-[#000836]/90"
        x-transition
        @keydown.escape.window="showReminderModal = false"
    >
        <div
            class="w-full max-w-2xl min-w-[min(100%,340px)] max-h-[90vh] bg-[#0b386a] rounded-2xl shadow-2xl ring-2 ring-[#FFE600]/70 ring-offset-2 ring-offset-[#000836] overflow-hidden flex flex-col"
            @click.outside="showReminderModal = false"
        >
            <div class="px-5 py-3 bg-[#FFE600] flex items-center justify-between text-[#003366] rounded-t-2xl">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-[#003366] text-[#FFE600] shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="font-semibold text-sm truncate">Nuevo recordatorio</h3>
                        <p class="text-xs opacity-90 truncate">{{ $contact->nombre_completo }}</p>
                    </div>
                </div>
                <button type="button" @click="showReminderModal = false" class="p-1 rounded-lg text-[#003366] hover:bg-[#003366]/10 hover:text-black transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('reminders.store') }}" class="px-5 py-4 space-y-4 text-white overflow-y-auto flex-1">
                @csrf
                <input type="hidden" name="reminder_context" value="1">

                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1.5">Título del recordatorio</label>
                    <input type="text" name="title" required maxlength="255" value="{{ old('title') }}" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white placeholder-gray-400 focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm" placeholder="Ej. Llamar a {{ Str::limit($contact->nombre_completo, 40) }}">
                    @error('title')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-white/15">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FFE600]/90 mb-3">Datos del contacto (puedes editarlos)</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-white/90 mb-1">Nombre del cliente</label>
                            <input type="text" name="nombre_cliente" maxlength="255" value="{{ old('nombre_cliente', $contact->nombre_completo) }}" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white/90 mb-1">Empresa</label>
                            <input type="text" name="empresa" maxlength="255" value="{{ old('empresa', $empresaNombre) }}" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white/90 mb-1">Correo electrónico</label>
                            <input type="email" name="correo_electronico" maxlength="255" value="{{ old('correo_electronico', $emailPrimario) }}" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white/90 mb-1">Número telefónico</label>
                            <input type="text" name="numero_telefonico" maxlength="50" value="{{ old('numero_telefonico', $telefonoReminder) }}" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white/90 mb-1">Extensión</label>
                            <input type="text" name="extension" maxlength="20" value="{{ old('extension', $contact->extension) }}" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white/90 mb-1">Área</label>
                            <input type="text" name="area" maxlength="255" value="{{ old('area', $contact->departamento) }}" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-white/90 mb-1">Puesto de trabajo</label>
                            <input type="text" name="puesto_trabajo" maxlength="255" value="{{ old('puesto_trabajo', $contact->puesto_de_trabajo) }}" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-white/15">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#FFE600]/90 mb-3">Seguimiento</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-white/90 mb-1">Tipo de Acción *</label>
                                <select name="tipo_accion" required class="w-full rounded-xl border-2 border-[#FFE600]/90 bg-[#0b386a] text-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm [&>option]:bg-white [&>option]:text-gray-900">
                                    <option value="">Seleccione un tipo</option>
                                    @foreach(\App\Models\Reminder::TIPO_ACCION_OPCIONES as $val => $etiqueta)
                                        <option value="{{ $val }}" @selected(old('tipo_accion') === $val)>{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_accion')
                                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-white/90 mb-1">Bitácora de Notas</label>
                                <textarea name="description" rows="6" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white placeholder-gray-400 focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm" placeholder="Ingrese las notas y observaciones...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-white/90 mb-1">Fecha</label>
                                <input type="date" name="date" value="{{ old('date') }}" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-3 px-4 text-sm min-h-[44px]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-white/90 mb-1">Hora</label>
                                <input type="time" name="time" value="{{ old('time') }}" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-3 px-4 text-sm min-h-[44px]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-white/90 mb-1">Repetir</label>
                                <select name="repeat" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-2.5 px-3 text-sm">
                                    <option value="">No repetir</option>
                                    <option value="daily" @selected(old('repeat') === 'daily')>Diario</option>
                                    <option value="weekly" @selected(old('repeat') === 'weekly')>Semanal</option>
                                    <option value="monthly" @selected(old('repeat') === 'monthly')>Mensual</option>
                                </select>
                                <label class="inline-flex items-center gap-2 text-xs text-white/80 mt-2">
                                    <input type="checkbox" name="all_day" value="1" @checked(old('all_day')) class="rounded border-white/30 text-[#FFE600] bg-white focus:ring-[#FFE600]">
                                    Recordatorio todo el día
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-white/90 mb-1">Fecha límite</label>
                                <input type="date" name="deadline_date" value="{{ old('deadline_date') }}" style="color: #111827;" class="w-full rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-[#FFE600]/60 py-3 px-4 text-sm min-h-[44px]">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-4 border-t border-white/15">
                    <button type="button" @click="showReminderModal = false" class="px-5 py-2.5 text-sm rounded-xl border border-white/30 text-white/90 hover:bg-white/10 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm rounded-xl bg-[#FFE600] text-[#003366] font-semibold hover:bg-[#e6cf00] transition-colors">
                        Guardar recordatorio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
