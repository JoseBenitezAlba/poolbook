<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoolBook</title>
    <meta name="description" content="Reserva tu carril de piscina en segundos: elige día, hora y carril, confirma, y listo.">
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

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
            padding-top: 35vh;
            gap: 4.5rem;
        }
        .bienvenidos {
            position: static !important;
            top: auto !important;
            margin: 0 !important;
        }
        .Home-buttons {
            margin-bottom: 3rem;
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
                    <a href="{{ route('login') }}" class="btn-liquid">
                        <span class="inner">Mi cuenta</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn-liquid">
                        <span class="inner">Mi cuenta</span>
                    </a>
                @endguest

                <a href="{{ route('calendario') }}" class="btn-liquid">
                    <span class="inner">Reservar</span>
                </a>

                <a href="#como-funciona" class="btn-liquid">
                    <span class="inner">¿Cómo funciona?</span>
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

    <script>
    $(function() {
        var viscosity = 50,
            mouseDist = 30,
            damping = 0.05,
            points = 20;

        $('.btn-liquid').each(function() {
            initButton($(this));
        });

        function initButton($button) {
            var pointsA = [],
                pointsB = [],
                $canvas = $('<canvas></canvas>'),
                canvas = $canvas.get(0),
                context = canvas.getContext('2d'),
                mouseX = 0,
                mouseY = 0,
                relMouseX = 0,
                relMouseY = 0,
                mouseLastX = 0,
                mouseLastY = 0,
                mouseSpeedX = 0,
                mouseSpeedY = 0;

            $button.append($canvas);
            var buttonWidth = $button.width(),
                buttonHeight = $button.height();
            canvas.width = buttonWidth + 100;
            canvas.height = buttonHeight + 100;

            $canvas.on('mousemove', function(e) {
                var rect = canvas.getBoundingClientRect();
                mouseX = e.clientX - rect.left;
                mouseY = e.clientY - rect.top;

                relMouseX = mouseX;
                relMouseY = mouseY;

                mouseSpeedX = mouseX - mouseLastX;
                mouseSpeedY = mouseY - mouseLastY;

                mouseLastX = mouseX;
                mouseLastY = mouseY;
            });

            function addPoints(x, y) {
                pointsA.push(new Point(x, y, 1));
                pointsB.push(new Point(x, y, 2));
            }

            var x = buttonHeight;
            addPoints(x + 100, 150);
            for (var j = 1; j < points; j++) {
                addPoints(x + ((buttonWidth - buttonHeight) / points) * j, 10);
            }

            function Point(x, y, level) {
                this.x = this.ix = 50 + x;
                this.y = this.iy = 50 + y;
                this.vx = 10;
                this.vy = 10;
                this.level = level;
            }

            Point.prototype.move = function() {
                var dx = this.ix - relMouseX,
                    dy = this.iy - relMouseY;
                var dist = Math.sqrt(dx * dx + dy * dy);
                var relDist = (1 - dist / mouseDist);

                this.vx += (this.ix - this.x) / (viscosity * this.level);
                this.vy += (this.iy - this.y) / (viscosity * this.level);

                if (relDist > 0 && relDist < 1) {
                    this.vx += mouseSpeedX * relDist;
                    this.vy += mouseSpeedY * relDist;
                }
                this.vx *= (1 - damping);
                this.vy *= (1 - damping);
                this.x += this.vx;
                this.y += this.vy;
            };

            function renderCanvas() {
                requestAnimationFrame(renderCanvas);
                context.clearRect(0, 0, canvas.width, canvas.height);

                context.fillStyle = '#fff';
                context.beginPath();
                context.moveTo(pointsA[0].x, pointsA[0].y);
                for (var i = 1; i < pointsA.length; i++) {
                    var p = pointsA[i];
                    var prevP = pointsA[i - 1];
                    var cx = (p.x + prevP.x) / 2;
                    var cy = (p.y + prevP.y) / 2;
                    context.quadraticCurveTo(prevP.x, prevP.y, cx, cy);
                }
                context.closePath();
                context.fill();

                for (var i = 0; i < pointsA.length; i++) {
                    pointsA[i].move();
                    pointsB[i].move();
                }

                var gradient = context.createRadialGradient(relMouseX, relMouseY, 0, relMouseX, relMouseY, canvas.width / 2);
                gradient.addColorStop(0, 'rgba(185, 175, 233, 0.9)');
                gradient.addColorStop(1, 'rgba(168, 213, 245, 0.9)');
                context.fillStyle = gradient;
                context.fill();
            }

            renderCanvas();
        }
    });
    const canvas = document.getElementById("ocean");
const ctx = canvas.getContext("2d");

function resizeOcean(){
    canvas.width = window.innerWidth;
    canvas.height = canvas.offsetHeight;
}

resizeOcean();

window.addEventListener("resize", resizeOcean);

const waves = [
{
    color:"rgba(0,130,255,.18)",
    amplitude:45,
    wavelength:0.008,
    speed:0.6,
    offset:0
},
{
    color:"rgba(0,150,255,.30)",
    amplitude:35,
    wavelength:0.011,
    speed:0.9,
    offset:35
},
{
    color:"rgba(0,180,255,.45)",
    amplitude:28,
    wavelength:0.015,
    speed:1.2,
    offset:70
},
{
    color:"#33b8ff",
    amplitude:22,
    wavelength:0.020,
    speed:1.6,
    offset:105
}
];

let t=0;

function draw(){

    ctx.clearRect(0,0,canvas.width,canvas.height);

    waves.forEach(w=>{

        ctx.beginPath();

        ctx.moveTo(0,canvas.height);

        for(let x=0;x<=canvas.width;x++){

            let y=

                canvas.height*0.35

                +

                Math.sin(x*w.wavelength+t*w.speed)*w.amplitude

                +

                Math.sin(x*w.wavelength*2.3+t*w.speed*.7)*w.amplitude*.45

                +

                Math.sin(x*w.wavelength*.4+t*w.speed*.25)*w.amplitude*.8

                +

                w.offset;

            ctx.lineTo(x,y);
        }

        ctx.lineTo(canvas.width,canvas.height);

        ctx.closePath();

        ctx.fillStyle=w.color;

        ctx.fill();

    });

    t+=0.02;

    requestAnimationFrame(draw);

}

draw();
    </script>

</body>
</html>