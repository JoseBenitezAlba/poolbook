// ============================================================
// OLAS ANIMADAS (canvas #ocean)
// Mismo efecto que la home, sacado a un archivo compartido para
// poder usarlo en más páginas (por ejemplo, el login) sin copiar
// y pegar el código. Va envuelto en una función autoejecutable para
// que sus variables (canvas, ctx, waves, t...) no choquen con las de
// otros scripts de la página.
// ============================================================
(function () {
    const canvas = document.getElementById('ocean');

    // Si la página no tiene <canvas id="ocean">, no hacemos nada.
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    function resizeOcean() {
        canvas.width = window.innerWidth;
        canvas.height = canvas.offsetHeight;
    }

    resizeOcean();
    window.addEventListener('resize', resizeOcean);

    // Cuatro capas de ola, de más transparente/lenta (fondo) a más
    // opaca/rápida (frente). "amplitude" = altura de la ola,
    // "wavelength" = lo estrechas que son, "speed" = velocidad.
    const waves = [
        { color: 'rgba(0,130,255,.18)', amplitude: 45, wavelength: 0.008, speed: 0.6, offset: 0 },
        { color: 'rgba(0,150,255,.30)', amplitude: 35, wavelength: 0.011, speed: 0.9, offset: 35 },
        { color: 'rgba(0,180,255,.45)', amplitude: 28, wavelength: 0.015, speed: 1.2, offset: 70 },
        { color: '#33b8ff',            amplitude: 22, wavelength: 0.020, speed: 1.6, offset: 105 }
    ];

    let t = 0;

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        waves.forEach(function (w) {
            ctx.beginPath();
            ctx.moveTo(0, canvas.height);

            for (let x = 0; x <= canvas.width; x++) {
                // Cada ola es la suma de tres senoidales a distinta
                // frecuencia, para que no se vea un vaivén "perfecto".
                const y =
                    canvas.height * 0.35 +
                    Math.sin(x * w.wavelength + t * w.speed) * w.amplitude +
                    Math.sin(x * w.wavelength * 2.3 + t * w.speed * 0.7) * w.amplitude * 0.45 +
                    Math.sin(x * w.wavelength * 0.4 + t * w.speed * 0.25) * w.amplitude * 0.8 +
                    w.offset;

                ctx.lineTo(x, y);
            }

            ctx.lineTo(canvas.width, canvas.height);
            ctx.closePath();
            ctx.fillStyle = w.color;
            ctx.fill();
        });

        t += 0.02;
        requestAnimationFrame(draw);
    }

    draw();
})();