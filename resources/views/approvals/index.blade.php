<x-app-layout>
    <x-slot name="header">
        <div class="page-header-card__icon" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <div>
            <h2 class="page-header-card__title text-[#FFE600] font-bold">Solicitudes pendientes</h2>
            <p class="page-header-card__subtitle text-[#FFE600]/90 font-semibold">Autorizar o denegar altas de empresas, eliminaciones solicitadas y registros de usuarios</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        {{-- Recuadro: contador a la izquierda, pestañas a la derecha --}}
        <div class="panel-card-dark">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-white shrink-0">
                    <span class="text-base font-semibold">Total de solicitudes pendientes:</span>
                    <span class="inline-flex items-center justify-center min-w-[1.75rem] h-8 px-2.5 rounded-lg text-base font-bold bg-[#FFE600] text-[#003366]">{{ $totalPendientes ?? 0 }}</span>
                </div>
                <div class="approval-tabs flex gap-1 p-1 rounded-xl mb-0 shrink-0">
                @can('companies.approve')
                <a href="{{ route('approvals.index', ['tab' => 'empresas']) }}"
                   class="flex-1 sm:flex-none px-4 py-2.5 rounded-lg text-sm font-medium transition {{ $tab === 'empresas' ? 'approval-tabs__item--active' : 'approval-tabs__item' }}">
                    Empresas
                    @if(($companiesCount ?? 0) > 0)
                        <span class="ml-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-xs font-bold bg-red-500 text-white">{{ $companiesCount }}</span>
                    @endif
                </a>
                @endcan
                @can('users.approve')
                <a href="{{ route('approvals.index', ['tab' => 'usuarios']) }}"
                   class="flex-1 sm:flex-none px-4 py-2.5 rounded-lg text-sm font-medium transition {{ $tab === 'usuarios' ? 'approval-tabs__item--active' : 'approval-tabs__item' }}">
                    Usuarios
                    @if(($usersCount ?? 0) > 0)
                        <span class="ml-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-xs font-bold bg-red-500 text-white">{{ $usersCount }}</span>
                    @endif
                </a>
                @endcan
                @can('companies.approve')
                <a href="{{ route('approvals.index', ['tab' => 'contactos']) }}"
                   class="flex-1 sm:flex-none px-4 py-2.5 rounded-lg text-sm font-medium transition {{ $tab === 'contactos' ? 'approval-tabs__item--active' : 'approval-tabs__item' }}">
                    Contactos
                    @if(($contactsCount ?? 0) > 0)
                        <span class="ml-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-xs font-bold bg-red-500 text-white">{{ $contactsCount }}</span>
                    @endif
                </a>
                @endcan
                </div>
            </div>
        </div>

        {{-- Contenido según pestaña activa --}}
        @if($tab === 'empresas')
        @can('companies.approve')
        <div class="panel-card-dark p-0 overflow-hidden">
            @if($companies->count() > 0)
                <div>
                    @foreach($companies as $company)
                    <div class="approval-request-card">
                        <div class="px-4 sm:px-5 py-4 flex flex-wrap items-start justify-between gap-4">
                            @if($company->deletion_pending)
                            <div class="flex-1 min-w-0 space-y-2">
                                <p class="approval-request-card__header">Eliminación de empresa solicitada</p>
                                <p class="text-sm text-white/90">
                                    El usuario <strong class="text-[#FFE600]">{{ $company->deletionRequester?->name ?? $company->creator?->name ?? 'N/D' }}</strong>
                                    solicita dar de baja la siguiente empresa:
                                </p>
                                <dl class="text-sm space-y-1 mt-2">
                                    <div><span class="approval-request-card__label">Nombre comercial:</span> <span class="text-white">{{ $company->nombre_comercial }}</span></div>
                                    <div><span class="approval-request-card__label">RFC:</span> {{ $company->rfc ?? '—' }}</div>
                                    <div><span class="approval-request-card__label">Solicitud el:</span> {{ $company->deletion_requested_at?->format('d/m/Y H:i') ?? '—' }}</div>
                                </dl>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                                <form method="POST" id="approval-co-del-app-{{ $company->id }}" action="{{ route('approvals.companies.approve-deletion', $company) }}" class="inline">
                                    @csrf
                                    <button type="button" class="btn-approve-amber js-approval-confirm-trigger"
                                        data-form-id="approval-co-del-app-{{ $company->id }}"
                                        data-title="Confirmar eliminación"
                                        data-message="¿Confirmar eliminación definitiva de esta empresa?"
                                        data-variant="amber"
                                        data-confirm-text="Sí, aprobar">
                                        Aprobar eliminación
                                    </button>
                                </form>
                                <form method="POST" id="approval-co-del-den-{{ $company->id }}" action="{{ route('approvals.companies.deny-deletion', $company) }}" class="inline flex items-center gap-2">
                                    @csrf
                                    <button type="button" class="px-4 py-2 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-500 transition js-deny-deletion-trigger"
                                        data-form-id="approval-co-del-den-{{ $company->id }}">
                                        Denegar eliminación
                                    </button>
                                </form>
                            </div>
                            @else
                            <div class="flex-1 min-w-0 space-y-2">
                                <p class="approval-request-card__header">Nuevo registro solicitado</p>
                                <p class="text-sm text-white/90">
                                    El usuario <strong class="text-[#FFE600]">{{ $company->creator?->name ?? 'N/D' }}</strong> solicita registrar la siguiente empresa:
                                </p>
                                <dl class="text-sm space-y-1 mt-2">
                                    <div><span class="approval-request-card__label">Nombre comercial:</span> <span class="text-white">{{ $company->nombre_comercial }}</span></div>
                                    <div><span class="approval-request-card__label">RFC:</span> {{ $company->rfc ?? '—' }}</div>
                                    <div><span class="approval-request-card__label">Sector:</span> {{ $company->sector ?? '—' }}</div>
                                    <div><span class="approval-request-card__label">Fecha y hora:</span> {{ $company->created_at->format('d/m/Y H:i') }}</div>
                                </dl>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                                <form method="POST" action="{{ route('approvals.companies.approve', $company) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-approve-amber">
                                        Aprobar
                                    </button>
                                </form>
                                <form method="POST" id="approval-co-reg-den-{{ $company->id }}" action="{{ route('approvals.companies.deny', $company) }}" class="inline flex items-center gap-2">
                                    @csrf
                                    <input type="text" name="motivo" placeholder="Motivo (opcional)" class="px-2 py-1.5 rounded text-sm bg-white/10 text-white placeholder-white/50 border border-white/20 w-40">
                                    <button type="button" class="px-4 py-2 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-500 transition js-approval-confirm-trigger"
                                        data-form-id="approval-co-reg-den-{{ $company->id }}"
                                        data-title="Denegar solicitud"
                                        data-message="¿Denegar esta solicitud de registro de empresa?"
                                        data-variant="danger"
                                        data-confirm-text="Sí, denegar">
                                        Denegar
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="px-4 py-4 border-t border-white/20">
                    {{ $companies->withQueryString()->links() }}
                </div>
            @else
                <p class="text-center text-white py-8 px-4">No hay solicitudes de empresas (altas o eliminaciones) pendientes.</p>
            @endif
        </div>
        @endcan
        @endif

        @if($tab === 'usuarios')
        @can('users.approve')
        <div class="panel-card-dark p-0 overflow-hidden">
            @if($users->count() > 0)
                <div>
                    @foreach($users as $user)
                    <div class="approval-request-card">
                        <div class="px-4 sm:px-5 py-4 flex flex-wrap items-start justify-between gap-4">
                            <div class="flex-1 min-w-0 space-y-2">
                                <p class="approval-request-card__header">Nuevo registro solicitado</p>
                                <dl class="text-sm space-y-1">
                                    <div><span class="approval-request-card__label">Nombre:</span> <span class="text-white">{{ $user->name }}</span></div>
                                    <div><span class="approval-request-card__label">Correo:</span> {{ $user->email }}</div>
                                    <div><span class="approval-request-card__label">Fecha y hora:</span> {{ $user->created_at->format('d/m/Y H:i') }}</div>
                                    <div>
                                        <span class="approval-request-card__label">Rol:</span>
                                        @if($user->roles->isNotEmpty())
                                            @foreach($user->roles as $role)
                                                <span class="px-2 py-0.5 bg-white/20 text-white rounded text-xs">{{ ucfirst($role->name) }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-white/50 italic">Sin rol asignado</span>
                                        @endif
                                    </div>
                                </dl>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                                <form method="POST" action="{{ route('approvals.users.approve', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-approve-amber">
                                        Aprobar
                                    </button>
                                </form>
                                <form method="POST" id="approval-us-den-{{ $user->id }}" action="{{ route('approvals.users.deny', $user) }}" class="inline">
                                    @csrf
                                    <button type="button" class="px-4 py-2 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-500 transition js-approval-confirm-trigger"
                                        data-form-id="approval-us-den-{{ $user->id }}"
                                        data-title="Denegar registro"
                                        data-message="¿Denegar y eliminar este registro? El usuario deberá registrarse nuevamente si desea intentarlo."
                                        data-variant="danger"
                                        data-confirm-text="Sí, denegar">
                                        Denegar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="px-4 py-4 border-t border-white/20">
                    {{ $users->withQueryString()->links() }}
                </div>
            @else
                <p class="text-center text-white py-8 px-4">No hay usuarios pendientes de aprobación.</p>
            @endif
        </div>
        @endcan
        @endif

        @if($tab === 'contactos')
        @can('companies.approve')
        <div class="panel-card-dark p-0 overflow-hidden">
            @if($contacts->count() > 0)
                <div>
                    @foreach($contacts as $contact)
                    <div class="approval-request-card">
                        <div class="px-4 sm:px-5 py-4 flex flex-wrap items-start justify-between gap-4">
                            @if($contact->deletion_pending)
                            <div class="flex-1 min-w-0 space-y-2">
                                <p class="approval-request-card__header">Eliminación de contacto solicitada</p>
                                <p class="text-sm text-white/90">
                                    El usuario <strong class="text-[#FFE600]">{{ $contact->deletionRequester?->name ?? $contact->creator?->name ?? 'N/D' }}</strong>
                                    solicita dar de baja el siguiente contacto:
                                </p>
                                <dl class="text-sm space-y-1 mt-2">
                                    <div><span class="approval-request-card__label">Nombre:</span> <span class="text-white">{{ $contact->nombre_completo }}</span></div>
                                    <div><span class="approval-request-card__label">Empresa:</span> {{ $contact->company?->nombre_comercial ?? '—' }}</div>
                                    <div><span class="approval-request-card__label">Solicitud el:</span> {{ $contact->deletion_requested_at?->format('d/m/Y H:i') ?? '—' }}</div>
                                    <div><span class="approval-request-card__label">Motivo:</span> {{ $contact->deletion_reason ?? '—' }}</div>
                                </dl>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                                <form method="POST" id="approval-ct-del-app-{{ $contact->id }}" action="{{ route('approvals.contacts.approve-deletion', $contact) }}" class="inline">
                                    @csrf
                                    <button type="button" class="btn-approve-amber js-approval-confirm-trigger"
                                        data-form-id="approval-ct-del-app-{{ $contact->id }}"
                                        data-title="Confirmar eliminación"
                                        data-message="¿Confirmar eliminación definitiva de este contacto?"
                                        data-variant="amber"
                                        data-confirm-text="Sí, aprobar">
                                        Aprobar eliminación
                                    </button>
                                </form>
                                <form method="POST" id="approval-ct-del-den-{{ $contact->id }}" action="{{ route('approvals.contacts.deny-deletion', $contact) }}" class="inline">
                                    @csrf
                                    <button type="button" class="px-4 py-2 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-500 transition js-deny-deletion-trigger"
                                        data-form-id="approval-ct-del-den-{{ $contact->id }}">
                                        Denegar eliminación
                                    </button>
                                </form>
                            </div>
                            @else
                            <div class="flex-1 min-w-0 space-y-2">
                                <p class="approval-request-card__header">Nuevo contacto solicitado</p>
                                <dl class="text-sm space-y-1 mt-2">
                                    <div><span class="approval-request-card__label">Nombre:</span> <span class="text-white">{{ $contact->nombre_completo }}</span></div>
                                    <div><span class="approval-request-card__label">Correo:</span> {{ $contact->email ?? '—' }}</div>
                                    <div><span class="approval-request-card__label">Empresa:</span> {{ $contact->company?->nombre_comercial ?? '—' }}</div>
                                    <div><span class="approval-request-card__label">Solicitado por:</span> {{ $contact->creator?->name ?? 'N/D' }}</div>
                                    <div><span class="approval-request-card__label">Fecha y hora:</span> {{ $contact->created_at->format('d/m/Y H:i') }}</div>
                                </dl>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                                <form method="POST" action="{{ route('approvals.contacts.approve', $contact) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-approve-amber">
                                        Aprobar
                                    </button>
                                </form>
                                <form method="POST" id="approval-ct-reg-den-{{ $contact->id }}" action="{{ route('approvals.contacts.deny', $contact) }}" class="inline flex items-center gap-2">
                                    @csrf
                                    <input type="text" name="motivo" placeholder="Motivo (opcional)" class="px-2 py-1.5 rounded text-sm bg-white/10 text-white placeholder-white/50 border border-white/20 w-40">
                                    <button type="button" class="px-4 py-2 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-500 transition js-approval-confirm-trigger"
                                        data-form-id="approval-ct-reg-den-{{ $contact->id }}"
                                        data-title="Denegar contacto"
                                        data-message="¿Denegar este contacto y rechazar su registro?"
                                        data-variant="danger"
                                        data-confirm-text="Sí, denegar">
                                        Denegar
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="px-4 py-4 border-t border-white/20">
                    {{ $contacts->withQueryString()->links() }}
                </div>
            @else
                <p class="text-center text-white py-8 px-4">No hay solicitudes de contactos (altas o eliminaciones) pendientes.</p>
            @endif
        </div>
        @endcan
        @endif
    </div>

    @include('approvals.partials.approval-confirm-modal')
    @include('approvals.partials.approval-deny-deletion-modal')
</x-app-layout>
