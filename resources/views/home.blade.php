<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoolBook</title>
</head>
<body>
    <div class="container">
        <div class="content">
            <h2 class="border">PoolBook</h2>
            <h2 class="ola">Pool<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>Book</h2>
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
            <div class="wave circulo a"></div>
            <div class="wave circulo b"></div>
            <div class="wave circulo c"></div>
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
                mouseDirectionX = 0,
                mouseDirectionY = 0,
                mouseSpeedX = 0,
                mouseSpeedY = 0;

            $button.append($canvas);
            var buttonWidth = $button.width(),
            buttonHeight = $button.height();
            canvas.width = buttonWidth + 100;
            canvas.height = buttonHeight + 100;

            // Setup mouse event listeners
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
            // Add more points as needed...

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

                // Draw the liquid effect
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

                // Update and move each point based on mouse interaction
                for (var i = 0; i < pointsA.length; i++) {
                    pointsA[i].move();
                    pointsB[i].move();
                }

                // Add a gradient for a cool visual effect
                var gradient = context.createRadialGradient(relMouseX, relMouseY, 0, relMouseX, relMouseY, canvas.width / 2);
                gradient.addColorStop(0, 'rgba(160, 144, 240, 0.9)');
                gradient.addColorStop(1, 'rgba(141, 200, 241, 0.9)');
                context.fillStyle = gradient;
                context.fill();
            }

            renderCanvas();  // Start the animation
        }
    });
   </script>
	
</body>
</html>