<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">{{ $contact->nombre_completo }}</h2>
            <p class="page-header-card__subtitle">Detalle de contacto</p>
        </div>
        @include('contacts.partials.show-header-actions', ['contact' => $contact, 'sale' => $sale ?? null])
    </x-slot>

    <div class="space-y-8">
        @include('contacts.partials.show-body', ['contact' => $contact, 'contactSales' => $contactSales ?? null])
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
