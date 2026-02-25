@props(['id' => 'modal-registro-exitoso'])

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
    <div id="{{ $id }}-backdrop" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" aria-hidden="true"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-[#1a3d6b] rounded-2xl shadow-xl max-w-md w-full p-8 text-center border-4 border-[#FFE600]">
            <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500 text-white mb-6">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Registro exitoso</h3>
            <p class="text-white/90 text-sm mb-6">El registro se ha guardado correctamente.</p>
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
