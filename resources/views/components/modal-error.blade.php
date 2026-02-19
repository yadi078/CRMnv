@props(['id' => 'modal-error', 'title' => 'No se pudo registrar'])

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
    {{-- Backdrop --}}
    <div id="{{ $id }}-backdrop" class="fixed inset-0 bg-black/50 transition-opacity" aria-hidden="true"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-center">
            <div class="mx-auto flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-600 mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-[#1F2937]">{{ $title }}</h3>
            <p id="{{ $id }}-message" class="mt-1 text-sm text-[#6B7280]">Por favor, intente nuevamente.</p>
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
