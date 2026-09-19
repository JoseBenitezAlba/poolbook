<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PoolBook — Detalle técnico</title>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <style>
        body { background: var(--paper); }
        .recruiter-wrap {
            max-width: 680px;
            margin: 0 auto;
            padding: 3.5rem 1.5rem 4rem;
        }
        .recruiter-wrap > a.volver {
            display: inline-block;
            margin-bottom: 2rem;
            color: var(--ink-soft);
            text-decoration: none;
            font-size: 0.9rem;
        }
        .recruiter-wrap > a.volver:hover { color: var(--coral); }
        .recruiter-wrap h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .recruiter-wrap > p.lead {
            color: var(--ink-soft);
            font-size: 1.05rem;
            line-height: 1.55;
            margin-bottom: 2.5rem;
        }
        .item {
            border-top: 1px solid var(--line);
            padding: 1.5rem 0;
        }
        .item:last-of-type {
            border-bottom: 1px solid var(--line);
        }
        .item h2 {
            font-size: 1.15rem;
            margin-bottom: 0.5rem;
        }
        .item p {
            color: var(--ink-soft);
            font-size: 0.95rem;
            line-height: 1.55;
        }
        .item code {
            background: var(--paper-deep);
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-size: 0.85em;
        }
        .top-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .top-links a.volver {
            margin-bottom: 0;
        }
        .social-links a {
            color: var(--ink-soft);
            text-decoration: none;
            font-size: 0.9rem;
            margin-left: 1rem;
        }
        .social-links a:hover { color: var(--coral); }

        .demo-box {
            background: var(--paper-deep);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            margin-top: 0.5rem;
        }
        .demo-box .cuenta {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.5rem 0;
            font-size: 0.92rem;
        }
        .demo-box .cuenta + .cuenta {
            border-top: 1px solid var(--line);
        }
    </style>
</head>
<body>
<div class="recruiter-wrap">
    <div class="top-links">
        <a href="{{ url('/') }}" class="volver">&larr; Volver a PoolBook</a>
        <div class="social-links">
            <a href="https://github.com/JoseBenitezAlba/poolbook" target="_blank">GitHub ↗</a>
            <a href="https://www.linkedin.com/in/jose-manuel-benitez-alba" target="_blank">LinkedIn ↗</a>
        </div>
    </div>

    <h1>Qué hay detrás de PoolBook</h1>
    <p class="lead">
        PoolBook es una aplicación real de reservas para una piscina (carriles, horarios, bonos de sesiones),
        construida en Laravel. Esto es lo que probablemente no se ve solo mirando la interfaz:
    </p>

    <div class="item">
        <h2>Roles y visibilidad por permisos</h2>
        <p>
            Usuarios y administradores ven cosas distintas de la misma pantalla: un admin ve el nombre real
            de cada reserva y puede cancelar cualquiera; un usuario normal solo ve "Ocupado" en las que no
            son suyas. Se controla con el paquete <code>spatie/laravel-permission</code> y una
            <code>Policy</code> dedicada (<code>CitaPolicy</code>), no con condicionales sueltos por la vista.
        </p>
    </div>

    <div class="item">
        <h2>Consumo de bonos con lógica FIFO y bloqueo de fila</h2>
        <p>
            Al reservar, el sistema elige automáticamente el bono que antes caduca (no el primero que
            encuentra), y lo hace dentro de una transacción con <code>lockForUpdate()</code> para que dos
            reservas simultáneas no consuman la misma sesión dos veces.
        </p>
    </div>

    <div class="item">
        <h2>Un bug de zona horaria real, encontrado y corregido</h2>
        <p>
            El calendario codificaba la hora local de Madrid en los componentes UTC de la fecha (un patrón
            habitual de FullCalendar con <code>timeZone</code> personalizado); el backend la reinterpretaba
            como UTC real y la desplazaba dos horas, rechazando reservas de tarde/noche que sí eran válidas.
            Diagnosticado con logs y corregido en el parseo de <code>Carbon</code>.
        </p>
    </div>

    <div class="item">
        <h2>Asistente conversacional con function calling</h2>
        <p>
            Un chat integrado interpreta lenguaje natural ("resérvame el sábado a las 10, el carril que
            esté libre" o incluso reservas recurrentes como "resérvame todos los lunes hasta diciembre")
            y ejecuta la reserva real a través de function calling contra el modelo, no solo devuelve texto.
        </p>
    </div>

    <div class="item">
        <h2>Panel de administración</h2>
        <p>
            Gestión de usuarios y bonos (altas, desactivación, historial de reservas por usuario) sin tocar
            la base de datos a mano.
        </p>
    </div>

    <div class="item">
        <h2>Pruébalo tú mismo</h2>
        <p>
            Dos cuentas de demo, cada una en un rol distinto, para que puedas entrar sin tener que registrarte
            ni preocuparte por quedarte sin sesiones:
        </p>
        <div class="demo-box">
            <div class="cuenta">
                <span><strong>Admin</strong> — panel de usuarios, bonos y gestión de cualquier reserva</span>
                <span>admin@poolbook.com / admin1234</span>
            </div>
            <div class="cuenta">
                <span><strong>Usuario</strong> — bono con sesiones prácticamente ilimitadas, para probar el
                    calendario y el asistente de chat sin límite</span>
                <span>prueba@prueba.com / prueba</span>
            </div>
        </div>
    </div>

    <div class="item">
        <h2>Stack técnico</h2>
        <p>
            Laravel 10 · <code>spatie/laravel-permission</code> · FullCalendar (Scheduler) · SweetAlert2 para
            confirmaciones · Google Gemini con function calling para el asistente · Vite · Bootstrap con un
            sistema de diseño propio por encima (tipografía, color y componentes reestilizados).
        </p>
    </div>
</div>
</body>
</html>