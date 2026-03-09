<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Nueva Empresa</h2>
            <p class="page-header-card__subtitle">Registrar nueva empresa</p>
        </div>
    </x-slot>

    <div class="pt-1 pb-4 sm:pt-1 sm:pb-6" x-data="companyCreateForm()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="panel-card-dark">
                <form method="POST" action="{{ route('companies.store') }}" id="form-nueva-empresa" @submit.prevent="submitForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nombre_comercial" value="Nombre Comercial *" class="text-white" />
                            <x-text-input
                                id="nombre_comercial"
                                name="nombre_comercial"
                                type="text"
                                class="mt-1 block w-full bg-white text-[#1F2937] border-[#E2E8F0] focus:border-[#FFE600] focus:ring-4 focus:ring-[#FFE600]/40"
                                :value="old('nombre_comercial')"
                                required
                            />
                            <x-input-error :messages="$errors->get('nombre_comercial')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="rfc" value="RFC" class="text-white" />
                            <x-text-input
                                id="rfc"
                                name="rfc"
                                type="text"
                                class="mt-1 block w-full uppercase"
                                :value="old('rfc')"
                                maxlength="13"
                            />
                            <x-input-error :messages="$errors->get('rfc')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="sector" value="Sector/Giro *" class="text-white" />
                            <x-text-input
                                id="sector"
                                name="sector"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('sector')"
                                required
                            />
                            <x-input-error :messages="$errors->get('sector')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="municipio" value="Municipio" class="text-white" />
                            <x-text-input
                                id="municipio"
                                name="municipio"
                                type="text"
                                class="mt-1 block w-full bg-white text-[#1F2937] border-[#E2E8F0] focus:border-[#FFE600] focus:ring-4 focus:ring-[#FFE600]/40"
                                :value="old('municipio')"
                            />
                            <x-input-error :messages="$errors->get('municipio')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="estado" value="Estado" class="text-white" />
                            <x-text-input
                                id="estado"
                                name="estado"
                                type="text"
                                class="mt-1 block w-full bg-white text-[#1F2937] border-[#E2E8F0] focus:border-[#FFE600] focus:ring-4 focus:ring-[#FFE600]/40"
                                :value="old('estado')"
                            />
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ejecutivo_asignado" value="Ejecutivo Asignado" class="text-white" />
                            <x-text-input
                                id="ejecutivo_asignado"
                                name="ejecutivo_asignado"
                                type="text"
                                class="mt-1 block w-full bg-white text-[#1F2937] border-[#E2E8F0] focus:border-[#FFE600] focus:ring-4 focus:ring-[#FFE600]/40"
                                :value="old('ejecutivo_asignado')"
                            />
                            <x-input-error :messages="$errors->get('ejecutivo_asignado')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="datos_fiscales" value="Datos Fiscales" class="text-white" />
                            <textarea
                                id="datos_fiscales"
                                name="datos_fiscales"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-[#E2E8F0] bg-white text-[#1F2937] focus:border-[#FFE600] focus:ring-4 focus:ring-[#FFE600]/40"
                            >{{ old('datos_fiscales') }}</textarea>
                            <x-input-error :messages="$errors->get('datos_fiscales')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ route('companies.index') }}" class="btn-danger-app">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancelar
                        </a>
                        <button type="submit" class="btn-amber-app" :disabled="sending">
                            <span x-show="!sending">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Aceptar
                            </span>
                            <span x-show="sending" x-cloak>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal de error (flotante) --}}
        <div x-show="showErrorModal"
             x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
             @keydown.escape.window="closeErrorModal()"
             role="alertdialog"
             aria-modal="true"
             aria-labelledby="error-modal-title">
            <div x-show="showErrorModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="w-full max-w-md bg-[#1a3d6b] rounded-2xl shadow-xl p-8 text-center border-4 border-[#FFE600]"
                 @click.outside="closeErrorModal()">
                <div class="mx-auto w-16 h-16 rounded-full bg-red-500 flex items-center justify-center shadow-lg mb-6">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 id="error-modal-title" class="text-xl font-bold text-white mb-2">Error al registrar</h3>
                <p class="text-white/90 text-sm mb-6" x-html="errorMessage"></p>
                <button type="button"
                        @click="closeErrorModal()"
                        class="w-full py-3 px-4 rounded-xl font-semibold text-gray-900 bg-[#FFE600] hover:bg-[#E6CF00] focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-[#1a3d6b] transition-colors">
                    Aceptar
                </button>
            </div>
        </div>

        {{-- Modal de éxito --}}
        <div x-show="showSuccessModal"
             x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
             @keydown.escape.window="closeSuccessModal()"
             role="dialog"
             aria-modal="true"
             aria-labelledby="modal-title">
            <div x-show="showSuccessModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="w-full max-w-md bg-[#1a3d6b] rounded-2xl shadow-xl p-8 text-center border-4 border-[#FFE600]"
                 @click.outside="closeSuccessModal()">
                <div class="mx-auto w-16 h-16 rounded-full bg-emerald-500 flex items-center justify-center shadow-lg mb-6">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 id="modal-title" class="text-xl font-bold text-white mb-2">Registro exitoso</h3>
                <p class="text-white/90 text-sm mb-6" x-text="successMessage">La empresa se ha registrado correctamente.</p>
                <button type="button"
                        @click="closeSuccessModal()"
                        class="w-full py-3 px-4 rounded-xl font-semibold text-gray-900 bg-[#FFE600] hover:bg-[#E6CF00] focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-[#1a3d6b] transition-colors">
                    Aceptar
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function companyCreateForm() {
            return {
                showSuccessModal: false,
                showErrorModal: false,
                sending: false,
                successMessage: 'La empresa se ha registrado correctamente.',
                errorMessage: '',

                submitForm() {
                    this.sending = true;
                    this.showErrorModal = false;
                    const form = document.getElementById('form-nueva-empresa');
                    const formData = new FormData(form);
                    const url = form.getAttribute('action');
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    })
                    .then(res => {
                        return res.json().then(data => ({ ok: res.ok, status: res.status, data }));
                    })
                    .then(({ ok, data }) => {
                        this.sending = false;
                        if (ok && data.success) {
                            this.successMessage = data.message || 'La empresa se ha registrado correctamente.';
                            this.showSuccessModal = true;
                        } else {
                            const err = data.errors || (data.message ? [data.message] : ['Error al procesar el formulario.']);
                            this.showFormErrors(Array.isArray(err) ? err : (typeof err === 'object' ? Object.values(err).flat() : [err]));
                        }
                    })
                    .catch(err => {
                        this.sending = false;
                        this.showFormErrors(['Error de conexión. Intente de nuevo.']);
                    });
                },

                showFormErrors(errors) {
                    const list = Array.isArray(errors) ? errors : (typeof errors === 'object' ? Object.values(errors).flat() : [errors]);
                    this.errorMessage = list.join('<br>');
                    this.showErrorModal = true;
                },

                closeErrorModal() {
                    this.showErrorModal = false;
                },

                closeSuccessModal() {
                    this.showSuccessModal = false;
                    document.getElementById('form-nueva-empresa').reset();
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
