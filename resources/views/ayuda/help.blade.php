<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - PoolBook</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #164C75;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .nav-container {
            display: flex;
            justify-content: flex-start; /* Cambiar a flex-start */
            align-items: center;
            margin-bottom: 20px;
        }

        .nav-container a,
        .nav-container form button {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            margin-left: 10px;
            display: inline-block;
            background-color: #007bff; /* Color de fondo predeterminado para todos los botones */
            border: none; /* Eliminar borde predeterminado del botón */
            cursor: pointer; /* Agregar cursor de puntero al pasar el mouse */
            font-size: 16px; /* Asegurar que todos los botones tengan el mismo tamaño de fuente */
            line-height: 1.5; /* Asegurar que todos los botones tengan la misma altura de línea */
        }

        .nav-container a.home {
            background-color: #007bff;
        }

        .nav-container a.dashboard {
            background-color: #007bff;
        }

        .nav-container form button.logout {
            background-color: #dc3545;
        }

        .nav-container a.login {
            background-color: #28a745;
        }

        .nav-container a.register {
            background-color: #ffc107;
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            color: white;
            text-align: center;
            margin-bottom: 40px;
        }
        a{ 
        text-decoration: none;
    }
      

        .card-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: calc(33.333% - 20px);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        .card h2 {
            margin-bottom: 16px;
            color: #007bff;
        }

        .card p {
            color: #666;
            margin-bottom: 20px;
        }

        .card a {
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            display: inline-block;
        }

        .card a:hover {
            background-color: #0056b3;
        }

        .help-buttons {
            display: flex;
            justify-content: flex-start; /* Cambiar a flex-start */
            margin-top: 40px;
            text-align: center;
        }

        .btn-liquid {
            margin: 0 10px;
            text-align: center;
            display: inline-block;
            overflow: hidden;
            position: relative;
        }

        .inner {
            display: block;
            padding: 15px 30px;
            color: #fff;
            text-decoration: none;
            background-color: #007bff;
            border-radius: 5px;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .btn-liquid:hover .inner {
            transform: translateY(-5px);
            background-color: #0056b3;
        }

        .btn-liquid::before,
        .btn-liquid::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            z-index: -1;
        }

     

        .btn-liquid:hover::before {
            width: 120%;
            height: 120%;
            left: -10%;
        }

        .btn-liquid::after {
            background-color: rgba(255, 255, 255, 0.1);
            transition: width 0.3s ease, height 0.3s ease, left 0.3s ease;
        }

        .btn-liquid:hover::after {
            width: 120%;
            height: 120%;
            left: -10%;
        }

        .nav-container form {
            display: inline-block; /* Asegurar que el formulario esté en línea con los demás elementos */
            margin: 0; /* Eliminar el margen predeterminado del formulario */
        }

        .nav-container form button {
            padding: 10px 20px;
            font-size: 16px;
            line-height: 1.5;
        }
        .center-container {
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    height:  /* Establecer la altura del contenedor al 100% del viewport */
}

    </style>
</head>
<body>
    <div class="container">
        <!-- Menú -->
        <div class="nav-container">
            @auth
                <a href="{{ url('/dashboard') }}" style="text-decoration: none; color: #fff; margin-right: auto; margin-left: 10px;">{{ Auth::user()->name }}</a>
            @endauth
            <div class="flex items-center">
                <a href="{{ url('/home') }}" class="home">Home</a>
                @auth
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="logout">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="login">Log in</a>
                   
                @endauth
            </div>
        </div>
        
        
        
        <h1>¡Bienvenid@ a PoolBook!</h1>
        <p>Tu guía completa para el mundo de la natación. Aquí encontrarás todo lo que necesitas saber para empezar y mejorar en este maravilloso deporte.</p>

        <div class="card-container">
            <div class="card">
                <h2>Material de Natación</h2>
                <p>Descubre el equipo esencial para tus entrenamientos y competiciones.</p>
                <a href="{{ route('natacion.material') }}">Ver más</a>
            </div>
            <div class="card">
                <h2>Estilos de Natación</h2>
                <p> de entrenamiento adaptados a todos los niveles.</p>
                <a href="{{ route('natacion.entrenamiento') }}">Ver más</a>
            </div>
        </div>

        <div class="center-container">
            <div class="help-buttons">
                <a href="{{ route('ayuda.app') }}" class="btn-liquid">
                    <span class="inner">Ayuda de la Aplicación</span>
                </a>
            </div>
        </div>
</body>
</html>
