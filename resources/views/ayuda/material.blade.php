<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material de Natación - PoolBook</title>

    <style>
        body {
            background-color: #f5f5f5;
            /* Cambia el color de fondo del cuerpo */
            margin: 0;
            /* Elimina el margen por defecto */
            padding: 0;
            /* Elimina el relleno por defecto */
            font-family: Arial, sans-serif;
            /* Utiliza una fuente sans-serif por defecto */
        }

        .container {
            max-width: 1200px;
            /* Limita el ancho del contenedor principal */
            margin: 50px auto;
            /* Centra el contenedor en la página */
            padding: 20px;
            /* Añade un poco de espacio interno al contenedor */
            background-color:#cedfec;
            /* Cambia el color de fondo del contenedor */
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            /* Añade una sombra suave al contenedor */
            border-radius: 10px;
            /* Redondea los bordes del contenedor */
            padding: 50px;
        }

        h1 {
            color: #333;
            /* Cambia el color del título principal */
            text-align: center;
        }

        p {
            color: #666;
            margin: 50px auto;
            /* Cambia el color del texto normal */
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-top: 20px;
            /* Añade un poco de espacio sobre las tarjetas */
        }

        .card {
            flex: 0 0 calc(33.333% - 20px);
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            box-sizing: border-box;
            
            /* Añade una sombra suave a las tarjetas */
            transition: box-shadow 0.3s ease;
            /* Agrega una transición suave a la sombra */
        }

        .card:hover {
            box-shadow: 0 0 15px rgb(15, 51, 124);
            /* Aumenta la sombra al pasar el ratón sobre las tarjetas */
        }

        .card img {
            max-width: 100%;
            border-radius: 8px;
            text-align: center;
            margin: 0 auto;
        }

        .btn-back {
    text-align: center; /* Centra el botón */
    margin-top: 24px; /* Añade un margen superior */
}

.btn-back a {
    text-decoration: none; /* Elimina el subrayado del enlace */
    color: #007bff; /* Color del texto del enlace */
    background-color: #fff; /* Color de fondo del enlace */
    padding: 10px 20px; /* Ajusta el padding para el espacio interno del enlace */
    border: 2px solid #007bff; /* Añade un borde al enlace */
    border-radius: 5px; /* Redondea los bordes del enlace */
    transition: background-color 0.3s, color 0.3s; /* Agrega una transición suave al color de fondo y al color del texto */
}

.btn-back a:hover {
    background-color: #007bff; /* Cambia el color de fondo al pasar el ratón */
    color: #fff; /* Cambia el color del texto al pasar el ratón */
}


        .nav-container {
            display: flex;
            /* Cambia el diseño de bloque a flexbox */
            justify-content: space-between;
            /* Alinea los elementos hijos en los extremos */
            align-items: center;
            /* Centra verticalmente los elementos hijos */
            margin-bottom: 20px;
        }
        .nav-container .flex.items-center {
    display: flex; /* Cambia el diseño de bloque a flexbox */
    justify-content: space-between; /* Alinea los elementos hijos en los extremos */
    align-items: center; /* Centra verticalmente los elementos hijos */
}


.nav-container a,
.nav-container form button {
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    color: #fff;
    margin-left: 10px; /* Agrega un margen izquierdo */
    margin-top: 10px;
    margin-right: 10px; /* Agrega un margen derecho */
    display: inline-block;
    background-color: #007bff;
    border: none;
    cursor: pointer;
    font-size: 16px;
    line-height: 1.5;
}


        .nav-container a.home,
        .nav-container a.login,
        .nav-container form button.logout {
            background-color: #0056b3;
            /* Cambia el color de fondo a azul oscuro */
        }

        .nav-container a.dashboard {
            background-color: #007bff;
        }

        .nav-container form button.logout {
            background-color: #dc3545;
        }

        .nav-container a.home:hover,
        .nav-container form button.logout:hover {
            background-color: #003e7f;
            /* Cambia el color de fondo al pasar el ratón */
        }

        .nav-container a.login {
            background-color: #28a745;
        }

        .nav-container a.register {
            background-color: #ffc107;
        }
    </style>
</head>

<body>
    <div class="nav-container">
        @auth
            <a href="{{ url('/dashboard') }}"
                style="text-decoration: none; color: #fff; margin-right: auto; margin-left: 10px;">{{ Auth::user()->name }}</a>
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

    <div class="container">
        <h1>Material de Natación</h1>
        <p>Para practicar natación se necesita tener un mínimo de material: bañador y gafas (tus ojos te lo
            agradecerán). Sin embargo, cuando nos preparamos para mejorar nuestra técnica, para alguna competición o
            travesía, necesitamos material tanto para los entrenamientos como el día de la prueba que queramos realizar.
        </p>

        <div class="card-container"> <!-- Nuevo contenedor para las tarjetas -->
            <div class="card">
                <img src="{{ asset('images/aletas.jpg') }}" alt="Aletas de Natación">
                <h2>Aletas</h2>
                <p>Material muy útil para nuestro entrenamiento.</p>
            </div>

            <div class="card">
                <img src="{{ asset('images/gafas.jpg') }}" alt="Gafas de Natación">
                <h2>Gafas de natación</h2>
                <p>Porque nuestra visión cuando nadamos es esencial.</p>
            </div>

            <div class="card">
                <img src="{{ asset('images/gorro.jpg') }}" alt="Gorro de Natación">
                <h2>Gorro de natación</h2>
                <p>Una prenda imprescindible para tus entrenamientos.</p>
            </div>

            <div class="card">
                <img src="{{ asset('images/tabla.jpg') }}" alt="Tabla de Nadar">
                <h2>Tabla de natación</h2>
                <p>Utiliza este complemento para un mejor nado.</p>
            </div>

            <div class="card">
                <img src="{{ asset('images/Pull.jpg') }}" alt="Pull buoy">
                <h2>Pull buoy</h2>
                <p>Mejora la técnica de nado y tu posición en el agua.</p>
            </div>

            <div class="card">
                <img src="{{ asset('images/Tubo.jpg') }}" alt="Tubo de natación">
                <h2>Tubo de natación</h2>
                <p>Para mejorar tu técnica en tus entrenamientos.</p>
            </div>

        </div>

        <div class="btn-back">
            <a href="{{ route('help') }}">Volver a Ayuda</a>
        </div>
    </div>
</body>

</html>
