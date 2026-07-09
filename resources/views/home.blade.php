<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoolBook</title>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <canvas id="ocean"></canvas>
        <div class="content">
            <h2 class="border">PoolBook</h2>
            <h2 class="ola">PoolBook</h2>
            <p class="bienvenidos">¡Bienvenidos!</p>

            <div class="Home-buttons">
                @guest
                    <a href="{{ route('login') }}" class="btn-liquid">
                        <span class="inner">Perfil</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn-liquid">
                        <span class="inner">Perfil</span>
                    </a>
                @endguest

                <a href="{{ route('calendario') }}" class="btn-liquid">
                    <span class="inner">Calendario</span>
                </a>

                <a href="{{ route('help') }}" class="btn-liquid">
                    <span class="inner">¿Eres Nuevo?</span>
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
    </div>

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