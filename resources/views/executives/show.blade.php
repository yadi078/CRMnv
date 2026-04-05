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

    <div class="space-y-6" x-data="executiveProfileSearch(@js($execSearchPayload))">
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

        <x-executive-portfolio-stats
            title="Resumen de asignaciones"
            :companies-count="$executive->assignedCompanies->count()"
            :contacts-count="$unifiedContactsForList->count()"
            footnote="Empresas y contactos asignados; cada contacto cuenta una sola vez en el total."
        />

        {{-- Empresas asignadas por estado (entidad federativa) --}}
        <div class="panel-card-dark">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent !mb-0 text-base sm:text-lg">Empresas por estado</h3>
            <p class="mt-1.5 mb-3 max-w-2xl text-xs leading-snug text-white/70">
                Cuántas empresas tiene en cada estado.
            </p>
            @if($empresasCountByEstado->isEmpty())
                <p class="text-sm text-white/75">Ninguna empresa asignada aún.</p>
            @else
                <div class="rounded-xl border border-white/15 bg-white/[0.06] p-3 sm:p-4">
                    <ul class="grid grid-cols-2 gap-2.5 sm:grid-cols-3 sm:gap-3 lg:grid-cols-4 text-sm">
                        @foreach($empresasCountByEstado as $row)
                            <li class="flex min-w-0 items-center justify-between gap-2 rounded-lg border border-white/10 bg-[#071A3D]/50 px-2.5 py-2 sm:px-3">
                                <span class="min-w-0 truncate text-white/90" title="{{ $row['estado'] }}">{{ $row['estado'] }}</span>
                                <span class="inline-flex shrink-0 min-w-[1.75rem] justify-center rounded-md bg-[#FFE600]/15 px-1.5 py-0.5 text-xs font-bold tabular-nums text-[#FFE600] ring-1 ring-[#FFE600]/35">{{ $row['count'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @if(isset($orphanAssignedContacts) && $orphanAssignedContacts->isNotEmpty())
                    <p class="mt-3 text-xs text-white/60 leading-relaxed">
                        Además, <span class="font-semibold text-[#FFE600]">{{ $orphanAssignedContacts->count() }}</span>
                        {{ $orphanAssignedContacts->count() === 1 ? 'contacto asignado' : 'contactos asignados' }}
                        con empresa distinta a las de arriba o sin empresa (no aparecen en el listado de empresas inferior).
                    </p>
                @endif
            @endif
        </div>

        @can('companies.create')
        {{-- Importar base Excel asignada a este ejecutivo (misma lógica que Empresas) --}}
        <div class="panel-card-dark" x-data="{ fileLabel: 'Ningún archivo seleccionado' }">
            <h3 class="text-center text-base font-bold text-white mb-1">Importar Excel</h3>
            <p class="text-center text-xs text-white/70 mb-5">Carga masiva para este ejecutivo</p>
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
                    Importar
                </button>
            </form>
            <p class="text-xs text-white/75 mt-4 text-center leading-snug max-w-xl mx-auto">
                Hace falta el <span class="text-[#FFE600] font-semibold">formato de plantilla</span> del CRM (el mismo que en Empresas). Lo importado queda asignado a <span class="font-semibold text-white">{{ $executive->name }}</span>.
            </p>
        </div>
        @endcan

        {{-- Buscar: solo empresas (fichas en cuadrícula); el nombre enlaza a la ficha --}}
        <div class="panel-card-dark !py-4 sm:!py-5">
            <h3 class="panel-card-dark__title panel-card-dark__title--accent !mb-1 text-base sm:text-lg">Buscar</h3>
            <p class="text-xs text-white/65 leading-snug max-w-2xl mb-3">
                Filtra por nombre comercial.
            </p>
            <div class="max-w-xl">
                <label for="exec-profile-search-empresa" class="block text-[11px] font-semibold text-[#FFE600] mb-1">Empresa</label>
                <input
                    id="exec-profile-search-empresa"
                    type="search"
                    autocomplete="off"
                    x-model.debounce.300ms="searchEmpresa"
                    placeholder="Nombre comercial…"
                    class="w-full rounded-lg border border-white/20 bg-white/10 text-white placeholder:text-white/35 text-sm py-2 px-2.5 focus:outline-none focus:ring-2 focus:ring-[#FFE600]/40"
                />
            </div>
            <div class="mt-2">
                <button
                    type="button"
                    class="text-[11px] font-semibold text-[#FFE600]/90 hover:text-white underline underline-offset-2"
                    x-show="searchEmpresa"
                    x-cloak
                    @click="searchEmpresa = ''"
                >
                    Limpiar búsqueda
                </button>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <h4 class="text-sm font-bold text-[#FFE600]">Empresas</h4>
                    @can('companies.create')
                        <a
                            href="{{ route('companies.create') }}"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#FFE600] text-[#071A3D] text-xs font-bold hover:bg-[#ffeb3b] border border-[#fff9c4]/80 shrink-0"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Nueva
                        </a>
                    @endcan
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    @forelse($executive->assignedCompanies as $company)
                        @php($contactosN = $company->contacts->count())
                        <div x-show="listEmpresaRowVisible(@js($company->nombre_comercial))" x-cloak>
                            <a
                                href="{{ \App\Support\CrmNavigation::withReturn(route('companies.show', $company)) }}"
                                class="flex h-full flex-col rounded-lg border border-white/10 bg-[#0a1f38]/85 px-2.5 py-2 transition-colors hover:border-[#FFE600]/40 hover:bg-[#0f2744]/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FFE600]/50"
                            >
                                <span class="font-semibold text-white text-sm leading-snug line-clamp-2">{{ $company->nombre_comercial }}</span>
                                <dl class="mt-1.5 space-y-0.5 text-[11px] text-white/60">
                                    <div class="flex gap-1">
                                        <dt class="shrink-0 text-white/45">Estado</dt>
                                        <dd class="min-w-0 truncate text-white/80" title="{{ trim((string) ($company->estado ?? '')) !== '' ? $company->estado : 'Sin estado' }}">{{ trim((string) ($company->estado ?? '')) !== '' ? $company->estado : 'Sin estado' }}</dd>
                                    </div>
                                    <div class="flex gap-1">
                                        <dt class="shrink-0 text-white/45">Estatus</dt>
                                        <dd class="min-w-0 truncate text-white/80" title="{{ $company->status_label }}">{{ $company->status_label }}</dd>
                                    </div>
                                    <div class="flex gap-1">
                                        <dt class="shrink-0 text-white/45">Contactos</dt>
                                        <dd class="tabular-nums text-white/80">{{ $contactosN }} {{ $contactosN === 1 ? 'contacto' : 'contactos' }}</dd>
                                    </div>
                                </dl>
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-white/55 py-2 sm:col-span-2 lg:col-span-3">No hay empresas asignadas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
