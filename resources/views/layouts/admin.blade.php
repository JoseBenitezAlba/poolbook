<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Panel Admin</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js']) {{-- importa Tailwind / Bootstrap y tu app.css --}}
    <link rel="stylesheet" href="{{ asset('menu-admin.css') }}"> {{-- tu CSS personalizado de menú admin --}}
</head>
<body>
    <div class="container2">
        <aside>
            <p>Menu</p>
            <a href="{{ route('calendario') }}">Calendario</a>
            <a href="{{ route('reservas.index') }}">Reservas</a>
            <a href="{{ route('admin.index') }}">Usuarios</a>
            <a href="{{ route('admin.users.create') }}">Crear admin</a>
            <a href="{{ route('admin.perfil') }}">Perfil</a>
            <a href="{{ route('dashboard') }}">Dashboard</a>
            @auth
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
               Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @endauth
        </aside>

        <div class="content">
            @yield('admin-content')
        </div>
    </div>
</body>
</html>