@props(['id' => 'modal-registro-exitoso'])

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
    {{-- Backdrop --}}
    <div id="{{ $id }}-backdrop" class="fixed inset-0 bg-black/50 transition-opacity" aria-hidden="true"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-center">
            <div class="mx-auto flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-600 mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-[#1F2937]">Registro exitoso</h3>
            <p class="mt-1 text-sm text-[#6B7280]">El registro se ha guardado correctamente.</p>
            <div class="mt-6 flex justify-center gap-3">
                <button type="button" id="{{ $id }}-accept" class="btn-primary-app">
                    Aceptar
                </button>
                <button type="button" id="{{ $id }}-close" class="px-4 py-2 rounded-xl border border-[#E2E8F0] text-[#1F2937] hover:bg-gray-50">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
