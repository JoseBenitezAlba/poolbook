<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda de la Aplicación - PoolBook</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f4c75;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .video-card {
            background-color: #1a5f8a;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 32px;
        }
        .video-card h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 8px;
            color: #8dc8f0;
        }
        .video-card p {
            margin-bottom: 16px;
            color: #cce4f7;
        }
        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 8px;
        }
        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .btn-volver {
            display: inline-block;
            background-color: #3282b8;
            color: #fff;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-volver:hover {
            background-color: #1a5f8a;
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4 py-10 max-w-4xl">

        <h1 class="text-4xl font-bold text-center mb-4" style="color: #8dc8f0;">Ayuda de la Aplicación</h1>
        <p class="text-center mb-10" style="color: #cce4f7;">Aquí encontrarás tutoriales en vídeo para aprender a usar PoolBook paso a paso.</p>

        <!-- Video 1 -->
        <div class="video-card">
            <h2>📅 Cómo crear y eliminar una cita</h2>
            <p>Aprende a reservar un carril en el calendario y a eliminar una reserva existente.</p>
            <div class="video-wrapper">
                <iframe src="https://www.youtube.com/embed/xGOlchSwi8w"
                    title="Crear y eliminar cita - PoolBook"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        </div>

        <!-- Video 2 -->
        <div class="video-card">
            <h2>👤 Cómo ver tus reservas</h2>
            <p>Descubre cómo consultar todas las reservas que tienes realizadas desde tu perfil.</p>
            <div class="video-wrapper">
                <iframe src="https://www.youtube.com/embed/-4ylQlJrt-8"
                    title="Ver Reservas - PoolBook"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ url('/help') }}" class="btn-volver">← Volver a Ayuda</a>
        </div>

    </div>
</body>
</html>