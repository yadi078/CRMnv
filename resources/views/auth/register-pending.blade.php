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
                <p class="text-sm text-gray-500 mb-4">
                    Cuando un administrador apruebe tu cuenta, serás redirigido automáticamente a tu panel. No necesitas volver a iniciar sesión.
                </p>
                <p id="checking-approval" class="text-sm text-amber-600 mb-8 flex items-center gap-2">
                    <span class="inline-block w-4 h-4 border-2 border-amber-500 border-t-transparent rounded-full animate-spin"></span>
                    Comprobando aprobación…
                </p>
                <div class="mt-auto pt-8 flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="w-full lg:w-auto inline-flex justify-center items-center bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-xl hover:bg-gray-300 transition-all duration-200">
                        Ir a Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var checkUrl = '{{ route("approval.check") }}';
        var interval = 5000;

        function check() {
            fetch(checkUrl, {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.approved && data.url) {
                    var el = document.getElementById('checking-approval');
                    if (el) el.innerHTML = '¡Cuenta aprobada! Entrando a tu panel…';
                    window.location.href = data.url;
                }
            })
            .catch(function() {});
        }

        check();
        setInterval(check, interval);
    })();
    </script>
</x-guest-layout>
