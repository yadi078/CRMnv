<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1">
            <x-page-header-avatar :user="$executive" :fallback-initials="true" :compact="true">
                <svg class="text-[#FFE600]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </x-page-header-avatar>
            <div class="min-w-0 flex-1">
                <h2 class="page-header-card__title">Perfil del ejecutivo</h2>
                <p class="page-header-card__subtitle truncate sm:whitespace-normal">{{ $executive->name }} — mismo usuario que inicia sesión en el CRM</p>
            </div>
        </div>
        <div class="flex flex-row flex-nowrap items-center justify-end gap-2 sm:gap-3 shrink-0">
            <x-executive-reminder-button :executive="$executive" />
            <form
                method="POST"
                action="{{ route('executives.destroy', $executive) }}"
                class="inline-flex m-0 shrink-0"
                onsubmit="return confirm('¿Eliminar al ejecutivo «{{ $executive->name }}»? Se quitarán las asignaciones de empresas y contactos y se borrará la cuenta de usuario.');"
            >
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 h-11 min-h-[44px] rounded-xl border-2 border-red-400/55 bg-red-950/35 px-3 sm:px-4 text-sm font-semibold text-red-100 hover:bg-red-900/45 hover:border-red-300/65 focus:outline-none focus:ring-2 focus:ring-red-400/45 whitespace-nowrap"
                >
                    <svg class="w-5 h-5 shrink-0 opacity-95" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span class="hidden min-[380px]:inline">Eliminar ejecutivo</span>
                    <span class="min-[380px]:hidden">Eliminar</span>
                </button>
            </form>
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
                    <div class="sm:col-span-2">
                        <dt class="text-white/60 font-medium mb-2">Estado de cuenta</dt>
                        <dd class="mt-0">
                            <p class="text-xs text-white/55 mb-2 max-w-xl">Activa: puede entrar al CRM. Inactiva: el acceso queda bloqueado hasta reactivar.</p>
                            <form
                                method="POST"
                                action="{{ route('executives.update-account-status', $executive) }}"
                                class="inline-flex flex-col xs:flex-row xs:items-start gap-2"
                            >
                                @csrf
                                @method('PATCH')
                                <div
                                    class="inline-flex rounded-xl border border-white/20 p-0.5 bg-black/25 shadow-inner gap-0.5"
                                    role="group"
                                    aria-label="Cambiar estado de cuenta"
                                >
                                    <button
                                        type="submit"
                                        name="is_active"
                                        value="1"
                                        class="min-w-[5.5rem] px-4 py-2 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FFE600] focus-visible:ring-offset-2 focus-visible:ring-offset-[#0f2744] {{ $executive->is_active ? 'bg-[#FFE600] text-[#071A3D] shadow-sm' : 'text-white/75 hover:text-white hover:bg-white/10' }}"
                                    >
                                        Activa
                                    </button>
                                    <button
                                        type="submit"
                                        name="is_active"
                                        value="0"
                                        class="min-w-[5.5rem] px-4 py-2 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FFE600] focus-visible:ring-offset-2 focus-visible:ring-offset-[#0f2744] {{ ! $executive->is_active ? 'bg-red-500/85 text-white shadow-sm' : 'text-white/75 hover:text-white hover:bg-white/10' }}"
                                    >
                                        Inactiva
                                    </button>
                                </div>
                            </form>
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
