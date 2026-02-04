<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-azul-fuerte leading-tight">
            Editar Seguimiento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <form method="POST" action="{{ route('follow-ups.update', $followUp) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="company_id" value="Empresa (Opcional)" />
                            <select id="company_id" name="company_id" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Seleccione una empresa</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $followUp->company_id) == $company->id ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contact_id" value="Contacto (Opcional)" />
                            <select id="contact_id" name="contact_id" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Seleccione un contacto</option>
                                @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}" {{ old('contact_id', $followUp->contact_id) == $contact->id ? 'selected' : '' }}>{{ $contact->nombre_completo }} - {{ $contact->company->nombre_comercial }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('contact_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tipo_accion" value="Tipo de Acción *" />
                            <select id="tipo_accion" name="tipo_accion" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="llamada" {{ old('tipo_accion', $followUp->tipo_accion) === 'llamada' ? 'selected' : '' }}>Llamada</option>
                                <option value="reunión" {{ old('tipo_accion', $followUp->tipo_accion) === 'reunión' ? 'selected' : '' }}>Reunión</option>
                                <option value="cierre" {{ old('tipo_accion', $followUp->tipo_accion) === 'cierre' ? 'selected' : '' }}>Cierre</option>
                            </select>
                            <x-input-error :messages="$errors->get('tipo_accion')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="fecha_alarma" value="Fecha y Hora Programada *" />
                            <x-text-input id="fecha_alarma" name="fecha_alarma" type="datetime-local" class="mt-1 block w-full" :value="old('fecha_alarma', $followUp->fecha_alarma->format('Y-m-d\TH:i'))" required />
                            <x-input-error :messages="$errors->get('fecha_alarma')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="asignado_a" value="Asignado a" />
                            <select id="asignado_a" name="asignado_a" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Sin asignar</option>
                                @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}" {{ old('asignado_a', $followUp->asignado_a) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('asignado_a')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="bitacora_notas" value="Bitácora de Notas" />
                            <textarea id="bitacora_notas" name="bitacora_notas" rows="6" class="mt-1 block w-full rounded-md border-gray-300">{{ old('bitacora_notas', $followUp->bitacora_notas) }}</textarea>
                            <x-input-error :messages="$errors->get('bitacora_notas')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 space-x-4">
                        <a href="{{ route('follow-ups.show', $followUp) }}" class="text-gray-600 hover:text-gray-800 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancelar
                        </a>
                        <x-primary-button class="bg-amarillo text-azul-fuerte hover:bg-yellow-400 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Actualizar
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
