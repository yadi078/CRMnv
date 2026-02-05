<x-app-user-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Bienvenido, {{ Auth::user()->name }}</h2>
                    <p class="text-sm text-white/80 mt-0.5">Tablero principal — Vista general al ingresar</p>
                </div>
            </div>
            <a href="{{ route('companies.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-900 bg-amber-400 hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-[#1e293b] transition-all shadow-sm flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva empresa o contacto
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 bg-gray-50/80">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Resumen de Actividad: 3 cajas como en la imagen --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Resumen de Actividad</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('follow-ups.index', ['completado' => 0]) }}" class="flex items-center gap-4 p-5 rounded-xl bg-white border border-gray-200 shadow-sm hover:shadow-md hover:border-amber-200 transition-all no-underline">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-2xl font-bold text-gray-900">{{ $seguimientosPendientes }}</p>
                            <p class="text-sm font-medium text-gray-600 flex items-center gap-1.5">
                                Seguimientos pendientes
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </p>
                        </div>
                    </a>
                    <div class="flex items-center gap-4 p-5 rounded-xl bg-white border border-gray-200 shadow-sm">
                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-2xl font-bold text-gray-900">{{ $alarmasProgramadas }}</p>
                            <p class="text-sm font-medium text-gray-600 flex items-center gap-1.5">
                                Alarmas programadas
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('companies.index') }}" class="flex items-center gap-4 p-5 rounded-xl bg-white border-l-4 border-l-red-500 border border-gray-200 shadow-sm hover:shadow-md hover:border-red-200 transition-all no-underline">
                        <div class="w-14 h-14 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-2xl font-bold"><span class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded bg-red-500 text-white text-lg">{{ $solicitudesPendientes }}</span></p>
                            <p class="text-sm font-medium text-gray-600 mt-1">Solicitudes Pendientes</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Mis Empresas --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Mis Empresas</h3>
                    <form method="GET" action="{{ route('user.dashboard') }}" class="mb-4">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </span>
                            <input type="text" name="q_empresas" value="{{ request('q_empresas') }}" placeholder="Buscar empresa..." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 py-2.5 pl-10 pr-3 text-sm">
                        </div>
                    </form>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Nombre</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Sector</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Contacto Principal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($misEmpresas as $empresa)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($empresa->approval_status === 'pendiente')
                                            <span class="w-2 h-2 rounded-full bg-amber-500 flex-shrink-0" title="Pendiente"></span>
                                            @endif
                                            <a href="{{ route('companies.show', $empresa) }}" class="font-medium text-gray-900 hover:text-amber-600">{{ $empresa->nombre_comercial }}</a>
                                        </div>
                                        @if($empresa->approval_status === 'pendiente')
                                        <p class="text-xs text-amber-600 mt-0.5">Pendiente</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $empresa->sector ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        @php $contactoPrincipal = $empresa->contacts->first(); @endphp
                                        @if($contactoPrincipal)
                                        <span>{{ $contactoPrincipal->nombre_completo }}</span>
                                        @if($contactoPrincipal->celular)
                                        <span class="text-gray-500 flex items-center gap-1 mt-0.5">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                            {{ $contactoPrincipal->celular }}
                                        </span>
                                        @endif
                                        @else
                                        —
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-500">No hay empresas registradas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-3 text-xs text-gray-500">Empresas en estado Pendiente (aún no aprobadas por el administrador).</p>
                    <a href="{{ route('companies.index') }}" class="mt-3 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">Ver todas las empresas →</a>
                </div>

                {{-- Mis Contactos --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Mis Contactos</h3>
                    @if($misContactos->isEmpty())
                    <p class="text-gray-600 mb-4">Aún no has agregado contactos.</p>
                    <a href="{{ route('contacts.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Añadir Nuevo Contacto
                    </a>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Nombre</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Empresa</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Contacto</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($misContactos->take(5) as $contacto)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('contacts.show', $contacto) }}" class="font-medium text-gray-900 hover:text-amber-600">{{ $contacto->nombre_completo }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $contacto->company->nombre_comercial ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $contacto->celular ?? $contacto->email ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('contacts.create') }}" class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Añadir Nuevo Contacto
                    </a>
                    <a href="{{ route('contacts.index') }}" class="mt-2 ml-3 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">Ver todos →</a>
                    @endif
                </div>
            </div>

            {{-- Historial de Ventas: dos tarjetas iguales --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Historial de Ventas</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Curso/Servicio</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Cliente</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Fecha</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay ventas registradas por el momento.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('user.sales.index') }}" class="mt-3 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">Ir a Historial de Ventas →</a>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Historial de Ventas</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Curso/Servicio</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Cliente</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Fecha</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay ventas registradas por el momento.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('user.sales.index') }}" class="mt-3 inline-block text-sm font-medium text-amber-600 hover:text-amber-700">Ir a Historial de Ventas →</a>
                </div>
            </div>
        </div>
    </div>
</x-app-user-layout>
