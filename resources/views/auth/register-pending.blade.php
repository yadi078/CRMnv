<x-guest-layout>
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden max-w-2xl mx-auto">
        <div class="flex flex-col">
            <div class="bg-[#000836] relative px-8 py-12 flex flex-col justify-center items-center text-center overflow-hidden">
                <div class="absolute bottom-0 right-0 w-40 h-40 bg-[#FFFF00] rounded-tl-[100px] opacity-20"></div>
                <div class="relative z-10">
                    <div class="mb-6 flex justify-center">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-28 w-auto object-contain">
                    </div>
                    <h1 class="text-2xl font-bold text-amarillo mb-2">INVERTIR EN VALOR</h1>
                    <h2 class="text-2xl font-bold text-amarillo mb-4">¡ATRAE VALOR!</h2>
                </div>
            </div>

            <div class="px-8 py-12 lg:py-16">
                <div class="flex justify-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center">
                        <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-[#003366] mb-4 text-center">Registro exitoso</h2>
                <p class="text-gray-600 text-center mb-6">
                    Tu cuenta ha sido creada correctamente. Un administrador debe aprobar tu registro antes de que puedas iniciar sesión.
                </p>
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">
                        {{ session('status') }}
                    </div>
                @endif
                <p class="text-sm text-gray-500 text-center mb-8">
                    Te notificaremos cuando tu cuenta sea aprobada. Mientras tanto, puedes intentar iniciar sesión para verificar el estado.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('login') }}" class="inline-flex justify-center items-center bg-[#000099] text-white font-bold py-3 px-6 rounded-xl hover:bg-[#003366] transition-all duration-200">
                        Ir a Iniciar Sesión
                    </a>
                    <a href="{{ url('/') }}" class="inline-flex justify-center items-center border-2 border-[#003366] text-[#003366] font-semibold py-3 px-6 rounded-xl hover:bg-[#003366] hover:text-white transition-all duration-200">
                        Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
