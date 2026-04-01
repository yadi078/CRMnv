{{--
  Sidebar usuario operativo: Inicio, Empresas, Filtros, Contactos, Seguimientos, Historial,
  Gestión de datos, Notificaciones, Perfil. (Sin panel admin, ejecutivos ni aprobaciones.)
--}}
<nav
    class="sidebar-nav"
    role="navigation"
    aria-label="Navegación usuario"
>
    <div class="sidebar-nav__brand">
        <a href="{{ route('user.dashboard') }}" class="sidebar-nav__brand-link" aria-label="Ir al inicio">
            <img
                src="{{ asset('img/logo-empresa.png') }}"
                onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';"
                alt="CE Consultoría"
                class="sidebar-nav__brand-img"
                width="58"
                height="58"
            />
        </a>
    </div>

    <ul class="sidebar-nav__list" role="list">
        <li class="sidebar-nav__item">
            <a
                href="{{ route('user.dashboard') }}"
                class="sidebar-nav__link {{ request()->routeIs('user.dashboard') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Inicio"
                aria-current="{{ request()->routeIs('user.dashboard') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Inicio</span>
            </a>
        </li>

        <li class="sidebar-nav__item">
            @php
                $isCompaniesRoute = request()->routeIs('companies.*');
                $isFiltersView = request()->routeIs('companies.index') && request('view') === 'filtros';
                $isCompaniesActive = $isCompaniesRoute && ! $isFiltersView;
            @endphp
            <a
                href="{{ route('companies.index') }}"
                class="sidebar-nav__link sidebar-nav__link--icon-accent-when-active {{ $isCompaniesActive ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Empresas"
                aria-current="{{ $isCompaniesActive ? 'page' : false }}"
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
                href="{{ route('follow-ups.index') }}"
                class="sidebar-nav__link {{ request()->routeIs('follow-ups.*') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Seguimientos"
                aria-current="{{ request()->routeIs('follow-ups.*') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    {{-- Lista / tareas (no campana: reservada a Notificaciones) --}}
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Seguimientos</span>
            </a>
        </li>

        <li class="sidebar-nav__item">
            <a
                href="{{ route('user.sales.index') }}"
                class="sidebar-nav__link {{ request()->routeIs('user.sales.*') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Historial de Ventas"
                aria-current="{{ request()->routeIs('user.sales.*') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Historial de Ventas</span>
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
                href="{{ route('profile.edit') }}"
                class="sidebar-nav__link {{ request()->routeIs('profile.*') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Perfil"
                aria-current="{{ request()->routeIs('profile.*') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Perfil</span>
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
