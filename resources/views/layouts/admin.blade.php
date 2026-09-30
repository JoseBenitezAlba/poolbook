<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'PoolBook · Admin')</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Sistema de diseño PoolBook (el mismo que el resto del sitio) -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('menu-admin.css') }}">
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar" id="admin-sidebar">
            <div class="admin-sidebar__top">
                <div class="admin-sidebar__brand">PoolBook</div>

                {{-- Solo se ve en móvil (ver menu-admin.css): abre y cierra el menú --}}
                <button type="button"
                        class="admin-sidebar__toggle"
                        aria-label="Abrir menú"
                        aria-expanded="false"
                        aria-controls="admin-menu">
                    <svg class="icon-abrir" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                    <svg class="icon-cerrar" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M6 6l12 12M18 6L6 18"/>
                    </svg>
                </button>
            </div>

            <nav class="admin-sidebar__nav" id="admin-menu">
                <a href="{{ route('calendario') }}" class="{{ request()->routeIs('calendario') ? 'is-active' : '' }}">Calendario</a>
                <a href="{{ route('reservas.index') }}" class="{{ request()->routeIs('reservas.index') ? 'is-active' : '' }}">Reservas</a>
                <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') || request()->routeIs('admin.users.show') ? 'is-active' : '' }}">Usuarios</a>
                <a href="{{ route('admin.users.create') }}" class="{{ request()->routeIs('admin.users.create') ? 'is-active' : '' }}">Crear Usuarios</a>
                <a href="{{ route('admin.perfil') }}" class="{{ request()->routeIs('admin.perfil') ? 'is-active' : '' }}">Perfil</a>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}">Dashboard</a>
            </nav>
            @auth
                <div class="admin-sidebar__logout">
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                       Cerrar sesión
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            @endauth
        </aside>

        <div class="admin-content">
            @yield('admin-content')
        </div>
    </div>

    <script>
        // Menú hamburguesa (solo tiene efecto visible en móvil)
        (function () {
            var toggle = document.querySelector('.admin-sidebar__toggle');
            var sidebar = document.getElementById('admin-sidebar');
            if (!toggle || !sidebar) return;

            toggle.addEventListener('click', function () {
                var abierto = sidebar.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', abierto ? 'true' : 'false');
                toggle.setAttribute('aria-label', abierto ? 'Cerrar menú' : 'Abrir menú');
            });
        })();
    </script>
</body>
</html>