<x-app-user-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg></x-page-header-avatar>
            <div>
                <h2 class="page-header-card__title">Editar Contacto</h2>
            <p class="page-header-card__subtitle">{{ $contact->nombre_completo }}</p>
            </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="panel-card-dark p-6">
                <form method="POST" action="{{ route('contacts.update', $contact) }}">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="company_id" value="Empresa *" />
                            <select id="company_id" name="company_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">Seleccione una empresa</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $contact->company_id) == $company->id ? 'selected' : '' }}>{{ $company->nombre_comercial }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="nombre_completo" value="Nombre Completo *" />
                            <x-text-input id="nombre_completo" name="nombre_completo" type="text" class="mt-1 block w-full" :value="old('nombre_completo', $contact->nombre_completo)" minlength="4" maxlength="255" required />
                            <x-input-error :messages="$errors->get('nombre_completo')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="genero" value="Género" />
                            <select id="genero" name="genero" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Seleccione</option>
                                <option value="Masculino" {{ old('genero', $contact->genero) === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="Femenino" {{ old('genero', $contact->genero) === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                <option value="Otro" {{ old('genero', $contact->genero) === 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="puesto_de_trabajo" value="Puesto de Trabajo" />
                            <x-text-input id="puesto_de_trabajo" name="puesto_de_trabajo" type="text" class="mt-1 block w-full" :value="old('puesto_de_trabajo', $contact->puesto_de_trabajo)" />
                        </div>
                        <div>
                            <x-input-label for="departamento" value="Departamento" />
                            <x-text-input id="departamento" name="departamento" type="text" class="mt-1 block w-full" :value="old('departamento', $contact->departamento)" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Correo electrónico *" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $contact->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div class="flex items-center gap-3 md:col-span-2">
                            <input id="email_activo" name="email_activo" type="checkbox" value="1" class="rounded border-gray-300 text-amber-500 shadow-sm focus:border-amber-500 focus:ring-amber-500" @checked(old('email_activo', $contact->email_activo ?? true)) />
                            <label for="email_activo" class="text-sm text-white/90 select-none">
                                Mostrar correo en fichas, listados y PDF
                            </label>
                        </div>
                        <div>
                            <x-input-label for="telefono" value="Teléfono" />
                            <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono', $contact->telefono)" placeholder="Teléfono fijo" />
                        </div>
                        <div>
                            <x-input-label for="celular" value="Celular" />
                            <x-text-input id="celular" name="celular" type="text" class="mt-1 block w-full" :value="old('celular', $contact->celular)" />
                        </div>
                        <div>
                            <x-input-label for="extension" value="Extensión" />
                            <x-text-input id="extension" name="extension" type="text" class="mt-1 block w-full" :value="old('extension', $contact->extension)" />
                        </div>
                        <div>
                            <x-input-label for="fecha_cumpleanos" value="Fecha de cumpleaños (opcional)" />
                            <x-text-input id="fecha_cumpleanos" name="fecha_cumpleanos" type="date" class="mt-1 block w-full text-gray-900" :value="old('fecha_cumpleanos', $contact->fecha_cumpleanos?->format('Y-m-d'))" />
                            <p class="mt-1 text-xs text-white/60">Para enviar felicitaciones al administrador el día del cumpleaños.</p>
                        </div>
                        <div>
                            <x-input-label for="municipio" value="Municipio / Ciudad" />
                            <x-text-input id="municipio" name="municipio" type="text" class="mt-1 block w-full" :value="old('municipio', $contact->municipio)" />
                        </div>
                        <div>
                            <x-input-label for="estado" value="Estado" />
                            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $contact->estado)" />
                        </div>
                        <div class="md:col-span-2 mt-2 pt-6 border-t border-white/20">
                            <h3 class="text-lg font-semibold text-[#FFE600] mb-2">Datos para ficha de registro del cliente</h3>
                            <p class="text-sm text-white/80 mb-4">Razón social, nombre comercial, domicilio fiscal, RFC y régimen. TEL se toma de Teléfono/Celular de arriba.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <x-input-label for="razon_social" value="RAZÓN SOCIAL" />
                                    <x-text-input id="razon_social" name="razon_social" type="text" class="mt-1 block w-full" :value="old('razon_social', $contact->razon_social)" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="nombre_comercial" value="Nombre comercial" />
                                    <x-text-input id="nombre_comercial" name="nombre_comercial" type="text" class="mt-1 block w-full" :value="old('nombre_comercial', $contact->nombre_comercial)" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="calle_numero" value="CALLE Y NÚMERO" />
                                    <x-text-input id="calle_numero" name="calle_numero" type="text" class="mt-1 block w-full" :value="old('calle_numero', $contact->calle_numero)" />
                                </div>
                                <div>
                                    <x-input-label for="colonia_cp" value="COLONIA Y C.P." />
                                    <x-text-input id="colonia_cp" name="colonia_cp" type="text" class="mt-1 block w-full" :value="old('colonia_cp', $contact->colonia_cp)" />
                                </div>
                                <div>
                                    <x-input-label for="rfc" value="RFC" />
                                    <x-text-input id="rfc" name="rfc" type="text" class="mt-1 block w-full" :value="old('rfc', $contact->rfc)" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="regimen_fiscal" value="RÉGIMEN EN QUE TRIBUTA" />
                                    <x-text-input id="regimen_fiscal" name="regimen_fiscal" type="text" class="mt-1 block w-full" :value="old('regimen_fiscal', $contact->regimen_fiscal)" />
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="notas" value="Notas" />
                            <textarea id="notas" name="notas" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('notas', $contact->notas) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="status_color" value="Estado de prospecto" />
                            <select
                                id="status_color"
                                name="status_color"
                                class="mt-1 block w-full rounded-md border-gray-300"
                            >
                                @foreach(\App\Models\Contact::PROSPECT_STATUS_LABELS as $value => $label)
                                    <option value="{{ $value }}" {{ old('status_color', $contact->status_color ?? 'seguimiento') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status_color')" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-6 gap-3 flex-wrap">
                        <a href="{{ route('contacts.show', $contact) }}" class="btn-icon-text text-gray-600 hover:text-gray-800 px-4 py-2 rounded-xl border border-gray-300 hover:bg-gray-50">Cancelar</a>
                        <button type="submit" class="btn-amber-app">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-user-layout>
