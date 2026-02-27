<x-guest-layout>
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-4 border-amarillo">
        <div class="flex flex-col lg:flex-row">
            <!-- Left Section - Same as Login -->
            <div class="lg:w-2/5 bg-[#000836] relative px-8 py-12 lg:py-16 flex flex-col justify-start items-center text-center overflow-hidden pt-8 lg:pt-12">
                <!-- Curved bottom-right corner effect -->
                <div class="absolute bottom-0 right-0 w-40 h-40 bg-[#FFFF00] rounded-tl-[100px] opacity-0 lg:opacity-100"></div>
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-white rounded-tl-[80px]"></div>

                <div class="relative z-10 flex flex-col items-center">
                    <div class="mb-6 flex justify-center">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-36 lg:h-44 w-auto object-contain">
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-amarillo mb-2 text-center w-full">INVERTIR EN VALOR</h1>
                    <h2 class="text-2xl lg:text-3xl font-bold text-amarillo mb-4 text-center w-full">¡ATRAE VALOR!</h2>
                </div>
            </div>

            <!-- Right Section - Content -->
            <div class="lg:w-3/5 bg-white px-8 py-12 lg:py-16 flex flex-col">
                <div class="flex flex-col items-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-[#003366] text-center">Registro exitoso</h2>
                </div>
                <p class="text-gray-600 mb-6">
                    Tu cuenta ha sido creada correctamente. Un administrador debe aprobar tu registro antes de que puedas iniciar sesión.
                </p>
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">
                        {{ session('status') }}
                    </div>
                @endif
                <p class="text-sm text-gray-500 mb-8">
                    Te notificaremos cuando tu cuenta sea aprobada. Mientras tanto, puedes intentar iniciar sesión para verificar el estado.
                </p>
                <div class="mt-auto pt-8">
                    <a href="{{ route('login') }}" class="w-full lg:w-auto inline-flex justify-center items-center bg-[#000099] text-white font-bold py-3.5 px-4 rounded-xl hover:bg-[#003366] focus:outline-none focus:ring-2 focus:ring-[#000099] focus:ring-offset-2 transition-all duration-200 uppercase tracking-wider shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        Ir a Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
