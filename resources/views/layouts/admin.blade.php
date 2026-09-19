<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Panel Admin</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Sistema de diseño PoolBook (el mismo que el resto del sitio) -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('menu-admin.css') }}">
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-sidebar__brand">PoolBook</div>
            <nav class="admin-sidebar__nav">
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
</body>
</html>