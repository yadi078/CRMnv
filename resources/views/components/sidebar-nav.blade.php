{{--
  SidebarNav: Navegación vertical fija estilo smart-home / dashboard.
  Paleta azul rey, íconos circulares, estado activo con "wave" y glow.
  Logo de la empresa en la parte superior.
--}}
<nav
    class="sidebar-nav"
    role="navigation"
    aria-label="Navegación principal"
>
    {{-- Logo de la empresa: enlaza al dashboard --}}
    <div class="sidebar-nav__brand">
        <a href="{{ route('dashboard') }}" class="sidebar-nav__brand-link" aria-label="Ir al inicio">
            <img
                src="{{ asset('img/logo-empresa.png') }}"
                onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';"
                alt="CE Consultoría - Invertir en valor, atrae valor"
                class="sidebar-nav__brand-img"
                width="58"
                height="58"
            />
        </a>
    </div>

    <ul class="sidebar-nav__list" role="list">
        {{-- Cada ítem: ícono + etiqueta (la etiqueta se ve solo con menú expandido) --}}
        <li class="sidebar-nav__item">
            <a
                href="{{ route('dashboard') }}"
                class="sidebar-nav__link {{ request()->routeIs('dashboard') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Panel"
                aria-current="{{ request()->routeIs('dashboard') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Panel</span>
            </a>
        </li>

        @php
            $isCompaniesRoute = request()->routeIs('companies.*');
            $isFiltersView = request()->routeIs('companies.index') && request('view') === 'filtros';
            $isSalesNav = request()->routeIs('user.sales.*');
            $isCompaniesMainActive = $isCompaniesRoute && ! $isFiltersView && ! $isSalesNav;
        @endphp
        <li class="sidebar-nav__item">
            <a
                href="{{ route('companies.index') }}"
                class="sidebar-nav__link sidebar-nav__link--icon-accent-when-active {{ $isCompaniesMainActive ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Empresas"
                aria-current="{{ $isCompaniesMainActive ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Empresas</span>
            </a>
        </li>

        @can('viewAny', \App\Models\Sale::class)
        <li class="sidebar-nav__item">
            <a
                href="{{ route('user.sales.index') }}"
                class="sidebar-nav__link {{ $isSalesNav ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Historial de ventas"
                aria-current="{{ $isSalesNav ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Historial de ventas</span>
            </a>
        </li>
        @endcan

        <li class="sidebar-nav__item">
            <a
                href="{{ route('filtros.index') }}"
                class="sidebar-nav__link {{ request()->routeIs('filtros.index') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Filtros"
                aria-current="{{ request()->routeIs('filtros.index') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M5 10h14M9 16h6m-3 4v-4" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Filtros</span>
            </a>
        </li>

        <li class="sidebar-nav__item">
            <a
                href="{{ route('contacts.index') }}"
                class="sidebar-nav__link {{ request()->routeIs('contacts.*') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Contactos"
                aria-current="{{ request()->routeIs('contacts.*') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Contactos</span>
            </a>
        </li>

        <li class="sidebar-nav__item">
            <a
                href="{{ route('executives.index') }}"
                class="sidebar-nav__link {{ request()->routeIs('executives.*') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Ejecutivos"
                aria-current="{{ request()->routeIs('executives.*') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Ejecutivos</span>
            </a>
        </li>

        <li class="sidebar-nav__item">
            <a
                href="{{ route('approvals.index') }}"
                class="sidebar-nav__link {{ request()->routeIs('approvals.*') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Solicitudes pendientes"
                aria-current="{{ request()->routeIs('approvals.*') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Solicitudes pendientes</span>
                @php
                    $pendientes = 0;
                    try {
                        if (auth()->user()?->can('companies.approve')) {
                            $pendientes += \App\Models\Company::pendientesAprobacion()->count();
                            $pendientes += \App\Models\Contact::pendientesAprobacion()->count();
                        }
                        if (auth()->user()?->can('users.approve')) {
                            $pendientes += \App\Models\User::where('approval_status', 'pendiente')->count();
                        }
                    } catch (\Throwable $e) {
                        $pendientes = 0;
                    }
                    $displayPend = $pendientes > 99 ? '99+' : $pendientes;
                @endphp
                @if($pendientes > 0)
                <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-bold bg-red-500 text-white shadow-sm">
                    {{ $displayPend }}
                </span>
                @endif
            </a>
        </li>

        <li class="sidebar-nav__item">
            <a
                href="{{ route('notifications.index') }}"
                class="sidebar-nav__link {{ request()->routeIs('notifications.*') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Notificaciones"
                aria-current="{{ request()->routeIs('notifications.*') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Notificaciones</span>
                @php
                    try {
                        $unread = auth()->user()->unreadNotificationsCount();
                    } catch (\Throwable $e) {
                        $unread = 0;
                    }
                    $display = $unread > 99 ? '99+' : $unread;
                @endphp
                <span id="sidebar-notification-badge-wrap" class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-bold bg-red-500 text-white shadow-sm" style="{{ $unread > 0 ? '' : 'display: none;' }}">
                    <span id="sidebar-notification-badge">{{ $unread > 0 ? $display : '' }}</span>
                </span>
            </a>
        </li>

        <li class="sidebar-nav__item">
            <a
                href="{{ route('data-management.index') }}"
                class="sidebar-nav__link {{ request()->routeIs('data-management.*') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Gestión de Datos"
                aria-current="{{ request()->routeIs('data-management.*') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Gestión de Datos</span>
            </a>
        </li>

        <li class="sidebar-nav__item">
            <a
                href="{{ route('profile.edit') }}"
                class="sidebar-nav__link {{ request()->routeIs('profile.*') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Configuración"
                aria-current="{{ request()->routeIs('profile.*') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Configuración</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-nav__footer">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="sidebar-nav__logout" aria-label="Cerrar sesión">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                <span class="sidebar-nav__label">Cerrar sesión</span>
            </button>
        </form>
    </div>
</nav>
