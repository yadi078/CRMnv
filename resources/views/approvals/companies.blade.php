<x-app-layout>
    <x-slot name="header">
        <x-page-header-avatar><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg></x-page-header-avatar>
        <div>
            <h2 class="page-header-card__title">Aprobaciones Pendientes - Empresas</h2>
            <p class="page-header-card__subtitle">Altas pendientes y eliminaciones solicitadas</p>
        </div>
        @can('users.approve')
        <a href="{{ route('approvals.users') }}" class="btn-panel-dark ml-auto">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Aprobaciones de usuarios
            </a>
        @endcan
    </x-slot>

    <div class="space-y-8">
        <div class="panel-card-dark p-0 overflow-hidden divide-y divide-white/10">
                @if($companies->count() > 0)
                    @foreach($companies as $company)
                    <div class="px-4 sm:px-5 py-4 flex flex-wrap items-start justify-between gap-4 hover:bg-white/5">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-white">{{ $company->nombre_comercial }}</h3>
                            <p class="text-sm text-white/80">RFC: {{ $company->rfc ?? '-' }}</p>
                            @if($company->deletion_pending)
                                <p class="text-sm text-white/70 mt-1"><span class="text-[#FFE600] font-semibold">Eliminación solicitada</span> por <strong class="text-[#FFE600]">{{ $company->deletionRequester?->name ?? 'N/D' }}</strong> el {{ $company->deletion_requested_at?->format('d/m/Y H:i') ?? '—' }}</p>
                            @else
                                <p class="text-sm text-white/70">Creado por: <strong class="text-[#FFE600]">{{ $company->creator?->name ?? 'N/D' }}</strong> el {{ $company->created_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            @if($company->deletion_pending)
                            <form method="POST" id="approval-legacy-co-del-app-{{ $company->id }}" action="{{ route('approvals.companies.approve-deletion', $company) }}" class="inline">
                                @csrf
                                <button type="button" class="btn-panel-dark bg-emerald-600 hover:bg-emerald-500 text-white border-0 js-approval-confirm-trigger"
                                    data-form-id="approval-legacy-co-del-app-{{ $company->id }}"
                                    data-title="Confirmar eliminación"
                                    data-message="¿Confirmar eliminación definitiva de esta empresa?"
                                    data-variant="amber"
                                    data-confirm-text="Sí, aprobar">
                                    Aprobar eliminación
                                </button>
                            </form>
                            <form method="POST" id="approval-legacy-co-del-den-{{ $company->id }}" action="{{ route('approvals.companies.deny-deletion', $company) }}" class="inline">
                                @csrf
                                <button type="button" class="px-4 py-2 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-500 transition js-deny-deletion-trigger"
                                    data-form-id="approval-legacy-co-del-den-{{ $company->id }}">
                                    Denegar eliminación
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('approvals.companies.approve', $company) }}" class="inline">
                                @csrf
                                <button type="submit" class="btn-panel-dark bg-emerald-600 hover:bg-emerald-500 text-white border-0">
                                    Aceptar
                                </button>
                            </form>
                            <form method="POST" id="approval-legacy-co-reg-den-{{ $company->id }}" action="{{ route('approvals.companies.deny', $company) }}" class="inline flex items-center gap-2">
                                @csrf
                                <input type="text" name="motivo" placeholder="Motivo (opcional)" class="px-2 py-1.5 rounded text-sm bg-white/10 text-white placeholder-white/50 border border-white/20 w-40">
                                <button type="button" class="px-4 py-2 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-500 transition js-approval-confirm-trigger"
                                    data-form-id="approval-legacy-co-reg-den-{{ $company->id }}"
                                    data-title="Denegar solicitud"
                                    data-message="¿Denegar esta solicitud de registro de empresa?"
                                    data-variant="danger"
                                    data-confirm-text="Sí, denegar">
                                    Denegar
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach

                <div class="px-4 py-4 border-t border-white/10">
                    {{ $companies->links() }}
                </div>
                @else
                <p class="text-center text-white py-12 px-4">No hay solicitudes de empresas pendientes</p>
                @endif
        </div>
    </div>

    @include('approvals.partials.approval-confirm-modal')
    @include('approvals.partials.approval-deny-deletion-modal')
</x-app-layout>
