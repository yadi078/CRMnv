@props(['id' => 'modal-error', 'title' => 'No se pudo registrar'])

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
    <div id="{{ $id }}-backdrop" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" aria-hidden="true"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-[#1a3d6b] rounded-2xl shadow-xl max-w-md w-full p-8 text-center border-4 border-[#FFE600]">
            <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-red-500 text-white mb-6">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">{{ $title }}</h3>
            <p id="{{ $id }}-message" class="text-white/90 text-sm mb-6">Por favor, intente nuevamente.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button type="button" id="{{ $id }}-accept" class="w-full sm:w-auto py-3 px-6 rounded-xl font-semibold text-gray-900 bg-[#FFE600] hover:bg-[#E6CF00] focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-[#1a3d6b] transition-colors">
                    Aceptar
                </button>
                <button type="button" id="{{ $id }}-close" class="w-full sm:w-auto py-3 px-6 rounded-xl font-semibold text-white/90 border-2 border-white/40 hover:bg-white/10 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
