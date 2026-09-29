<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoolBook</title>
    <meta name="description" content="Reserva tu carril de piscina en segundos: elige día, hora y carril, confirma, y listo.">
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    <style>
        html {
            scroll-behavior: smooth;
        }

        .scroll-hint {
            position: absolute;
            bottom: 18px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 5;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            font-size: 0.75rem;
            animation: scroll-hint-bounce 1.8s ease-in-out infinite;
        }
        .scroll-hint:hover {
            color: #fff;
        }
        .scroll-hint svg {
            width: 20px;
            height: 20px;
        }
        @keyframes scroll-hint-bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(6px); }
        }
        @media (prefers-reduced-motion: reduce) {
            .scroll-hint { animation: none; }
        }

        .content {
            justify-content: flex-start !important;
            padding-top: 30vh;
            gap: 4.5rem;
        }
        .bienvenidos {
            position: static !important;
            top: auto !important;
            margin: 0px !important;
            padding-top: 60px !important;
        }
        .Home-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.25rem;
            margin-bottom: 3rem;
        }

        /* ------------------------------------------------------------
           Botones "agua": CSS puro, sin JavaScript.
           En reposo: píldora con borde azul claro. Al pasar el ratón
           (o enfocar con teclado), una ola sube desde abajo, llena el
           botón y se sigue desplazando de lado a lado.
           ------------------------------------------------------------ */
        .btn-agua {
            position: relative;
            isolation: isolate;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 320px;
            max-width: 85vw;
            height: 52px;
            padding: 0 1.5rem;
            border: 1.5px solid rgba(141, 200, 240, 0.7);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            font-family: var(--font-body, sans-serif);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            text-decoration: none;
            overflow: hidden;
            transition: color 0.35s ease, border-color 0.35s ease;
        }
        .btn-agua span {
            position: relative;
            z-index: 2;
        }
        .btn-agua::before {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 200%;
            height: 130%;
            z-index: 1;
            /* La ola: un SVG con la cresta arriba y relleno hasta abajo.
               Se repite en horizontal (cada tramo = ancho del botón) y su
               posición se anima para que parezca que se mueve. */
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M0,30 C150,60 350,0 600,30 C850,60 1050,0 1200,30 L1200,120 L0,120 Z' fill='%238dc8f0'/%3E%3C/svg%3E") repeat-x 0 0 / 50% 100%;
            transform: translateY(100%);
            transition: transform 0.55s cubic-bezier(0.22, 0.8, 0.3, 1);
            animation: agua-ola 3.5s linear infinite;
        }
        .btn-agua:hover::before,
        .btn-agua:focus-visible::before {
            transform: translateY(15%);
        }
        .btn-agua:hover,
        .btn-agua:focus-visible {
            color: #0f4c75;
            border-color: #8dc8f0;
        }
        @keyframes agua-ola {
            from { background-position-x: 0%; }
            to   { background-position-x: 100%; }
        }
        @media (prefers-reduced-motion: reduce) {
            .btn-agua::before { animation: none; }
        }
        .recruiter-link {
            position: fixed;
            bottom: 14px;
            left: 18px;
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.85);
            background: rgba(14, 43, 51, 0.35);
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            backdrop-filter: blur(2px);
            text-decoration: none;
            z-index: 10;
        }
        .recruiter-link:hover {
            background: rgba(14, 43, 51, 0.55);
            color: #fff;
        }

        .como-funciona {
            max-width: 960px;
            margin: 1rem auto 4rem;
            padding: 0 1.5rem;
        }
        .como-funciona__pasos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .paso {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1.5rem;
            text-align: left;
        }
        .paso__numero {
            font-family: var(--font-display);
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--teal-500);
            margin-bottom: 0.5rem;
        }
        .paso h3 {
            font-size: 1.05rem;
            margin-bottom: 0.4rem;
        }
        .paso p {
            color: var(--ink-soft);
            font-size: 0.92rem;
            line-height: 1.45;
            margin: 0;
        }

        .cta-final {
            text-align: center;
            margin-top: 2.5rem;
        }

        .como-funciona__titulo {
            position: static !important;
            transform: none !important;
            display: block !important;
            font-family: var(--font-display) !important;
            font-size: 1.9rem !important;
            font-weight: 600 !important;
            color: var(--ink) !important;
            -webkit-text-fill-color: var(--ink) !important;
            -webkit-text-stroke: 0 !important;
            text-shadow: none !important;
            mix-blend-mode: normal !important;
            letter-spacing: normal !important;
            text-align: center;
            margin: 0;
        }

        @media (max-width: 720px) {
            .como-funciona__pasos {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <canvas id="ocean"></canvas>
        <div class="content">
            <h2 class="border">PoolBook</h2>
            <h2 class="ola">PoolBook</h2>
            <p class="bienvenidos">¡Bienvenidos!</p>
            <p class="bienvenidos-sub">Reserva tu carril de piscina en segundos, sin llamadas ni esperas.</p>

            <div class="Home-buttons">
                @guest
                    <a href="{{ route('login') }}" class="btn-agua">
                        <span>Mi cuenta</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn-agua">
                        <span>Mi cuenta</span>
                    </a>
                @endguest

                <a href="{{ route('calendario') }}" class="btn-agua">
                    <span>Reservar</span>
                </a>

                <a href="#como-funciona" class="btn-agua">
                    <span>¿Cómo funciona?</span>
                </a>
            </div>
        </div>
<div class="waves">

<svg
    class="editorial-wave"
    viewBox="0 24 150 28"
    preserveAspectRatio="none"
    xmlns="http://www.w3.org/2000/svg">

    <defs>
        <path
            id="gentle-wave"
            d="M-160 44c30 0 58-18 88-18s58 18 88 18
               58-18 88-18 58 18 88 18v44h-352z"/>
    </defs>

    <g class="parallax-waves">
        <use href="#gentle-wave" x="48" y="0"/>
        <use href="#gentle-wave" x="48" y="2"/>
        <use href="#gentle-wave" x="48" y="4"/>
        <use href="#gentle-wave" x="48" y="6"/>
    </g>

</svg>

</div>

    <a href="#como-funciona" class="scroll-hint" aria-label="Ver más abajo">
        <span>Ver más</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 9l6 6 6-6"/>
        </svg>
    </a>
    </div>

    <!-- ============================================================
         Contenido explicativo, fuera del contenedor del hero para no
         interferir con el canvas ni con la posición de las olas.
         ============================================================ -->
    <section class="como-funciona" id="como-funciona">
        <h3 class="como-funciona__titulo">Reserva en 3 pasos</h3>
        <div class="como-funciona__pasos">
            <div class="paso">
                <div class="paso__numero">1</div>
                <h3>Regístrate</h3>
                <p>Crea tu cuenta en un minuto, sin más datos que los imprescindibles.</p>
            </div>
            <div class="paso">
                <div class="paso__numero">2</div>
                <h3>Elige día y carril</h3>
                <p>En el calendario ves al instante qué carriles y horas están libres.</p>
            </div>
            <div class="paso">
                <div class="paso__numero">3</div>
                <h3>Confirma, listo</h3>
                <p>Te lo mostramos claro antes de reservar. Puedes cancelarla cuando quieras.</p>
            </div>
        </div>

        <div class="cta-final">
            <a href="{{ route('calendario') }}" class="btn btn-cta">Reservar ahora</a>
            <div style="margin-top: 0.9rem;">
                <a href="{{ route('help') }}" style="font-size: 0.85rem; color: var(--ink-soft);">¿Necesitas más ayuda? Ver guía completa</a>
            </div>
        </div>
    </section>

    <a href="{{ route('recruiter') }}" class="recruiter-link">¿Eres reclutador? Detalle técnico del proyecto →</a>

    {{-- Olas del fondo: mismo archivo compartido que usa el login y el registro --}}
    <script src="{{ asset('js/ocean.js') }}"></script>

</body>
</html>