{{--
  Sidebar para Usuario Normal (operativo y gestión).
  Solo: Inicio, Empresas, Contactos, Seguimientos, Historial de Ventas.
  Sin acceso a Configuración global ni vistas de administrador.
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
                alt="C&amp;CE Consultoría"
                class="sidebar-nav__brand-img"
                width="72"
                height="72"
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
            <a
                href="{{ route('companies.index') }}"
                class="sidebar-nav__link {{ request()->routeIs('companies.*') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Empresas"
                aria-current="{{ request()->routeIs('companies.*') ? 'page' : false }}"
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
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
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
    </ul>
</nav>
