<!DOCTYPE html>

<html lang="en">
<head>

  <meta charset="utf-8" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.11/index.global.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Sistema de diseño PoolBook (el mismo que el resto del sitio) -->
  <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

  <!-- esto compila el css y el js de calendario.js/css con vite, antes lo tenia to metido aqui pero ya esta separado -->
  @vite(['resources/css/calendario.css', 'resources/js/calendario.js'])

  <!-- estas 3 lineas no se pueden mover al js normal, son de blade y necesitan el servidor -->
  <script>
    window.csrfToken = "{{ csrf_token() }}";
    window.isAuthenticated = @json(auth()->check());
    window.userId = @json(auth()->id());
  </script>

  <style>
    /* Cabecera del calendario: nombre del club + acceso rápido de admin */
    .poolbook-topbar {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.75rem 1.5rem;
      background-color: var(--paper);
      border-bottom: 1px solid var(--line);
    }
    .poolbook-topbar__brand {
      font-family: var(--font-display);
      font-weight: 600;
      font-size: 1.25rem;
      color: var(--ink);
      text-decoration: none;
    }
    .poolbook-topbar__brand:hover {
      color: var(--coral);
    }
  </style>

</head>
<body>

<div class="poolbook-topbar">
  <a href="{{ url('/') }}" class="poolbook-topbar__brand">PoolBook</a>
  <div class="flex items-center gap-4" style="display:flex; align-items:center; gap:1rem;">
    {{-- Enlace al panel de admin, solo visible si el usuario logueado tiene el rol Admin --}}
    @auth
      @if (auth()->user()->hasRole(\App\Enums\Role::ADMIN))
        <a href="{{ route('admin.index') }}" class="btn btn-outline-primary btn-sm">Gestionar usuarios</a>
      @endif
    @endauth

    @if (Route::has('login'))
      @auth
        <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="btn btn-cta btn-sm">Log in</a>
      @endauth
    @endif
  </div>
</div>

<div class="flex flex-col items-center bg-gray-100 p-4">
  <!-- el calendario en si, esto lo pinta FullCalendar via js -->
  <div id="calendar" class="mx-auto"></div>
</div>

<!-- el chat solo sale si estas logueado, si no ni se pinta el html -->
@auth

<button id="asistente-btn" title="Asistente de reservas">
  <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8">
    <circle cx="12" cy="12" r="9"/>
    <circle cx="12" cy="12" r="3.3"/>
    <path d="M12 3v5.7M12 15.3V21M3 12h5.7M15.3 12H21" stroke-width="2.4"/>
  </svg>
  <span>Asistente</span>
</button>
<div id="asistente-aviso" aria-hidden="true">¿Dudas para reservar? Pregúntame aquí</div>
<div id="asistente-panel">
  <div id="asistente-header">Asistente PoolBook</div>
  <div id="asistente-mensajes">
    <div class="msg-ia">¡Hola! Puedo decirte qué carriles hay libres o reservarte uno. Por ejemplo: "resérvame el sábado a las 10, el carril que esté libre" o incluso reservas recurrentes como "resérvame todos los lunes hasta diciembre ".</div>
  </div>
  <form id="asistente-form">
    <input id="asistente-input" type="text" placeholder="Escribe tu mensaje..." autocomplete="off" />
    <button type="submit">Enviar</button>
  </form>
</div>

@endauth

</body>
</html>