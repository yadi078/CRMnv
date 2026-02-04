<x-guest-layout>
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="flex flex-col lg:flex-row">
            <!-- Left Section - Welcome/Login -->
            <div class="lg:w-2/5 bg-[#000836] relative px-8 py-12 lg:py-16 flex flex-col justify-center items-center text-center lg:text-left lg:items-start overflow-hidden">
                <!-- Curved bottom-right corner effect -->
                <div class="absolute bottom-0 right-0 w-40 h-40 bg-[#FFFF00] rounded-tl-[100px] opacity-0 lg:opacity-100"></div>
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-white rounded-tl-[80px]"></div>
                
                <div class="relative z-10">
                    <div class="mb-6 flex justify-center">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-28 lg:h-32 w-auto object-contain">
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-amarillo mb-2 text-center lg:text-left">INVERTIR EN VALOR</h1>
                    <h2 class="text-2xl lg:text-3xl font-bold text-amarillo mb-4 text-center lg:text-left">¡ATRAE VALOR!</h2>
                    <p class="text-lg text-white mb-8">¿Ya tienes una Cuenta?</p>
                    <a href="{{ route('login') }}" class="inline-block border-2 border-white text-white px-8 py-3 rounded-xl hover:bg-white hover:text-[#000836] transition-all duration-200 font-semibold">
                        Iniciar Sesión
                    </a>
                </div>
            </div>

            <!-- Right Section - Registration Form -->
            <div class="lg:w-3/5 bg-white px-8 py-12 lg:py-16">
                <h2 class="text-3xl font-bold text-[#003366] mb-8">Crear Cuenta</h2>

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-[#808080] mb-2">
                            Nombre
                        </label>
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#808080]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <input 
                                id="name" 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}" 
                                required 
                                autofocus 
                                autocomplete="name"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FFFF00] focus:border-[#FFFF00] text-gray-900 placeholder:text-[#808080] transition-all duration-200"
                                placeholder="Ingresa tu nombre"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[#808080] mb-2">
                            Correo Electrónico
                        </label>
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#808080]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autocomplete="username"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FFFF00] focus:border-[#FFFF00] text-gray-900 placeholder:text-[#808080] transition-all duration-200"
                                placeholder="Ingresa tu correo electrónico"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-[#808080] mb-2">
                            Contraseña
                        </label>
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#808080]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="new-password"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FFFF00] focus:border-[#FFFF00] text-gray-900 placeholder:text-[#808080] transition-all duration-200"
                                placeholder="Ingresa tu contraseña"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-[#808080] mb-2">
                            Confirmar Contraseña
                        </label>
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#808080]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <input 
                                id="password_confirmation" 
                                type="password" 
                                name="password_confirmation" 
                                required 
                                autocomplete="new-password"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FFFF00] focus:border-[#FFFF00] text-gray-900 placeholder:text-[#808080] transition-all duration-200"
                                placeholder="Confirma tu contraseña"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm" />
                    </div>

                    <!-- Register Button -->
                    <div class="pt-4">
                        <button 
                            type="submit"
                            class="w-full bg-[#000099] text-white font-bold py-3.5 px-4 rounded-xl hover:bg-[#003366] focus:outline-none focus:ring-2 focus:ring-[#000099] focus:ring-offset-2 transition-all duration-200 uppercase tracking-wider shadow-md hover:shadow-lg"
                        >
                            Registrarse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
