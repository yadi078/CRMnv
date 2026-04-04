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
            'assignmentPageContactIds' => isset($assignmentContacts) && $assignmentContacts->isNotEmpty()
                ? $assignmentContacts->pluck('id')->values()->all()
                : [],
            'bulkExportToUserId' => old('bulk_assign') ? (string) old('user_id', '') : '',
            'registerModalOpen' => $errors->has('name') || $errors->has('email') || $errors->has('password') || $errors->has('role') || $errors->has('is_active'),
            'modalOpen' => ($errors->has('contact_id') || $errors->has('user_id')) && ! $errors->has('name') && ! $errors->has('email') && ! old('bulk_assign'),
            'bulkExportModalOpen' => $errors->has('contact_ids') || ($errors->has('user_id') && old('bulk_assign')),
            'previewPortfolioTransferUrl' => route('executives.preview-portfolio-transfer'),
            'executivesTransferToastMessage' => session('executives_transfer_toast'),
        ];
    @endphp

    <div
        class="space-y-6"
        x-data="executivesPage(@js($executivesPageInitial))"
    >
        @if (session('success'))
            <div class="rounded-xl border-2 border-[#FFE600] bg-emerald-950/35 px-4 py-3 text-sm text-white shadow-lg flex items-start gap-3" role="status">
                <svg class="w-6 h-6 text-[#FFE600] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="font-medium leading-snug">{{ session('success') }}</p>
            </div>
        @endif

        <div
            x-show="transferSuccessToast.show"
            x-cloak
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-6 left-1/2 z-[75] w-[calc(100%-2rem)] max-w-lg -translate-x-1/2 rounded-2xl border-2 border-emerald-400/80 bg-emerald-950/95 px-4 py-3 text-sm text-white shadow-2xl"
            role="alert"
        >
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-emerald-300 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="font-medium leading-snug" x-text="transferSuccessToast.message"></p>
            </div>
        </div>
        @if (session('status'))
            <div class="rounded-xl border-2 border-[#FFE600] bg-emerald-950/35 px-4 py-3 text-sm text-white shadow-lg flex items-start gap-3" role="status">
                <svg class="w-6 h-6 text-[#FFE600] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="font-medium leading-snug">{{ session('status') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-xl border-2 border-red-400/70 bg-red-950/40 px-4 py-3 text-sm text-red-50 shadow-lg flex items-start gap-3" role="alert">
                <svg class="w-6 h-6 text-red-300 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <p class="font-medium leading-snug">{{ session('error') }}</p>
            </div>
        @endif
        @if (session('warning'))
            <div class="rounded-xl border-2 border-amber-400/70 bg-amber-950/35 px-4 py-3 text-sm text-amber-50 shadow-lg" role="status">
                <p class="font-medium leading-snug">{{ session('warning') }}</p>
            </div>
        @endif

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
            <form method="GET" action="{{ route('executives.index') }}" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-x-5 md:gap-y-4 items-end">
                    <div class="min-w-0">
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
                    <div class="min-w-0">
                        <label for="entidad" class="block text-xs font-medium text-white/70 mb-1.5">Estado (México)</label>
                        <select
                            id="entidad"
                            name="entidad"
                            class="w-full rounded-xl border-0 bg-white/15 text-white text-sm py-2.5 px-3 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50"
                        >
                            <option value="">Todos</option>
                            @foreach($mexicanStates as $ent)
                                <option value="{{ $ent }}" @selected(request('entidad') === $ent)>{{ $ent }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-0">
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
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-x-5 md:gap-y-4 items-end pt-1 border-t border-white/10">
                    <div class="min-w-0">
                        <label for="cuenta_activa" class="block text-xs font-medium text-white/70 mb-1.5">Estado de cuenta</label>
                        <select
                            id="cuenta_activa"
                            name="cuenta_activa"
                            class="w-full rounded-xl border-0 bg-white/15 text-white text-sm py-2.5 px-3 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50"
                        >
                            <option value="">Todos</option>
                            <option value="activo" @selected(request('cuenta_activa') === 'activo')>Activo</option>
                            <option value="inactivo" @selected(request('cuenta_activa') === 'inactivo')>Inactivo</option>
                        </select>
                    </div>
                    <div class="min-w-0 md:col-span-1">
                        <label for="ejecutivo_id" class="block text-xs font-medium text-white/70 mb-1.5">Ejecutivo (asignación)</label>
                        <select
                            id="ejecutivo_id"
                            name="ejecutivo_id"
                            class="w-full rounded-xl border-0 bg-white/15 text-white text-sm py-2.5 px-3 focus:bg-white/25 focus:ring-2 focus:ring-[#FFE600]/50"
                        >
                            <option value="">Todos (con o sin ejecutivo)</option>
                            <option value="sin" @selected(request('ejecutivo_id') === 'sin')>Sin ejecutivo asignado</option>
                            <option value="con" @selected(request('ejecutivo_id') === 'con')>Con ejecutivo asignado</option>
                            @foreach($executiveFilterOptions as $opt)
                                <option value="{{ $opt['value'] }}" @selected((string) request('ejecutivo_id') === (string) $opt['value'])>{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-0 flex flex-wrap gap-2 md:justify-end md:items-end">
                    <a href="{{ route('executives.index', ['clear_filters' => 1]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/30 text-white/90 text-sm font-medium hover:bg-white/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        Limpiar
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#FFE600] text-[#071A3D] text-sm font-semibold shadow-md hover:bg-[#ffeb3b]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" /></svg>
                        Aplicar
                    </button>
                    </div>
                </div>
                <p class="text-[11px] text-white/45 leading-snug">Use «Sin» o «Con» para listar contactos y asignarlos sin elegir empresa antes. Los nombres solo de ficha (sin cuenta en el CRM) sirven para filtrar por el campo ejecutivo de la empresa.</p>
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
                            Cada fila muestra la empresa, el contacto, el ejecutivo responsable y la acción para asignar o transferir. Filtre por «Sin ejecutivo asignado» para ver pendientes de cartera.
                        </p>
                    </div>
                </div>

                @if($assignmentContacts->isNotEmpty())
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 px-4 py-3 sm:px-6 border-b border-white/10 bg-[#071A3D]/55">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer text-sm text-white/90 select-none">
                        <input
                            type="checkbox"
                            class="h-4 w-4 shrink-0 rounded border-white/35 bg-white/10 text-[#FFE600] focus:ring-2 focus:ring-[#FFE600]/50 focus:ring-offset-0 focus:ring-offset-transparent"
                            :checked="allOnPageSelected()"
                            @change="selectAllOnPage($event.target.checked)"
                        />
                        <span class="font-medium">Seleccionar todo</span>
                    </label>
                    <span class="text-xs text-white/50 tabular-nums" x-show="selectedIds.length > 0" x-cloak x-text="selectedIds.length + ' seleccionado(s)'"></span>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-[#FFE600] px-4 py-2 text-sm font-bold text-[#071A3D] shadow-md hover:bg-[#ffeb3b] sm:ml-auto disabled:opacity-40 disabled:cursor-not-allowed"
                        x-show="selectedIds.length > 0"
                        x-cloak
                        @click="openBulkExportModal()"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M8 12l4 4m0 0l4-4m-4 4V4"/></svg>
                        Exportar a ejecutivo
                    </button>
                </div>
                @endif

                <div class="divide-y divide-white/10">
                    @forelse($assignmentContacts as $cRow)
                        <div class="px-4 py-5 sm:px-6 hover:bg-white/[0.04] transition-colors">
                            <div class="flex gap-3 sm:gap-4 xl:gap-5">
                                <div class="pt-0.5 shrink-0">
                                    <input
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-white/35 bg-white/10 text-[#FFE600] focus:ring-2 focus:ring-[#FFE600]/50"
                                        :checked="isContactSelected({{ (int) $cRow->id }})"
                                        @change="toggleContactSelection({{ (int) $cRow->id }})"
                                        aria-label="Seleccionar contacto {{ $cRow->nombre_completo }}"
                                    />
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-5 sm:gap-6 xl:gap-4 xl:items-center flex-1 min-w-0">
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
                        </div>
                    @empty
                        <div class="px-6 py-14 text-center text-sm text-white/65">
                            No hay contactos que coincidan con los filtros.
                        </div>
                    @endforelse
                </div>

                @if (! empty($assignmentFilterStats))
                    <div class="px-4 py-4 sm:px-6 sm:py-5 border-t border-white/10 bg-black/15">
                        <x-executive-portfolio-stats
                            title="Totales con los filtros actuales"
                            :companies-count="$assignmentFilterStats['companies']"
                            :contacts-count="$assignmentFilterStats['contacts']"
                            footnote="Cantidades exactas de todo el resultado filtrado (no solo esta página)."
                            class="!bg-transparent border-0 !p-0 shadow-none"
                        />
                    </div>
                @endif
            </div>

            <div class="px-1">
                {{ $assignmentContacts->links() }}
            </div>

            {{-- Modal: asignación masiva a ejecutivo --}}
            <div
                x-show="bulkExportModalOpen"
                x-cloak
                x-transition
                class="fixed inset-0 z-[72] flex items-center justify-center p-4 bg-black/65 backdrop-blur-sm"
                role="dialog"
                aria-modal="true"
                aria-labelledby="exec-bulk-export-title"
                @keydown.escape.window="bulkExportModalOpen && closeBulkExportModal()"
            >
                <div class="absolute inset-0" @click="closeBulkExportModal()" aria-hidden="true"></div>
                <div
                    class="relative w-full max-w-md rounded-2xl border-4 border-[#FFE600] bg-gradient-to-b from-[#1a3d6b] to-[#0f2850] shadow-2xl p-6 text-left"
                    @click.stop
                >
                    <h3 id="exec-bulk-export-title" class="text-lg font-bold text-[#FFE600] mb-1">Exportar a ejecutivo</h3>
                    <p class="text-sm text-white/85 mb-4">
                        Los <span class="font-semibold text-white" x-text="selectedIds.length"></span> contacto(s) seleccionado(s) pasarán a la cartera del ejecutivo que elija.
                    </p>
                    <form method="POST" action="{{ route('executives.bulk-assign-contacts') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="bulk_assign" value="1">
                        <template x-for="id in selectedIds" :key="'bulk-cid-' + id">
                            <input type="hidden" name="contact_ids[]" :value="id">
                        </template>
                        <div>
                            <label for="exec-bulk-export-user" class="block text-xs font-semibold text-[#FFE600] mb-1.5">Ejecutivo destino</label>
                            <select
                                id="exec-bulk-export-user"
                                name="user_id"
                                x-model="bulkExportToUserId"
                                required
                                class="w-full rounded-xl border-2 border-gray-200 bg-white text-gray-900 text-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-[#FFE600] [&>option]:text-gray-900"
                            >
                                <option value="">Seleccione un ejecutivo…</option>
                                @foreach($executivesForTransfer as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-1 text-amber-200 text-xs" />
                            <x-input-error :messages="$errors->get('contact_ids')" class="mt-1 text-amber-200 text-xs" />
                        </div>
                        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:justify-end pt-2">
                            <button type="button" class="px-4 py-2.5 rounded-xl border-2 border-white/35 text-white text-sm font-medium hover:bg-white/10 w-full sm:w-auto" @click="closeBulkExportModal()">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="px-5 py-2.5 rounded-xl font-bold bg-[#FFE600] text-[#071A3D] text-sm hover:bg-[#ffeb3b] w-full sm:w-auto disabled:opacity-45 disabled:cursor-not-allowed"
                                :disabled="!bulkExportToUserId"
                            >
                                Confirmar
                            </button>
                        </div>
                    </form>
                </div>
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
                    <div
                        class="group relative rounded-2xl bg-[#071A3D] border border-[#0a2454] shadow-lg overflow-hidden transition transform hover:-translate-y-0.5 hover:shadow-xl hover:border-[#FFE600]/40 focus-within:ring-2 focus-within:ring-[#FFE600] focus-within:ring-offset-2 focus-within:ring-offset-white"
                    >
                        <a
                            href="{{ \App\Support\CrmNavigation::withReturn(route('executives.show', $exec)) }}"
                            class="block p-5 flex gap-4 items-start focus:outline-none"
                        >
                            @if($exec->profile_photo_url)
                                <img src="{{ $exec->profile_photo_url }}" alt="" class="w-16 h-16 rounded-full object-cover border-2 border-[#FFE600]/50 flex-shrink-0" />
                            @else
                                <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center text-lg font-bold text-[#FFE600] border-2 border-[#FFE600]/40 flex-shrink-0">
                                    {{ $exec->initials }}
                                </div>
                            @endif
                            <div class="min-w-0 flex-1 pr-10">
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
                        </a>
                        <form
                            method="POST"
                            action="{{ route('executives.destroy', $exec) }}"
                            class="absolute top-3 right-3 z-10"
                            onsubmit="return confirm('¿Eliminar al ejecutivo «{{ $exec->name }}»? Se quitarán las asignaciones de empresas y contactos y se borrará la cuenta de usuario.');"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl border border-red-500/45 bg-red-950/45 p-2 text-red-200 hover:bg-red-900/55 hover:border-red-400/60 focus:outline-none focus:ring-2 focus:ring-red-400/50"
                                title="Eliminar ejecutivo"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span class="sr-only">Eliminar ejecutivo</span>
                            </button>
                        </form>
                    </div>
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
        @if(!empty($canPortfolioTransfer))
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
                    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] gap-x-6 gap-y-4 lg:items-end">
                        <div class="min-w-0 flex flex-col gap-2">
                            <label for="transfer_from" class="block text-xs font-semibold text-[#FFE600] leading-snug">Ejecutivo origen <span class="text-white/60 font-normal">(pierde la asignación)</span></label>
                            <select
                                id="transfer_from"
                                name="from_user_id"
                                required
                                @change="refreshTransferPreview($event.target.value)"
                                class="w-full rounded-xl border-2 border-gray-200 bg-white text-gray-900 text-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-[#FFE600] [&>option]:text-gray-900"
                            >
                                <option value="" disabled @selected(old('from_user_id') === null || old('from_user_id') === '')>Seleccione origen…</option>
                                @foreach($executiveFilterOptions as $opt)
                                    <option value="{{ $opt['value'] }}" @selected((string) old('from_user_id') === (string) $opt['value'])>{{ $opt['label'] }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('from_user_id')" class="text-amber-200 text-xs" />
                        </div>

                        <div class="hidden lg:flex justify-center text-[#FFE600] self-end pb-2.5 shrink-0" aria-hidden="true">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </div>

                        <div class="min-w-0 flex flex-col gap-2">
                            <label for="transfer_to" class="block text-xs font-semibold text-[#FFE600] leading-snug">Ejecutivo destino <span class="text-white/60 font-normal">(recibe la cartera; solo activos y dados de alta)</span></label>
                            <select
                                id="transfer_to"
                                name="to_user_id"
                                required
                                class="w-full rounded-xl border-2 border-gray-200 bg-white text-gray-900 text-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-[#FFE600] [&>option]:text-gray-900"
                            >
                                <option value="" disabled @selected(old('to_user_id') === null || old('to_user_id') === '')>Seleccione ejecutivo…</option>
                                @foreach($executivesForPortfolioDestination as $u)
                                    <option value="{{ $u->id }}" @selected((string) old('to_user_id') === (string) $u->id)>{{ $u->name }} — {{ $u->email }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('to_user_id')" class="text-amber-200 text-xs" />
                        </div>
                    </div>
                    <p class="text-[10px] text-white/50 leading-snug mt-3">Misma lista de origen que el filtro «Ejecutivo»: cuentas del CRM y nombres solo en ficha. La operación también mueve los contactos vinculados; aquí solo se listan empresas.</p>

                    <div class="mt-4 rounded-xl border border-white/15 bg-[#071A3D]/45 px-4 py-3">
                        <h4 class="text-xs font-semibold text-[#FFE600] mb-2">Empresas que se transferirán</h4>
                        <p class="text-xs text-white/45 mb-2" x-show="!transferPreviewFromValue">Elija un ejecutivo de origen para ver la lista.</p>
                        <p class="text-xs text-white/60 mb-2 flex items-center gap-2" x-show="transferPreviewLoading">
                            <span class="inline-block h-3.5 w-3.5 animate-spin rounded-full border-2 border-[#FFE600] border-t-transparent" aria-hidden="true"></span>
                            Cargando…
                        </p>
                        <p class="text-xs text-white/65 mb-0" x-show="!transferPreviewLoading && transferPreviewFromValue && transferPreviewCompanies.length === 0">No hay empresas asignadas a ese origen.</p>
                        <ul class="text-sm text-white/90 space-y-1 max-h-48 overflow-y-auto pr-1" x-show="!transferPreviewLoading && transferPreviewCompanies.length > 0" x-cloak>
                            <template x-for="c in transferPreviewCompanies" :key="c.id">
                                <li class="border-b border-white/10 pb-1 last:border-0" x-text="c.nombre_comercial"></li>
                            </template>
                        </ul>
                        <p class="text-[10px] text-white/45 mt-2" x-show="transferPreviewCompanies.length >= 500" x-cloak>Máximo 500 empresas en vista previa.</p>

                        <div
                            class="mt-4 pt-4 border-t border-white/10"
                            x-show="transferPreviewFromValue && !transferPreviewLoading"
                            x-cloak
                        >
                            <h4 class="text-xs font-semibold text-[#FFE600] mb-2">Totales exactos a transferir</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="metric-card-dark metric-card-dark--compact">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="metric-card-dark__label uppercase tracking-wide text-[0.6875rem]">Empresas</p>
                                            <p class="metric-card-dark__value tabular-nums" x-text="Number(transferPreviewCompanyCount).toLocaleString('es-MX')"></p>
                                        </div>
                                        <div class="metric-card-dark__icon-wrap shrink-0 mt-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="metric-card-dark metric-card-dark--compact">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="metric-card-dark__label uppercase tracking-wide text-[0.6875rem]">Contactos</p>
                                            <p class="metric-card-dark__value tabular-nums" x-text="Number(transferPreviewContactCount).toLocaleString('es-MX')"></p>
                                        </div>
                                        <div class="metric-card-dark__icon-wrap shrink-0 mt-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-white/50 mt-2 leading-snug" x-show="transferPreviewCompaniesPreviewTruncated" x-cloak>La lista superior muestra como máximo 500 empresas; los totales reflejan la cartera completa del origen.</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2 border-t border-white/10">
                        <p class="text-xs text-white/75 max-w-xl leading-relaxed">
                            Origen «solo ficha»: empresas con ese texto en ejecutivo y sin usuario CRM vinculado; se enlazan al destino. Origen con cuenta: mismas reglas que antes. Destino: solo cuentas <span class="text-[#FFE600] font-medium">activas</span> y <span class="text-[#FFE600] font-medium">aprobadas</span>.
                        </p>
                        <div class="flex flex-wrap items-center gap-3 justify-end sm:justify-end w-full sm:w-auto">
                            <button
                                type="button"
                                @click="clearTransferPortfolioForm()"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full border-2 border-white/35 text-white text-sm font-semibold hover:bg-white/10 transition-colors shrink-0"
                            >
                                <svg class="w-5 h-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Limpiar
                            </button>
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
        @else
            <div class="panel-card-dark">
                <p class="text-sm text-white/75 text-center max-w-2xl mx-auto leading-relaxed">Para transferir cartera hace falta al menos un <strong class="text-white/90">origen</strong> (cuenta de cartera o nombre en ficha) y un <strong class="text-white/90">destino</strong> que sea ejecutivo <strong class="text-white/90">activo y aprobado</strong>. Si solo hay un ejecutivo de cartera dado de alta, registre otro o reactive una cuenta.</p>
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
                                            href="{{ \App\Support\CrmNavigation::withReturn(route('executives.show', $execRow)) }}"
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
