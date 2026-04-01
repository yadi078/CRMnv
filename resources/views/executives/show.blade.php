<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar :user="$executive" :fallback-initials="true" :compact="true">
            <svg class="text-[#FFE600]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Perfil del ejecutivo</h2>
            <p class="page-header-card__subtitle">{{ $executive->name }} — mismo usuario que inicia sesión en el CRM</p>
        </div>
        <div class="flex flex-wrap gap-2 ml-auto justify-end items-center">
            <x-executive-reminder-button :executive="$executive" />
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif
        @if (session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-100">
                {{ session('error') }}
            </div>
        @endif

        {{-- Información --}}
        <div class="panel-card-dark">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-4">Información del usuario</h3>
            <div class="flex flex-col sm:flex-row gap-5 sm:items-start">
                <div class="flex-shrink-0" aria-hidden="true">
                    @if($executive->profile_photo_url)
                        <img
                            src="{{ $executive->profile_photo_url }}"
                            alt=""
                            class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-2 border-[#FFE600] shadow-[0_0_0_1px_rgba(255,255,255,0.45),0_2px_10px_rgba(0,0,0,0.25)]"
                            width="64"
                            height="64"
                            decoding="async"
                        />
                    @else
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full flex items-center justify-center text-[#FFE600] text-sm font-bold border-2 border-[#FFE600] bg-[#0f2744]/90 shadow-[0_0_0_1px_rgba(255,255,255,0.45),0_2px_10px_rgba(0,0,0,0.25)]">
                            {{ $executive->initials }}
                        </div>
                    @endif
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm flex-1 min-w-0">
                    <div>
                        <dt class="text-white/60 font-medium">Nombre</dt>
                        <dd class="text-white mt-0.5">{{ $executive->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-white/60 font-medium">Correo</dt>
                        <dd class="text-white mt-0.5 break-all">{{ $executive->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-white/60 font-medium">Estado de cuenta</dt>
                        <dd class="mt-0.5">
                            @if($executive->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Activo</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-red-500/20 text-red-300 border border-red-500/30">Inactivo</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-white/60 font-medium">Aprobación</dt>
                        <dd class="text-white mt-0.5">
                            @if($executive->approval_status === 'aprobado')
                                <span class="text-emerald-300">Aprobado</span>
                            @elseif($executive->approval_status === 'pendiente')
                                <span class="text-amber-300">Pendiente</span>
                            @else
                                <span class="text-red-300">Rechazado</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Empresas asignadas: ficha azul oscuro + tarjetas de contacto (referencia CRM) --}}
        <div class="space-y-8">
            <h3 class="text-lg font-bold text-[#071A3D] px-1 tracking-tight">Empresas asignadas</h3>

            @if($executive->assignedCompanies->isEmpty())
                <div class="rounded-2xl bg-gradient-to-br from-[#0f2744] via-[#0c2240] to-[#071A3D] border border-[#1a3d6b]/60 shadow-xl p-6">
                    <p class="text-white/75 text-sm">Ninguna empresa asignada aún.</p>
                </div>
            @else
                @foreach($executive->assignedCompanies as $company)
                    <article class="rounded-2xl bg-gradient-to-br from-[#0f2744] via-[#0c2240] to-[#071A3D] border border-[#1e4976]/50 shadow-[0_8px_32px_rgba(0,0,0,0.35)] overflow-hidden">
                        {{-- Bloque empresa: título amarillo + datos en blanco + separadores --}}
                        <div class="px-5 sm:px-7 pt-6 sm:pt-7 pb-4">
                            <a href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $company)) }}" class="block group focus:outline-none focus:ring-2 focus:ring-[#FFE600]/50 rounded-lg">
                                <h4 class="text-xl sm:text-2xl font-bold text-[#FFE600] leading-tight group-hover:text-[#fff59d] transition-colors">
                                    {{ $company->nombre_comercial }}
                                </h4>
                            </a>

                            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-3 text-sm text-white">
                                @if($company->rfc)
                                    <p class="leading-snug"><span class="text-white/75 font-medium">RFC:</span> <span class="text-white">{{ $company->rfc }}</span></p>
                                @endif
                                @if($company->municipio || $company->estado)
                                    <p class="leading-snug"><span class="text-white/75 font-medium">Ciudad, Estado:</span> <span class="text-white">{{ trim(($company->municipio ?? '') . ', ' . ($company->estado ?? ''), ' ,') }}</span></p>
                                @endif
                                @if($company->sector)
                                    <p class="leading-snug"><span class="text-white/75 font-medium">Sector:</span> <span class="text-white">{{ $company->sector }}</span></p>
                                @endif
                            </div>

                            @if($company->ejecutivo_asignado)
                                <div class="mt-4 pt-4 border-t border-sky-300/25">
                                    <p class="text-sm text-white"><span class="text-white/75 font-medium">Ejecutivo asignado:</span> {{ $company->ejecutivo_asignado }}</p>
                                </div>
                            @endif

                            @if($company->datos_fiscales)
                                <div class="mt-4 pt-4 border-t border-sky-300/25">
                                    <p class="text-sm text-white leading-relaxed">
                                        <span class="text-white/75 font-medium">Domicilio fiscal:</span>
                                        {{ \Illuminate\Support\Str::limit($company->datos_fiscales, 280) }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Contactos: tarjetas blancas, solo ribete amarillo superior --}}
                        <div class="px-5 sm:px-7 pb-6 sm:pb-7 pt-2 bg-[#050d1a]/40 border-t border-sky-300/20">
                            @if($company->contacts->isEmpty())
                                <p class="text-white/80 text-sm py-2">Esta empresa aún no tiene contactos registrados.</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                    @foreach($company->contacts as $contact)
                                        <a
                                            href="{{ \App\Support\CrmNavigation::withReturn(route('contacts.show', $contact)) }}"
                                            class="group flex flex-col bg-white rounded-2xl shadow-[0_6px_20px_rgba(0,0,0,0.2)] px-4 py-4 min-h-[200px] border border-gray-200/90 border-t-[5px] border-t-[#FFE600] transition transform hover:-translate-y-1 hover:shadow-xl cursor-pointer text-left"
                                        >
                                            <span class="text-base font-bold text-gray-900 leading-snug group-hover:text-[#071A3D]">
                                                {{ $contact->nombre_completo }}
                                            </span>
                                            @if($contact->puesto_de_trabajo)
                                                <p class="text-sm text-gray-500 mt-1">{{ $contact->puesto_de_trabajo }}</p>
                                            @endif
                                            <div class="mt-3 space-y-1.5 text-sm">
                                                <p class="text-gray-900">
                                                    <span class="font-bold text-black">Correo:</span>
                                                    <span class="font-normal">{{ ($contact->email_activo ?? true) ? ($contact->email ?? '—') : '—' }}</span>
                                                </p>
                                                <p class="text-gray-900">
                                                    <span class="font-bold text-black">Teléfono:</span>
                                                    <span class="font-normal">{{ $contact->celular ?? $contact->telefono ?? '—' }}</span>
                                                </p>
                                            </div>
                                            <div class="mt-auto pt-3 border-t border-gray-300">
                                                <span class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 rounded-full text-xs font-bold bg-[#c8ead8] text-black border border-gray-800/35 shadow-sm">
                                                    {{ $contact->status_label }}
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            @endif

            @if(isset($orphanAssignedContacts) && $orphanAssignedContacts->isNotEmpty())
                <div class="rounded-2xl bg-gradient-to-br from-[#0f2744] via-[#0c2240] to-[#071A3D] border border-[#1e4976]/50 shadow-xl p-5 sm:p-7">
                    <h3 class="text-lg font-bold text-[#FFE600] mb-4">Contactos asignados (otras empresas o sin empresa en cartera)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($orphanAssignedContacts as $ct)
                            <a
                                href="{{ \App\Support\CrmNavigation::withReturn(route('contacts.show', $ct)) }}"
                                class="flex flex-col bg-white rounded-2xl shadow-md px-4 py-4 border border-gray-200 border-t-[5px] border-t-[#FFE600] hover:shadow-lg transition"
                            >
                                <span class="text-base font-bold text-gray-900">{{ $ct->nombre_completo }}</span>
                                <p class="text-sm text-gray-500 mt-1">{{ $ct->company?->nombre_comercial ?? 'Sin empresa' }}</p>
                                @if($ct->puesto_de_trabajo)
                                    <p class="text-sm text-gray-500">{{ $ct->puesto_de_trabajo }}</p>
                                @endif
                                <p class="text-sm mt-2"><span class="font-bold text-black">Correo:</span> <span class="text-gray-900">{{ ($ct->email_activo ?? true) ? ($ct->email ?? '—') : '—' }}</span></p>
                                <p class="text-sm"><span class="font-bold text-black">Teléfono:</span> <span class="text-gray-900">{{ $ct->celular ?? $ct->telefono ?? '—' }}</span></p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Asignación (admin): identidad del ejecutivo + listas --}}
        <div class="panel-card-dark overflow-hidden">
            {{-- Cabecera: burbuja de perfil + nombre + badge de rol --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 pb-5 mb-5 border-b border-white/20">
                <div class="flex items-center gap-4 min-w-0">
                    @if($executive->profile_photo_url)
                        <img
                            src="{{ $executive->profile_photo_url }}"
                            alt=""
                            class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-2 border-[#FFE600] shrink-0 shadow-[0_0_0_1px_rgba(255,255,255,0.45),0_4px_14px_rgba(0,0,0,0.35)]"
                            width="64"
                            height="64"
                            decoding="async"
                        />
                    @else
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full flex items-center justify-center text-[#FFE600] text-sm font-bold border-2 border-[#FFE600] bg-[#0f2744] shrink-0 shadow-[0_0_0_1px_rgba(255,255,255,0.45),0_4px_14px_rgba(0,0,0,0.35)]">
                            {{ $executive->initials }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="text-base sm:text-lg font-bold text-white truncate">{{ $executive->name }}</p>
                        <p class="text-sm text-white/90 truncate mt-0.5">{{ $executive->email }}</p>
                        <span class="inline-flex items-center mt-2 px-3 py-1 rounded-lg text-xs font-bold bg-[#FFE600] text-[#071A3D] shadow-sm border border-[#fff59d]">
                            @if($executive->hasRole('administrador'))
                                Administrador
                            @else
                                Ejecutivo
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <h3 class="panel-card-dark__title panel-card-dark__title--accent mb-2">Gestionar asignaciones</h3>
            <p class="text-sm text-white/90 mb-6 leading-relaxed">
                Seleccione las empresas y contactos vinculados a este ejecutivo. Puede reasignar registros entre ejecutivos guardando desde el perfil de cada uno.
            </p>

            @php
                $transferOldContactId = old('contact_id');
            @endphp
            <div
                x-data="{
                    q: '',
                    transferOpen: {{ ($errors->any() && $transferOldContactId) ? 'true' : 'false' }},
                    transferContactId: {{ $transferOldContactId ? (int) $transferOldContactId : 'null' }},
                    transferToUserId: @js((string) old('to_user_id', '')),
                    openTransferContact(id) {
                        this.transferContactId = id;
                        this.transferToUserId = '';
                        this.transferOpen = true;
                    },
                    matches(text) {
                        const s = this.q.trim().toLowerCase();
                        if (!s) return true;
                        return String(text).toLowerCase().includes(s);
                    },
                    marcarTodasContactos(on) {
                        document.getElementById('picklist-contactos-asig')?.querySelectorAll('.js-cb-contacto').forEach((cb) => { cb.checked = on; });
                    }
                }"
            >
            <form method="POST" action="{{ route('executives.assignments', $executive) }}" class="space-y-6" id="form-asignaciones-ejecutivo">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Empresas: casillas + búsqueda --}}
                    <div
                        class="rounded-2xl border-2 border-[#FFE600]/35 bg-[#0d2744]/80 p-4 shadow-lg"
                        x-data="{
                            q: '',
                            matches(text) {
                                const s = this.q.trim().toLowerCase();
                                if (!s) return true;
                                return String(text).toLowerCase().includes(s);
                            },
                            marcarTodasEmpresas(on) {
                                document.getElementById('picklist-empresas-asig')?.querySelectorAll('.js-cb-empresa').forEach((cb) => { cb.checked = on; });
                            }
                        }"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                            <span class="text-sm font-bold text-[#FFE600]">Empresas</span>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="marcarTodasEmpresas(true)" class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white/10 text-[#FFE600] hover:bg-white/20">Marcar todas</button>
                                <button type="button" @click="marcarTodasEmpresas(false)" class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white/10 text-white/90 hover:bg-white/20">Quitar todas</button>
                            </div>
                        </div>
                        <label class="sr-only" for="buscar-empresa-asig">Buscar empresa</label>
                        <input
                            id="buscar-empresa-asig"
                            type="search"
                            x-model="q"
                            list="datalist-empresas-sin-ejecutivo"
                            placeholder="Buscar empresa…"
                            class="w-full mb-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-[#FFE600] focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40"
                            autocomplete="off"
                        >
                        <datalist id="datalist-empresas-sin-ejecutivo">
                            @foreach($unassignedCompaniesForDatalist as $coD)
                                <option value="{{ $coD->nombre_comercial }}"></option>
                            @endforeach
                        </datalist>
                        <div id="picklist-empresas-asig" class="exec-assignment-picklist max-h-[min(22rem,55vh)] overflow-y-auto rounded-xl border-2 border-gray-200 bg-white p-2 space-y-0.5 shadow-inner">
                            @foreach($allCompanies as $co)
                                <label
                                    class="exec-assignment-picklist__row flex items-start gap-3 p-2.5 rounded-lg hover:bg-gray-50 cursor-pointer text-sm border border-transparent hover:border-gray-200"
                                    style="color: #000000;"
                                    x-show="matches(@js($co->nombre_comercial . (($co->assigned_user_id && $co->assigned_user_id !== $executive->id) ? ' otro ejecutivo' : '')))"
                                >
                                    <input
                                        type="checkbox"
                                        name="company_ids[]"
                                        value="{{ $co->id }}"
                                        class="js-cb-empresa mt-0.5 h-4 w-4 rounded border-gray-300 text-[#071A3D] focus:ring-[#FFE600]"
                                        @checked($co->assigned_user_id === $executive->id)
                                    >
                                    <span class="leading-snug font-medium" style="color: #000000;">
                                        {{ $co->nombre_comercial }}
                                        @if($co->assigned_user_id && $co->assigned_user_id !== $executive->id)
                                            <span class="block text-xs font-medium mt-0.5" style="color: #92400e;">Asignada a otro ejecutivo</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-white/80 mt-3 leading-snug">Marque las empresas de la cartera de este ejecutivo. El desplegable del campo de búsqueda solo lista <span class="text-[#FFE600] font-medium">empresas aún sin ejecutivo asignado</span> (para ubicarlas rápido).</p>
                    </div>

                    {{-- Contactos: casillas + búsqueda + transferir a otro ejecutivo (estado Alpine en el contenedor padre) --}}
                    <div class="rounded-2xl border-2 border-[#FFE600]/35 bg-[#0d2744]/80 p-4 shadow-lg">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                            <span class="text-sm font-bold text-[#FFE600]">Contactos</span>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="marcarTodasContactos(true)" class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white/10 text-[#FFE600] hover:bg-white/20">Marcar todas</button>
                                <button type="button" @click="marcarTodasContactos(false)" class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white/10 text-white/90 hover:bg-white/20">Quitar todas</button>
                            </div>
                        </div>
                        <label class="sr-only" for="buscar-contacto-asig">Buscar contacto</label>
                        <input
                            id="buscar-contacto-asig"
                            type="search"
                            x-model="q"
                            list="datalist-contactos-sin-ejecutivo"
                            placeholder="Buscar por nombre o empresa…"
                            class="w-full mb-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-[#FFE600] focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40"
                            autocomplete="off"
                        >
                        <datalist id="datalist-contactos-sin-ejecutivo">
                            @foreach($unassignedContactsForDatalist as $ctD)
                                @php
                                    $lineaDatalist = $ctD->nombre_completo.' — '.($ctD->company?->nombre_comercial ?? '—');
                                @endphp
                                <option value="{{ $lineaDatalist }}"></option>
                            @endforeach
                        </datalist>
                        <div id="picklist-contactos-asig" class="exec-assignment-picklist max-h-[min(22rem,55vh)] overflow-y-auto rounded-xl border-2 border-gray-200 bg-white p-2 space-y-0.5 shadow-inner">
                            @foreach($allContacts as $ct)
                                @php
                                    $contactoLinea = $ct->nombre_completo.' — '.($ct->company?->nombre_comercial ?? '—');
                                    if ($ct->assigned_user_id && $ct->assigned_user_id !== $executive->id) {
                                        $contactoLinea .= ' otro ejecutivo';
                                    }
                                @endphp
                                <div
                                    class="flex items-start gap-2 p-2.5 rounded-lg hover:bg-gray-50 text-sm text-gray-900 border border-transparent hover:border-gray-200"
                                    x-show="matches(@js($contactoLinea))"
                                >
                                    <label class="flex flex-1 min-w-0 items-start gap-3 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="contact_ids[]"
                                            value="{{ $ct->id }}"
                                            class="js-cb-contacto mt-0.5 h-4 w-4 shrink-0 rounded border-gray-300 text-[#071A3D] focus:ring-[#FFE600]"
                                            @checked($ct->assigned_user_id === $executive->id)
                                        >
                                        <span class="leading-snug min-w-0">
                                            <span class="font-medium text-gray-900">{{ $ct->nombre_completo }}</span>
                                            <span class="text-gray-600"> — {{ $ct->company?->nombre_comercial ?? '—' }}</span>
                                            @if($ct->assigned_user_id && $ct->assigned_user_id !== $executive->id)
                                                <span class="block text-xs text-amber-700 font-medium mt-0.5">Asignado a otro ejecutivo</span>
                                            @endif
                                        </span>
                                    </label>
                                    @if($otherExecutives->isNotEmpty() && (int) $ct->assigned_user_id === (int) $executive->id)
                                        <button
                                            type="button"
                                            @click.stop.prevent="openTransferContact({{ $ct->id }})"
                                            class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-lg bg-[#071A3D] text-[#FFE600] border border-[#FFE600]/60 hover:bg-[#0f2744] focus:outline-none focus:ring-2 focus:ring-[#FFE600]/50"
                                        >
                                            Transferir
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-white/80 mt-3 leading-snug">Los contactos pueden asignarse con independencia de la empresa. El desplegable del campo de búsqueda solo lista <span class="text-[#FFE600] font-medium">contactos aún sin ejecutivo asignado</span>. Use <span class="text-[#FFE600] font-medium">Transferir</span> para pasar un contacto de la cartera de este ejecutivo a otro sin guardar todo el formulario.</p>
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-3 pt-2 border-t border-white/10">
                    <button type="submit" class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-[#FFE600] text-[#071A3D] text-sm font-bold shadow-lg hover:bg-[#ffeb3b] border border-[#fff9c4]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Guardar asignaciones
                    </button>
                </div>
            </form>

            {{-- Modal: elegir ejecutivo destino (fuera del formulario PATCH para HTML válido) --}}
            <div
                x-show="transferOpen"
                x-cloak
                x-transition
                class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/65 backdrop-blur-sm"
                role="dialog"
                aria-modal="true"
                aria-labelledby="transfer-contact-modal-title"
                @keydown.escape.window="transferOpen = false"
            >
                <div class="absolute inset-0" @click="transferOpen = false" aria-hidden="true"></div>
                <div
                    class="relative w-full max-w-md rounded-2xl border-4 border-[#FFE600] bg-gradient-to-b from-[#1a3d6b] to-[#0f2850] shadow-2xl p-6 text-left"
                    @click.stop
                >
                    <h3 id="transfer-contact-modal-title" class="text-lg font-bold text-[#FFE600] mb-1">Transferir contacto</h3>
                    <p class="text-sm text-white/85 mb-4">Elija el ejecutivo que recibirá este contacto en su cartera.</p>
                    <form method="POST" action="{{ route('executives.transfer-contact', $executive) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="contact_id" x-bind:value="transferContactId">
                        <div>
                            <label for="transfer-contact-to-user" class="block text-xs font-semibold text-[#FFE600] mb-1.5">Ejecutivo destino</label>
                            <select
                                id="transfer-contact-to-user"
                                name="to_user_id"
                                x-model="transferToUserId"
                                required
                                class="w-full rounded-xl border-2 border-gray-200 bg-white text-gray-900 text-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-[#FFE600] [&>option]:text-gray-900"
                            >
                                <option value="">Seleccione un ejecutivo…</option>
                                @foreach($otherExecutives as $ex)
                                    <option value="{{ $ex->id }}">{{ $ex->name }} — {{ $ex->email }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('to_user_id')" class="mt-1 text-amber-200 text-xs" />
                        </div>
                        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:justify-end pt-2">
                            <button type="button" class="px-4 py-2.5 rounded-xl border-2 border-white/35 text-white text-sm font-medium hover:bg-white/10 w-full sm:w-auto" @click="transferOpen = false">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="px-5 py-2.5 rounded-xl font-bold bg-[#FFE600] text-[#071A3D] text-sm hover:bg-[#ffeb3b] w-full sm:w-auto disabled:opacity-45 disabled:cursor-not-allowed"
                                :disabled="!transferToUserId"
                            >
                                Confirmar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>

        @can('companies.create')
        {{-- Importar base Excel asignada a este ejecutivo (misma lógica que Empresas) --}}
        <div class="panel-card-dark" x-data="{ fileLabel: 'Ningún archivo seleccionado' }">
            <h3 class="text-center text-base font-bold text-white mb-1">Archivo Excel</h3>
            <p class="text-center text-xs text-white/70 mb-5">Importación masiva de empresas y contactos para la cartera de este ejecutivo</p>
            <form
                action="{{ route('companies.import') }}"
                method="POST"
                enctype="multipart/form-data"
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 lg:gap-6"
            >
                @csrf
                <input type="hidden" name="assign_to_user_id" value="{{ $executive->id }}">

                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 flex-1 min-w-0">
                    <label class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[#FFE600] text-[#071A3D] text-sm font-bold shadow-[0_4px_14px_rgba(0,0,0,0.2)] cursor-pointer hover:bg-[#ffeb3b] transition-colors border border-[#fff9c4] shrink-0">
                        <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span>Seleccionar archivo</span>
                        <input
                            type="file"
                            name="file"
                            class="hidden"
                            accept=".xlsx,.xls,.csv"
                            required
                            @change="fileLabel = $event.target.files[0]?.name || 'Ningún archivo seleccionado'"
                        >
                    </label>
                    <p class="text-sm text-white/95 truncate min-w-0" x-text="fileLabel" title=""></p>
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-[#FFE600] text-[#071A3D] text-sm font-bold shadow-[0_4px_14px_rgba(0,0,0,0.2)] hover:bg-[#ffeb3b] transition-colors border border-[#fff9c4] shrink-0"
                >
                    <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                    </svg>
                    Importar base Excel
                </button>
            </form>
            <p class="text-xs text-white/80 mt-5 text-center leading-relaxed max-w-2xl mx-auto">
                Mismo formato que la carga en <span class="text-[#FFE600] font-semibold">Empresas</span> (filas con Área de trabajo = EMPRESA, etc.). Los registros quedan vinculados a <span class="text-white font-semibold">{{ $executive->name }}</span> como ejecutivo asignado.
            </p>
        </div>
        @endcan

        {{-- Empresas: mismo panel que Contactos (listado resumido al final) --}}
        <div class="relative rounded-2xl bg-[#071A3D] shadow-[0_8px_28px_rgba(0,0,0,0.22)] border border-[#1a3d6b]/50 overflow-hidden pl-1 border-l-[5px] border-l-[#FFE600]">
            <div class="bg-[#071A3D] px-4 sm:px-6 pt-5 pb-4 border-b border-white/10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="text-lg sm:text-xl font-bold text-[#FFE600] tracking-tight">Empresas</h3>
                    @can('companies.create')
                        <a
                            href="{{ route('companies.create') }}"
                            class="inline-flex items-center justify-center gap-1.5 self-start sm:self-auto px-4 py-2.5 rounded-full bg-[#FFE600] text-[#071A3D] text-sm font-bold shadow-md hover:bg-[#ffeb3b] transition-colors border border-[#fff9c4] shrink-0"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Nueva Empresa
                        </a>
                    @endcan
                </div>
            </div>
            <div class="px-4 sm:px-6 py-5 space-y-3">
                @forelse($executive->assignedCompanies as $company)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-xl bg-[#0f2744]/95 border border-[#1e3a5f]/80 border-l-[6px] border-l-[#FFE600] pl-4 pr-3 py-3.5 shadow-inner">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-white text-sm sm:text-base leading-snug">{{ $company->nombre_comercial }}</p>
                            <div class="mt-1.5 space-y-0.5 text-xs sm:text-sm text-white/65">
                                @if($company->rfc)
                                    <p>RFC: {{ $company->rfc }}</p>
                                @endif
                                @if($company->municipio || $company->estado)
                                    <p>{{ trim(($company->municipio ?? '') . ', ' . ($company->estado ?? ''), ' ,') }}</p>
                                @endif
                                @if($company->sector)
                                    <p>{{ $company->sector }}</p>
                                @endif
                            </div>
                        </div>
                        <a
                            href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $company)) }}"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold bg-[#4a5568] text-[#FFE600] hover:bg-[#5a6578] transition-colors shrink-0 border border-white/10"
                        >
                            Ver
                        </a>
                    </div>
                @empty
                    <p class="text-sm text-white/70 text-center py-6">No hay empresas vinculadas a la cartera de este ejecutivo.</p>
                @endforelse
            </div>
        </div>

        {{-- Contactos: listado unificado al final (estilo panel con ribete amarillo) --}}
        <div class="relative rounded-2xl bg-[#071A3D] shadow-[0_8px_28px_rgba(0,0,0,0.22)] border border-[#1a3d6b]/50 overflow-hidden pl-1 border-l-[5px] border-l-[#FFE600]">
            <div class="bg-[#071A3D] px-4 sm:px-6 pt-5 pb-4 border-b border-white/10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="text-lg sm:text-xl font-bold text-[#FFE600] tracking-tight">Contactos</h3>
                    @can('contacts.create')
                        <a
                            href="{{ route('contacts.create') }}"
                            class="inline-flex items-center justify-center gap-1.5 self-start sm:self-auto px-4 py-2.5 rounded-full bg-[#FFE600] text-[#071A3D] text-sm font-bold shadow-md hover:bg-[#ffeb3b] transition-colors border border-[#fff9c4] shrink-0"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Nuevo Contacto
                        </a>
                    @endcan
                </div>
            </div>
            <div class="px-4 sm:px-6 py-5 space-y-3">
                @forelse($unifiedContactsForList as $contact)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-xl bg-[#0f2744]/95 border border-[#1e3a5f]/80 border-l-[6px] border-l-[#FFE600] pl-4 pr-3 py-3.5 shadow-inner">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-white text-sm sm:text-base leading-snug">{{ $contact->nombre_completo }}</p>
                            <div class="mt-1.5 space-y-0.5 text-xs sm:text-sm text-white/65">
                                @if($contact->puesto_de_trabajo)
                                    <p>{{ $contact->puesto_de_trabajo }}</p>
                                @endif
                                <p class="truncate" title="{{ ($contact->email_activo ?? true) ? ($contact->email ?? '') : '' }}">
                                    {{ ($contact->email_activo ?? true) ? ($contact->email ?? '—') : '—' }}
                                </p>
                                <p>{{ $contact->celular ?? $contact->telefono ?? '—' }}</p>
                            </div>
                        </div>
                        <a
                            href="{{ \App\Support\CrmNavigation::withReturn(route('contacts.show', $contact)) }}"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold bg-[#4a5568] text-[#FFE600] hover:bg-[#5a6578] transition-colors shrink-0 border border-white/10"
                        >
                            Ver
                        </a>
                    </div>
                @empty
                    <p class="text-sm text-white/70 text-center py-6">No hay contactos vinculados a la cartera de este ejecutivo.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
