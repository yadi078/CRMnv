<x-app-user-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title">Registrar Venta</h2>
            <p class="page-header-card__subtitle">Agregar curso o servicio vendido al historial</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-6">
                <form method="POST" action="{{ route('user.sales.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="company_id" value="Empresa *" />
                            <select id="company_id" name="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" required>
                                <option value="">Seleccione una empresa</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $companyId ?? null) == $company->id ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="nombre_servicio" value="Nombre del curso o servicio *" />
                            <x-text-input id="nombre_servicio" name="nombre_servicio" type="text" class="mt-1 block w-full" :value="old('nombre_servicio')" placeholder="Ej: Capacitación en Ventas, Curso de Liderazgo" required />
                            <x-input-error :messages="$errors->get('nombre_servicio')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="fecha_venta" value="Fecha de venta *" />
                            <x-text-input id="fecha_venta" name="fecha_venta" type="date" class="mt-1 block w-full" :value="old('fecha_venta', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('fecha_venta')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="monto" value="Monto ($)" />
                            <x-text-input id="monto" name="monto" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('monto')" placeholder="0.00" />
                            <x-input-error :messages="$errors->get('monto')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tipo_pago" value="Tipo de pago" />
                            <select id="tipo_pago" name="tipo_pago" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                <option value="">Seleccione</option>
                                <option value="efectivo" {{ old('tipo_pago') === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="transferencia" {{ old('tipo_pago') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="tarjeta_credito" {{ old('tipo_pago') === 'tarjeta_credito' ? 'selected' : '' }}>Tarjeta de crédito</option>
                                <option value="tarjeta_debito" {{ old('tipo_pago') === 'tarjeta_debito' ? 'selected' : '' }}>Tarjeta de débito</option>
                                <option value="cheque" {{ old('tipo_pago') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="deposito" {{ old('tipo_pago') === 'deposito' ? 'selected' : '' }}>Depósito</option>
                                <option value="otro" {{ old('tipo_pago') === 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            <x-input-error :messages="$errors->get('tipo_pago')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="participantes" value="Participantes" />
                            <x-text-input id="participantes" name="participantes" type="number" min="1" class="mt-1 block w-full" :value="old('participantes')" />
                            <x-input-error :messages="$errors->get('participantes')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="notas" value="Notas" />
                            <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
                            <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ route('user.sales.index') }}" class="btn-danger-app">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancelar
                        </a>
                        <button type="submit" class="btn-amber-app">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
    </div>
</x-app-user-layout>
