<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar>
            <svg class="text-[#FFE600]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Ejecutivos</h2>
            <p class="page-header-card__subtitle">Las mismas cuentas de usuario del CRM; aquí se gestionan cartera y asignaciones.</p>
        </div>
    </x-slot>

    @php
        $executivesFilterContactId = request()->filled('contacto_id') ? (int) request('contacto_id') : null;
        $oldAssignUserId = old('user_id');
        $oldAssignUserName = $oldAssignUserId ? optional(\App\Models\User::find($oldAssignUserId))->name ?? '' : '';
        $executivesPageInitial = [
            'contactTransferOpen' => $errors->has('to_user_id'),
            'transferToUserId' => (string) old('to_user_id', ''),
            'selectedExecutiveId' => $oldAssignUserId ? (int) $oldAssignUserId : null,
            'selectedExecutiveName' => $oldAssignUserName,
            'filterContactId' => $executivesFilterContactId,
            'autoAssignContactId' => $autoAssignContactId ?? null,
            'registerModalOpen' => $errors->has('name') || $errors->has('email') || $errors->has('password') || $errors->has('role') || $errors->has('is_active'),
            'modalOpen' => ($errors->has('contact_id') || $errors->has('user_id')) && ! $errors->has('name') && ! $errors->has('email'),
        ];
    @endphp

    <div
        class="space-y-6"
        x-data="executivesPage(@js($executivesPageInitial))"
    >
        {{-- Filtros --}}
        <div class="panel-card-dark">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                <h3 class="text-sm font-semibold text-white/90">Filtros</h3>
                <button
                    type="button"
                    @click="registerModalOpen = true"
                    class="btn-amber-app self-end sm:self-auto flex-shrink-0"
                    title="Registrar un nuevo ejecutivo en el sistema"
                >
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Registrar nuevo ejecutivo
                </button>
            </div>
            <form method="GET" action="{{ route('executives.index') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="empresa_id" class="block text-xs font-medium text-white/70 mb-1.5">Empresa</label>
                    <select
                        id="empresa_id"
                        name="empresa_id"
                        class="w-full rounded-xl border-0 bg-white/15 text-white text-sm py-2.5 px-3 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50"
                    >
                        <option value="">Todas</option>
                        @foreach($companiesForFilter as $c)
                            <option value="{{ $c->id }}" @selected(request('empresa_id') == $c->id)>{{ $c->nombre_comercial }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="estado" class="block text-xs font-medium text-white/70 mb-1.5">Estado</label>
                    <select
                        id="estado"
                        name="estado"
                        class="w-full rounded-xl border-0 bg-white/15 text-white text-sm py-2.5 px-3 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50"
                    >
                        <option value="">Todos</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activo</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivo</option>
                    </select>
                </div>
                <div>
                    <label for="contacto_id" class="block text-xs font-medium text-white/70 mb-1.5">Contacto</label>
                    <select
                        id="contacto_id"
                        name="contacto_id"
                        class="w-full rounded-xl border-0 bg-white/15 text-white text-sm py-2.5 px-3 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50"
                    >
                        <option value="">Todos</option>
                        @foreach($contactsForFilter as $ct)
                            <option value="{{ $ct->id }}" @selected(request('contacto_id') == $ct->id)>{{ $ct->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('executives.index', ['clear_filters' => 1]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/30 text-white/90 text-sm font-medium hover:bg-white/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        Limpiar
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#FFE600] text-[#071A3D] text-sm font-semibold shadow-md hover:bg-[#ffeb3b]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
                        Aplicar
                    </button>
                </div>
            </form>
        </div>

        {{-- Modal: registrar nuevo ejecutivo --}}
        <div
            x-show="registerModalOpen"
            x-cloak
            class="fixed inset-0 z-[55] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            @keydown.escape.window="closeRegisterModal()"
        >
            <div class="absolute inset-0" @click="closeRegisterModal()" aria-hidden="true"></div>
            <div
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-registrar-ejecutivo-title"
                class="relative w-full max-w-lg rounded-2xl bg-[#000836] border border-white/15 shadow-2xl max-h-[90vh] overflow-y-auto"
                @click.stop
            >
                <div class="sticky top-0 flex items-center justify-between px-5 py-4 border-b border-white/10 bg-[#000836]/95 backdrop-blur z-10">
                    <h3 id="modal-registrar-ejecutivo-title" class="text-lg font-semibold text-white">Registrar nuevo ejecutivo</h3>
                    <button type="button" class="p-2 rounded-lg text-white/80 hover:bg-white/10" @click="closeRegisterModal()" aria-label="Cerrar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('executives.store') }}" class="p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="is_active" value="1">
                    <input type="hidden" name="role" value="usuario">
                    <div>
                        <x-input-label for="reg_exec_name" value="Nombre de usuario" class="text-white/90" />
                        <x-text-input id="reg_exec_name" name="name" type="text" class="mt-1 block w-full bg-white/10 border-white/20 text-white" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="reg_exec_email" value="Gmail (correo)" class="text-white/90" />
                        <x-text-input id="reg_exec_email" name="email" type="email" class="mt-1 block w-full bg-white/10 border-white/20 text-white" :value="old('email')" required autocomplete="email" placeholder="nombre@gmail.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="reg_exec_password" value="Contraseña" class="text-white/90" />
                        <div class="relative mt-1">
                            <x-text-input
                                id="reg_exec_password"
                                name="password"
                                x-bind:type="registerPasswordVisible ? 'text' : 'password'"
                                class="block w-full bg-white/10 border-white/20 text-white pr-10"
                                required
                                autocomplete="new-password"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 z-10 flex items-center px-3 rounded-r-lg text-white/55 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FFE600]/50"
                                @click="registerPasswordVisible = !registerPasswordVisible"
                                x-bind:aria-pressed="registerPasswordVisible"
                                x-bind:aria-label="registerPasswordVisible ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                                aria-controls="reg_exec_password"
                            >
                                <svg x-show="!registerPasswordVisible" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="registerPasswordVisible" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="reg_exec_password_confirmation" value="Confirmar contraseña" class="text-white/90" />
                        <div class="relative mt-1">
                            <x-text-input
                                id="reg_exec_password_confirmation"
                                name="password_confirmation"
                                x-bind:type="registerPasswordConfirmVisible ? 'text' : 'password'"
                                class="block w-full bg-white/10 border-white/20 text-white pr-10"
                                required
                                autocomplete="new-password"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 z-10 flex items-center px-3 rounded-r-lg text-white/55 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FFE600]/50"
                                @click="registerPasswordConfirmVisible = !registerPasswordConfirmVisible"
                                x-bind:aria-pressed="registerPasswordConfirmVisible"
                                x-bind:aria-label="registerPasswordConfirmVisible ? 'Ocultar confirmación de contraseña' : 'Mostrar confirmación de contraseña'"
                                aria-controls="reg_exec_password_confirmation"
                            >
                                <svg x-show="!registerPasswordConfirmVisible" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="registerPasswordConfirmVisible" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-wrap justify-end gap-2 pt-2">
                        <button type="button" class="px-4 py-2.5 rounded-xl border border-white/30 text-white/90 text-sm hover:bg-white/10" @click="closeRegisterModal()">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 rounded-full bg-[#FFE600] text-[#071A3D] text-sm font-semibold shadow-md hover:bg-[#ffeb3b]">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        @if($assignmentContacts)
            {{-- Vista por empresa/contacto: filas tipo tarjeta (empresa · contacto · ejecutivo · acción) --}}
            <div class="panel-card-dark !p-0 overflow-hidden border border-white/10 rounded-2xl shadow-lg">
                <div class="px-5 pt-5 pb-4 sm:px-6 border-b border-white/10 bg-black/20">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-white">Asignaciones</h3>
                        <p class="text-xs text-white/55 mt-1 max-w-2xl leading-relaxed">
                            Cada fila muestra la empresa, el contacto, el ejecutivo responsable y la acción para reasignar el contacto.
                        </p>
                    </div>
                </div>

                <div class="divide-y divide-white/10">
                    @forelse($assignmentContacts as $cRow)
                        <div class="px-4 py-5 sm:px-6 hover:bg-white/[0.04] transition-colors">
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-5 sm:gap-6 xl:gap-4 xl:items-center">
                                <div class="xl:col-span-3 min-w-0">
                                    <p class="text-[11px] font-medium uppercase tracking-wider text-white/45 mb-1">Empresa</p>
                                    <p class="text-sm font-medium text-white leading-snug">{{ $cRow->company?->nombre_comercial ?? '—' }}</p>
                                </div>
                                <div class="xl:col-span-4 min-w-0">
                                    <p class="text-[11px] font-medium uppercase tracking-wider text-white/45 mb-1">Contacto</p>
                                    <p class="text-sm font-medium text-white leading-snug">{{ $cRow->nombre_completo }}</p>
                                </div>
                                <div class="xl:col-span-3 min-w-0 sm:col-span-2">
                                    <p class="text-[11px] font-medium uppercase tracking-wider text-white/45 mb-1">Ejecutivo</p>
                                    @if($cRow->assignedExecutive)
                                        <p class="text-sm font-medium text-white leading-snug">{{ $cRow->assignedExecutive->name }}</p>
                                        <p class="text-xs text-white/50 mt-1 truncate" title="{{ $cRow->assignedExecutive->email }}">{{ $cRow->assignedExecutive->email }}</p>
                                    @else
                                        <p class="text-sm text-white/50 italic">Sin ejecutivo asignado</p>
                                    @endif
                                </div>
                                <div class="xl:col-span-2 flex sm:justify-start xl:justify-end sm:col-span-2 xl:pt-0 pt-1">
                                    @if($cRow->assigned_user_id && $cRow->assignedExecutive)
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center gap-2 w-full sm:w-auto min-h-[40px] px-4 py-2 rounded-xl text-sm font-medium border border-[#FFE600]/50 text-[#FFE600] bg-[#FFE600]/5 hover:bg-[#FFE600]/15 hover:border-[#FFE600]/70 focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40 transition-colors"
                                            @click="openContactTransfer({{ (int) $cRow->assigned_user_id }}, {{ (int) $cRow->id }})"
                                        >
                                            <svg class="w-4 h-4 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                            Transferir a ejecutivo
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center gap-2 w-full sm:w-auto min-h-[40px] px-4 py-2 rounded-xl text-sm font-medium border border-[#FFE600]/60 text-[#FFE600] bg-[#FFE600]/10 hover:bg-[#FFE600]/20 focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40 transition-colors"
                                            @click="openModalForContact({{ (int) $cRow->id }})"
                                        >
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                            Asignar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-14 text-center text-sm text-white/65">
                            No hay contactos que coincidan con los filtros.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="px-1">
                {{ $assignmentContacts->links() }}
            </div>

            {{-- Modal: transferir contacto --}}
            <div
                x-show="contactTransferOpen"
                x-cloak
                x-transition
                class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/65 backdrop-blur-sm"
                role="dialog"
                aria-modal="true"
                aria-labelledby="exec-index-transfer-modal-title"
                @keydown.escape.window="contactTransferOpen = false"
            >
                <div class="absolute inset-0" @click="contactTransferOpen = false" aria-hidden="true"></div>
                <div
                    class="relative w-full max-w-md rounded-2xl border-4 border-[#FFE600] bg-gradient-to-b from-[#1a3d6b] to-[#0f2850] shadow-2xl p-6 text-left"
                    @click.stop
                >
                    <h3 id="exec-index-transfer-modal-title" class="text-lg font-bold text-[#FFE600] mb-1">Transferir contacto</h3>
                    <p class="text-sm text-white/85 mb-4">Elija el ejecutivo que recibirá este contacto en su cartera.</p>
                    <form
                        method="POST"
                        class="space-y-4"
                        x-bind:action="transferFromUserId ? '{{ url('/ejecutivos') }}/' + transferFromUserId + '/contactos/transferir' : '#' "
                    >
                        @csrf
                        <input type="hidden" name="contact_id" x-bind:value="transferContactId">
                        <div>
                            <label for="exec-index-transfer-to-user" class="block text-xs font-semibold text-[#FFE600] mb-1.5">Ejecutivo destino</label>
                            <select
                                id="exec-index-transfer-to-user"
                                name="to_user_id"
                                x-model="transferToUserId"
                                required
                                class="w-full rounded-xl border-2 border-gray-200 bg-white text-gray-900 text-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-[#FFE600] [&>option]:text-gray-900"
                            >
                                <option value="">Seleccione un ejecutivo…</option>
                                @foreach($executivesForTransfer as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('to_user_id')" class="mt-1 text-amber-200 text-xs" />
                        </div>
                        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:justify-end pt-2">
                            <button type="button" class="px-4 py-2.5 rounded-xl border-2 border-white/35 text-white text-sm font-medium hover:bg-white/10 w-full sm:w-auto" @click="contactTransferOpen = false">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="px-5 py-2.5 rounded-xl font-bold bg-[#FFE600] text-[#071A3D] text-sm hover:bg-[#ffeb3b] w-full sm:w-auto disabled:opacity-45 disabled:cursor-not-allowed"
                                :disabled="!transferToUserId || !transferFromUserId"
                            >
                                Confirmar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
        {{-- Tarjetas de ejecutivos (sin filtro empresa/contacto) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($executives as $exec)
                    <a
                        href="{{ \App\Support\CrmNavigation::withReturn(route('executives.show', $exec)) }}"
                        class="group relative rounded-2xl bg-[#071A3D] border border-[#0a2454] shadow-lg overflow-hidden transition transform hover:-translate-y-0.5 hover:shadow-xl hover:border-[#FFE600]/40 focus:outline-none focus:ring-2 focus:ring-[#FFE600] focus:ring-offset-2 focus:ring-offset-white"
                    >
                        <div class="p-5 flex gap-4 items-start">
                            @if($exec->profile_photo_url)
                                <img src="{{ $exec->profile_photo_url }}" alt="" class="w-16 h-16 rounded-full object-cover border-2 border-[#FFE600]/50 flex-shrink-0" />
                            @else
                                <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center text-lg font-bold text-[#FFE600] border-2 border-[#FFE600]/40 flex-shrink-0">
                                    {{ $exec->initials }}
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h3 class="text-lg font-semibold text-white truncate group-hover:text-[#FFE600] transition-colors">{{ $exec->name }}</h3>
                                <p class="text-sm text-white/75 truncate mt-1">{{ $exec->email }}</p>
                                <p class="mt-3">
                                    @if($exec->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Activo</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-red-500/20 text-red-300 border border-red-500/30">Inactivo</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
            @empty
                <div class="col-span-full panel-card-dark text-center py-12 text-white/80">
                        No hay ejecutivos que coincidan con los filtros.
                </div>
            @endforelse
        </div>

        <div class="px-1">
            {{ $executives->links() }}
        </div>
        @endif

        {{-- Transferir cartera (al final, tras el listado) --}}
        @if(isset($executivesForTransfer) && $executivesForTransfer->count() >= 2)
            <div class="panel-card-dark">
                <h3 class="text-center text-base font-bold text-white mb-1">Transferir cartera</h3>
                <p class="text-center text-xs text-white/70 mb-5">Mueva las empresas y contactos asignados de un ejecutivo hacia otro en un solo paso</p>

                <form
                    method="POST"
                    action="{{ route('executives.transfer-portfolio') }}"
                    class="space-y-5"
                    id="form-transfer-cartera"
                    x-ref="transferForm"
                    @submit.prevent="if (!$refs.transferForm.checkValidity()) { $refs.transferForm.reportValidity(); return; } transferConfirmOpen = true"
                >
                    @csrf
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5 lg:gap-6">
                        <div class="flex-1 min-w-0 space-y-2">
                            <label for="transfer_from" class="block text-xs font-semibold text-[#FFE600]">Ejecutivo origen <span class="text-white/60 font-normal">(pierde la asignación)</span></label>
                            <select
                                id="transfer_from"
                                name="from_user_id"
                                required
                                class="w-full rounded-xl border-2 border-gray-200 bg-white text-gray-900 text-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-[#FFE600] [&>option]:text-gray-900"
                            >
                                <option value="" disabled @selected(old('from_user_id') === null || old('from_user_id') === '')>Seleccione ejecutivo…</option>
                                @foreach($executivesForTransfer as $u)
                                    <option value="{{ $u->id }}" @selected((string) old('from_user_id') === (string) $u->id)>{{ $u->name }} — {{ $u->email }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('from_user_id')" class="mt-1 text-amber-200 text-xs" />
                        </div>

                        <div class="hidden lg:flex items-center justify-center pb-2 text-[#FFE600]" aria-hidden="true">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </div>

                        <div class="flex-1 min-w-0 space-y-2">
                            <label for="transfer_to" class="block text-xs font-semibold text-[#FFE600]">Ejecutivo destino <span class="text-white/60 font-normal">(recibe la cartera)</span></label>
                            <select
                                id="transfer_to"
                                name="to_user_id"
                                required
                                class="w-full rounded-xl border-2 border-gray-200 bg-white text-gray-900 text-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-[#FFE600] [&>option]:text-gray-900"
                            >
                                <option value="" disabled @selected(old('to_user_id') === null || old('to_user_id') === '')>Seleccione ejecutivo…</option>
                                @foreach($executivesForTransfer as $u)
                                    <option value="{{ $u->id }}" @selected((string) old('to_user_id') === (string) $u->id)>{{ $u->name }} — {{ $u->email }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('to_user_id')" class="mt-1 text-amber-200 text-xs" />
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2 border-t border-white/10">
                        <p class="text-xs text-white/75 max-w-xl leading-relaxed">
                            Se actualizarán las empresas con <span class="text-[#FFE600] font-medium">ejecutivo asignado</span> y los contactos vinculados al origen. El destino figurará como responsable en el CRM.
                        </p>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-[#FFE600] text-[#071A3D] text-sm font-bold shadow-[0_4px_14px_rgba(0,0,0,0.2)] hover:bg-[#ffeb3b] transition-colors border border-[#fff9c4] shrink-0"
                        >
                            <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Transferir empresas y contactos
                        </button>
                    </div>
                </form>
            </div>

            {{-- Confirmación transferir cartera (sustituye confirm() del navegador) --}}
            <div
                x-show="transferConfirmOpen"
                x-cloak
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-transfer-cartera-titulo"
                @keydown.escape.window="transferConfirmOpen = false"
            >
                <div class="absolute inset-0" @click="transferConfirmOpen = false" aria-hidden="true"></div>
                <div
                    class="relative w-full max-w-md rounded-2xl border-4 border-[#FFE600] bg-gradient-to-b from-[#1a3d6b] to-[#0f2850] shadow-2xl p-6 text-center"
                    @click.stop
                >
                    <div class="mx-auto w-14 h-14 rounded-full bg-[#FFE600]/20 text-[#FFE600] flex items-center justify-center mb-4 ring-2 ring-[#FFE600]/30">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <h3 id="modal-transfer-cartera-titulo" class="text-lg font-bold text-[#FFE600] mb-2">¿Transferir cartera?</h3>
                    <p class="text-sm text-white/90 mb-1 leading-relaxed">
                        Se moverán <span class="font-semibold text-white">todas las empresas y contactos</span> asignados al ejecutivo de <span class="text-[#FFE600]">origen</span> hacia el de <span class="text-[#FFE600]">destino</span>.
                    </p>
                    <p class="text-xs text-white/65 mb-6">Esta acción no se puede deshacer automáticamente.</p>
                    <div class="flex flex-col-reverse sm:flex-row gap-3 sm:justify-center sm:gap-4">
                        <button
                            type="button"
                            class="px-5 py-3 rounded-xl border-2 border-white/35 text-white text-sm font-medium hover:bg-white/10 transition-colors w-full sm:w-auto"
                            @click="transferConfirmOpen = false"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            class="px-5 py-3 rounded-xl font-bold bg-[#FFE600] text-[#071A3D] text-sm hover:bg-[#ffeb3b] shadow-lg border border-[#fff9c4] transition-colors w-full sm:w-auto"
                            @click="$refs.transferForm.submit(); transferConfirmOpen = false"
                        >
                            Sí, transferir
                        </button>
                    </div>
                </div>
            </div>
        @elseif(isset($executivesForTransfer) && $executivesForTransfer->count() < 2)
            <div class="panel-card-dark">
                <p class="text-sm text-white/75 text-center">Para transferir cartera entre ejecutivos hacen falta al menos dos cuentas de ejecutivo en el sistema.</p>
            </div>
        @endif

        {{-- Asistencia de contraseñas (al final de la página) --}}
        <div class="panel-card-dark overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.admin-user-password-assistance')
                </div>
        </div>
        </div>

        {{-- Modal: elegir responsable y confirmar asignación al contacto --}}
        <div
            x-show="modalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            @keydown.escape.window="closeModal()"
        >
            <div class="absolute inset-0" @click="closeModal()" aria-hidden="true"></div>
            <div
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-responsables-title"
                class="relative w-full max-w-lg rounded-2xl bg-[#000836] border border-white/15 shadow-2xl max-h-[90vh] flex flex-col overflow-hidden"
                @click.stop
            >
                <div class="flex-shrink-0 flex items-center justify-between px-5 py-4 border-b border-white/10 bg-[#000836]/95 backdrop-blur z-10">
                    <h3 id="modal-responsables-title" class="text-lg font-semibold text-white">Responsables de cartera</h3>
                    <button type="button" class="p-2 rounded-lg text-white/80 hover:bg-white/10" @click="closeModal()" aria-label="Cerrar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="px-5 pt-4 pb-3 overflow-y-auto flex-1 min-h-0">
                    <p class="text-xs font-semibold text-[#FFE600] uppercase tracking-wide">Dados de alta en el sistema</p>
                    <p class="text-xs text-white/60 mt-1 mb-3">Seleccione un responsable y pulse <strong class="text-white/90">Añadir</strong> para asignarlo al contacto. Las altas de cuentas las gestiona el administrador.</p>
                    <p class="text-xs text-amber-200/90 mb-3 rounded-lg border border-amber-400/30 bg-amber-500/10 px-3 py-2" x-show="!resolvedContactId()" x-cloak>
                        Indique un contacto concreto: use el filtro <strong class="text-white">Contacto</strong> arriba o el botón <strong class="text-white">Asignar</strong> en la fila sin ejecutivo.
                    </p>
                    @if(isset($executivesForTransfer) && $executivesForTransfer->isNotEmpty())
                        <div class="max-h-[min(50vh,20rem)] overflow-y-auto rounded-xl border border-white/10 bg-white/[0.06]">
                            <ul class="divide-y divide-white/10">
                                @foreach($executivesForTransfer as $execRow)
                                    <li class="flex items-stretch gap-1">
                                        <button
                                            type="button"
                                            @click='selectExecutive({{ (int) $execRow->id }}, @json($execRow->name))'
                                            class="flex-1 min-w-0 text-left flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 px-3 py-2.5 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#FFE600]/40"
                                            :class="selectedExecutiveId === {{ (int) $execRow->id }} ? 'bg-[#FFE600]/15 ring-2 ring-inset ring-[#FFE600]/50' : 'hover:bg-white/10'"
                                        >
                                            <span class="font-medium text-white min-w-0">{{ $execRow->name }}</span>
                                            <span class="flex flex-wrap items-center gap-2 text-xs text-white/65 min-w-0">
                                                <span class="truncate">{{ $execRow->email }}</span>
                                                @if($execRow->is_active)
                                                    <span class="shrink-0 inline-flex px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-200 border border-emerald-500/35">Activo</span>
                                                @else
                                                    <span class="shrink-0 inline-flex px-2 py-0.5 rounded-md bg-white/10 text-white/70 border border-white/20">Inactivo</span>
                                                @endif
                                            </span>
                                        </button>
                                        <a
                                            href="{{ route('executives.show', $execRow) }}"
                                            class="shrink-0 self-center px-2.5 py-2 text-xs font-medium text-[#FFE600] hover:underline"
                                            @click.stop
                                        >Ficha</a>
                                    </li>
                                @endforeach
                            </ul>
                    </div>
                                    @else
                        <p class="text-sm text-white/60">No hay ejecutivos dados de alta todavía. El administrador puede crear cuentas desde la gestión de usuarios o el módulo correspondiente.</p>
                                    @endif
                    </div>

                <div x-show="selectedExecutiveId" x-cloak class="flex-shrink-0 border-t border-white/10 bg-[#000836] px-5 py-4 space-y-3">
                    <p class="text-xs text-white/75">
                        <span class="text-[#FFE600] font-semibold" x-text="selectedExecutiveName"></span>
                        <span class="text-white/50"> → </span>
                        <span>se asignará al contacto seleccionado.</span>
                    </p>
                    <form method="POST" action="{{ route('executives.assign-contact') }}" class="space-y-2">
                        @csrf
                        <input type="hidden" name="user_id" x-bind:value="selectedExecutiveId">
                        <input type="hidden" name="contact_id" x-bind:value="resolvedContactId() ?? ''">
                        <x-input-error :messages="$errors->get('contact_id')" class="text-amber-200 text-xs" />
                        <x-input-error :messages="$errors->get('user_id')" class="text-amber-200 text-xs" />
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <button
                                type="submit"
                                class="btn-amber-app justify-center w-full sm:w-auto sm:ml-auto disabled:opacity-40 disabled:pointer-events-none"
                                :disabled="!selectedExecutiveId"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                Añadir
                            </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
