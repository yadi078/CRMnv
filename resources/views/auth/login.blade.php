<x-guest-layout>
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="flex flex-col lg:flex-row">
            <!-- Left Section - Welcome/Registration -->
            <div class="lg:w-2/5 bg-[#000836] relative px-8 py-12 lg:py-16 flex flex-col justify-center items-center text-center lg:text-left lg:items-start overflow-hidden">
                <!-- Curved bottom-right corner effect -->
                <div class="absolute bottom-0 right-0 w-40 h-40 bg-[#FFFF00] rounded-tl-[100px] opacity-0 lg:opacity-100"></div>
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-white rounded-tl-[80px]"></div>
                
                <div class="relative z-10">
                    <div class="mb-6 flex justify-center">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-36 lg:h-44 w-auto object-contain">
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-amarillo mb-2 text-center w-full">INVERTIR EN VALOR</h1>
                    <h2 class="text-2xl lg:text-3xl font-bold text-amarillo mb-4 text-center w-full">¡ATRAE VALOR!</h2>
                    <p class="text-lg text-white mb-8">¿No tienes una Cuenta?</p>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-block border-2 border-white text-white px-8 py-3 rounded-xl hover:bg-white hover:text-[#000836] transition-all duration-200 font-semibold inline-flex items-center">
                            <svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                            </svg>
                            Registrarse
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right Section - Login Form -->
            <div class="lg:w-3/5 bg-white px-8 py-12 lg:py-16">
                <h2 class="text-3xl font-bold text-[#003366] mb-8">Iniciar Sesión</h2>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[#808080] mb-2">
                            Usuario
                        </label>
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#808080]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                autocomplete="username"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FFFF00] focus:border-[#FFFF00] text-gray-900 placeholder:text-[#808080] transition-all duration-200"
                                placeholder="Ingresa tu usuario"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm" />
                    </div>

                    <!-- Password -->
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block text-sm font-medium text-[#808080] mb-2">
                            Contraseña
                        </label>
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[#808080]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <input 
                                id="password" 
                                :type="showPassword ? 'text' : 'password'" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                class="w-full pl-12 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#FFFF00] focus:border-[#FFFF00] text-gray-900 placeholder:text-[#808080] transition-all duration-200"
                                placeholder="Ingresa tu contraseña"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-[#808080] hover:text-[#003366] hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-[#FFFF00] focus:ring-offset-1"
                                :aria-label="showPassword ? 'Ocultar contraseña' : 'Ver contraseña'"
                                tabindex="-1"
                            >
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
                    </div>

                    <!-- Login Button -->
                    <div class="pt-4">
                        <button 
                            type="submit"
                            class="w-full bg-[#000099] text-white font-bold py-3.5 px-4 rounded-xl hover:bg-[#003366] focus:outline-none focus:ring-2 focus:ring-[#000099] focus:ring-offset-2 transition-all duration-200 uppercase tracking-wider shadow-md hover:shadow-lg inline-flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            Iniciar Sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
