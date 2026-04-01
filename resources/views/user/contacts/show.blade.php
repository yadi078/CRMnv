<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">{{ $contact->nombre_completo }}</h2>
            <p class="page-header-card__subtitle">Detalle de contacto</p>
        </div>
        <div class="flex flex-wrap gap-2 ml-auto justify-end items-center">
            <x-contact-reminder-button :contact="$contact" />
            @can('contacts.edit')
            <a href="{{ \App\Support\CrmNavigation::withReturn(route('contacts.edit', $contact)) }}" class="btn-amber-app">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Editar
            </a>
            @endcan
            @can('requestDeletion', $contact)
            <button
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-red-400/80 bg-red-600/90 text-white font-medium hover:bg-red-600"
                onclick="document.getElementById('modal-solicitud-eliminacion-contacto').classList.remove('hidden'); document.body.style.overflow='hidden';"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Solicitar eliminación
            </button>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-8">
        <x-pending-approval-notice :model="$contact" entity-label="contacto" />

        @if($contact->deletion_pending)
        <div class="panel-card-dark p-4 border border-amber-300/40 bg-amber-500/10">
            <h3 class="text-base font-semibold text-[#FFE600]">Solicitud de eliminación en revisión</h3>
            <p class="text-sm text-white/90 mt-1">
                Este contacto ya tiene una solicitud de eliminación pendiente.
                Un administrador debe aprobarla para completar la baja.
            </p>
            @if($contact->deletion_reason)
            <p class="text-sm text-white/80 mt-2"><span class="text-[#FFE600] font-semibold">Motivo:</span> {{ $contact->deletion_reason }}</p>
            @endif
        </div>
        @endif

        @if(($contact->deletion_resolution ?? '') === 'denied'
            && (int) ($contact->deletion_decision_user_id ?? 0) === (int) auth()->id()
            && filled($contact->deletion_resolution_note))
            <x-deletion-denied-alert
                class="mb-6"
                :note="$contact->deletion_resolution_note"
                :resolvedAt="$contact->deletion_resolved_at"
                entity-label="contacto"
            />
        @endif

        <div class="panel-card-dark p-6">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-6 text-xl">Información del Contacto</h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Datos de identidad --}}
                <div class="space-y-4 border border-white/10 rounded-xl p-4 bg-white/5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-400/10 text-amber-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-base text-white/70">Nombre Completo</p>
                            <p class="text-xl font-semibold text-white">{{ $contact->nombre_completo }}</p>
                        </div>
                    </div>

                    @if($contact->genero)
                    <div class="flex items-start justify-between gap-3 border-t border-white/10 pt-3">
                        <div>
                            <p class="text-base text-white/70">Género</p>
                            <p class="text-lg font-medium text-white">{{ $contact->genero }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-start justify-between gap-3 border-t border-white/10 pt-3">
                        <div>
                            <p class="text-base text-white/70">Departamento</p>
                            <p class="text-lg font-medium text-white">{{ $contact->departamento ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Datos de la empresa --}}
                <div class="space-y-4 border border-white/10 rounded-xl p-4 bg-white/5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-400/10 text-amber-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10l9 4 9-4V7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-base text-white/70">Empresa</p>
                            <p class="text-lg font-semibold text-white">
                                @if($contact->company)
                                    <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $contact->company)) }}" class="text-[#FFE600] hover:text-white underline underline-offset-4">
                                        {{ $contact->company->nombre_comercial }}
                                    </a>
                                @else
                                    <span class="text-white/60">-</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start justify-between gap-3 border-t border-white/10 pt-3">
                        <div>
                            <p class="text-base text-white/70">Puesto de Trabajo</p>
                            <p class="text-lg font-medium text-white">{{ $contact->puesto_de_trabajo ?? '-' }}</p>
                        </div>
                    </div>

                    @if($contact->extension || $contact->municipio || $contact->estado)
                    <div class="grid grid-cols-1 gap-3 border-t border-white/10 pt-3">
                        @if($contact->extension)
                        <div>
                            <p class="text-base text-white/70">Extensión</p>
                            <p class="text-lg font-medium text-white">{{ $contact->extension }}</p>
                        </div>
                        @endif
                        @if($contact->municipio || $contact->estado)
                        <div>
                            <p class="text-base text-white/70">Ubicación</p>
                            <p class="text-lg font-medium text-white">
                                {{ $contact->municipio ?? '' }}{{ $contact->municipio && $contact->estado ? ', ' : '' }}{{ $contact->estado ?? '' }}
                            </p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Datos de contacto --}}
                <div class="lg:col-span-2 space-y-4 border border-white/10 rounded-xl p-4 bg-white/5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-400/10 text-emerald-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12V8m0 0l-2 2m2-2l2 2M8 12v4m0 0l2-2m-2 2l-2-2" />
                            </svg>
                        </div>
                        <div class="w-full">
                            <p class="text-base text-white/70">Correo</p>
                            <div class="flex flex-wrap items-center gap-4 mt-1">
                                <p class="text-lg font-medium text-white">
                            {{ $contact->email_activo ? $contact->email : 'Correo desactivado' }}
                                </p>
                                @can('contacts.edit')
                                <form method="POST" action="{{ route('contacts.email-status', $contact) }}" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="email_activo" value="{{ $contact->email_activo ? 0 : 1 }}">
                                    <button type="submit" class="relative inline-flex items-center h-7 w-11 rounded-full transition-colors shadow-sm {{ $contact->email_activo ? 'bg-green-500' : 'bg-gray-300' }}">
                                        <span class="inline-block w-4 h-4 bg-white rounded-full transform transition-transform duration-200 {{ $contact->email_activo ? 'translate-x-4' : '' }}"></span>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-white/10 pt-4 mt-2">
                        <div>
                            <p class="text-base text-white/70">Teléfono</p>
                            <p class="text-lg font-medium text-white">{{ $contact->telefono ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-base text-white/70">Celular</p>
                            <p class="text-lg font-medium text-white">{{ $contact->celular ?? '-' }}</p>
                        </div>
                        @if($contact->fecha_cumpleanos)
                        <div>
                            <p class="text-base text-white/70">Fecha de cumpleaños</p>
                            <p class="text-lg font-medium text-white">{{ $contact->fecha_cumpleanos->format('d/m/Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Notas --}}
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm text-white/70">Notas</p>
                        @can('contacts.edit')
                        <button
                            type="button"
                            onclick="document.getElementById('user-notas-view').classList.add('hidden'); document.getElementById('user-notas-form').classList.remove('hidden');"
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full border border-white/30 bg-white/5 text-white hover:bg-white/15 transition"
                            title="Editar notas"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        @endcan
                    </div>
                    <div id="user-notas-view" class="text-white text-sm {{ $contact->notas ? '' : 'text-white/60 italic' }}">
                        {{ $contact->notas ?: 'Sin notas registradas.' }}
                    </div>
                    @can('contacts.edit')
                    <form
                        id="user-notas-form"
                        method="POST"
                        action="{{ route('contacts.notes', $contact) }}"
                        class="hidden space-y-2"
                    >
                        @csrf
                        @method('PATCH')
                        <textarea
                            name="notas"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm"
                        >{{ old('notas', $contact->notas) }}</textarea>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="btn-amber-app text-xs py-1.5 px-3">Guardar notas</button>
                            <button
                                type="button"
                                class="text-xs text-white/80 hover:text-white"
                                onclick="document.getElementById('user-notas-form').classList.add('hidden'); document.getElementById('user-notas-view').classList.remove('hidden');"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
                    @endcan
                </div>
            </div>
        </div>

    </div>

    @can('requestDeletion', $contact)
    <div id="modal-solicitud-eliminacion-contacto"
         class="{{ $errors->has('motivo') ? '' : 'hidden' }} fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/60"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-eliminacion-contacto-titulo">
        <div class="w-full max-w-md rounded-2xl border-4 border-[#FFE600] bg-[#1a3d6b] shadow-xl p-6 text-center" onclick="event.stopPropagation()">
            <h3 id="modal-eliminacion-contacto-titulo" class="text-lg font-bold text-white mb-2">Solicitar eliminación</h3>
            <p class="text-sm text-white/85 mb-4">
                Un administrador revisará su solicitud en <strong class="text-[#FFE600]">Solicitudes pendientes</strong>. La baja no es inmediata.
            </p>
            <form id="form-solicitud-eliminacion-contacto" method="POST" action="{{ route('contacts.request-deletion', $contact) }}" class="space-y-3 text-left">
                @csrf
                <div>
                    <label for="motivo_eliminacion_modal" class="block text-sm font-medium text-white/90 mb-1">Motivo de la eliminación *</label>
                    <textarea
                        id="motivo_eliminacion_modal"
                        name="motivo"
                        rows="4"
                        maxlength="500"
                        required
                        class="w-full rounded-xl border border-white/20 bg-white/10 text-white placeholder-white/50 focus:ring-2 focus:ring-[#FFE600]/50 focus:border-[#FFE600] px-3 py-2 text-sm"
                        placeholder="Explique por qué solicita la eliminación..."
                    >{{ old('motivo') }}</textarea>
                    @error('motivo')
                        <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col sm:flex-row gap-2 justify-end pt-2">
                    <button
                        type="button"
                        class="px-4 py-2.5 rounded-xl border border-white/40 text-white text-sm font-medium hover:bg-white/10"
                        onclick="document.getElementById('modal-solicitud-eliminacion-contacto').classList.add('hidden'); document.body.style.overflow='';"
                    >
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2.5 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-500 text-sm">
                        Enviar solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Confirmación (mismo diseño que el CRM; sustituye al confirm() del navegador) --}}
    <div id="modal-confirmacion-eliminacion-contacto"
         class="hidden fixed inset-0 z-[210] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-confirmacion-eliminacion-titulo">
        <div class="w-full max-w-md rounded-2xl border-4 border-[#FFE600] bg-[#1a3d6b] shadow-xl p-6 text-center"
             onclick="event.stopPropagation()">
            <div class="mx-auto w-14 h-14 rounded-full bg-[#FFE600]/15 text-[#FFE600] flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 id="modal-confirmacion-eliminacion-titulo" class="text-lg font-bold text-[#FFE600] mb-2">Confirmar envío</h3>
            <p class="text-sm text-white/90 mb-6">
                ¿Enviar la solicitud de eliminación de este contacto? Un administrador deberá aprobarla antes de que se complete la baja.
            </p>
            <div class="flex flex-col-reverse sm:flex-row gap-2 sm:justify-end">
                <button
                    type="button"
                    class="px-4 py-2.5 rounded-xl border border-white/40 text-white text-sm font-medium hover:bg-white/10 w-full sm:w-auto"
                    id="btn-cancelar-confirmacion-eliminacion-contacto"
                >
                    No, volver
                </button>
                <button
                    type="button"
                    class="px-4 py-2.5 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-500 text-sm w-full sm:w-auto"
                    id="btn-confirmar-envio-solicitud-eliminacion"
                >
                    Sí, enviar solicitud
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('modal-solicitud-eliminacion-contacto');
            var form = document.getElementById('form-solicitud-eliminacion-contacto');
            var confirmModal = document.getElementById('modal-confirmacion-eliminacion-contacto');
            if (!modal || !form || !confirmModal) return;

            modal.addEventListener('click', function () {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            });

            function cerrarConfirmacion() {
                confirmModal.classList.add('hidden');
            }

            confirmModal.addEventListener('click', cerrarConfirmacion);

            var btnCancelarConfirm = document.getElementById('btn-cancelar-confirmacion-eliminacion-contacto');
            if (btnCancelarConfirm) btnCancelarConfirm.addEventListener('click', cerrarConfirmacion);

            var btnConfirmarEnvio = document.getElementById('btn-confirmar-envio-solicitud-eliminacion');
            if (btnConfirmarEnvio) {
                btnConfirmarEnvio.addEventListener('click', function () {
                    cerrarConfirmacion();
                    form.submit();
                });
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                confirmModal.classList.remove('hidden');
            });
        })();
    </script>
    @endcan
</x-app-user-layout>
