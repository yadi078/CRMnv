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
    {{-- Logo de la empresa: enlaza al dashboard, bien centrado arriba del menú --}}
    <div class="sidebar-nav__brand">
        <a href="{{ route('dashboard') }}" class="sidebar-nav__brand-link" aria-label="Ir al inicio">
            <img
                src="{{ asset('img/logo-empresa.png') }}"
                onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';"
                alt="C&amp;CE Consultoría - Invertir en valor, atrae valor"
                class="sidebar-nav__brand-img"
                width="72"
                height="72"
            />
        </a>
    </div>

    <div class="sidebar-nav__welcome">
        <p class="sidebar-nav__welcome-text">Bienvenido</p>
        <p class="sidebar-nav__welcome-name">{{ Auth::user()->name }}</p>
    </div>

    <ul class="sidebar-nav__list" role="list">
        {{-- Cada ítem: ícono + etiqueta (la etiqueta se ve solo con menú expandido) --}}
        <li class="sidebar-nav__item">
            <a
                href="{{ route('dashboard') }}"
                class="sidebar-nav__link {{ request()->routeIs('dashboard') ? 'sidebar-nav__link--active' : '' }}"
                aria-label="Dashboard"
                aria-current="{{ request()->routeIs('dashboard') ? 'page' : false }}"
            >
                <span class="sidebar-nav__icon-wrap">
                    <span class="sidebar-nav__wave" aria-hidden="true"></span>
                    <svg class="sidebar-nav__icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Dashboard</span>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </span>
                <span class="sidebar-nav__label">Seguimientos</span>
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
            <button type="submit" class="sidebar-nav__logout">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="sidebar-nav__label">Cerrar sesión</span>
            </button>
        </form>
    </div>
</nav>
