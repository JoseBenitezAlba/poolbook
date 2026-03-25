<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estilos de Natación</title>
    <style>
        /* Agrega tus estilos aquí */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            background-color:#cedfec;
        }
        .image {
            
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 50px auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 50px;
            font-size: 48px;
        }

        h3 {
            margin-top: 30px;
            margin-bottom: 10px;
            font-size: 36px;

        }

        p {
            margin-bottom: 20px;
        }
        .description {
             margin-bottom: 20px;
        }
        .btn-back {
    margin-top: 24px;
    text-align: center;
}

.btn-back a {
    text-decoration: none;
    color: #fff;
    background-color: #007bff;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s ease; /* Añade una transición suave */
}

.btn-back a:hover {
    background-color: #0056b3; /* Cambia el color al pasar el ratón */
}

        .nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;

    padding: 10px; /* Añade un poco de espacio alrededor de los elementos */
}

.nav-container .flex.items-center {
    display: flex; /* Cambia el diseño de bloque a flexbox */
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
            @auth
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="login">Log in</a>
            @endauth
            <a href="{{ url('/home') }}" class="home">Home</a>
        </div>
    </div>
    
    
    <div class="container">
        <h2>Los principales estilos de natación</h2>
        
        <h3>Crol</h3>
        <div class="description">
            <p>Conocida también como “Crawl” proveniente de su origen inglés que significa arrastrarse, es la modalidad más común practicada y enseñada, además de ser considerada como la básica dentro de la disciplina.</p>
            <p>La posición característica que debe tener el cuerpo del nadador al llevar a cabo este estilo es: en posición boca abajo realizando brazadas de forma alternativa, es decir primero utilizando un brazo y luego el otro combinado a un movimiento continuo de patadas cuyo número es variable. Es hasta ahora la modalidad más rápida de las cuatro existentes, con los mejores tiempos registrados.</p>
            <p>Se debe tener en cuenta que al momento de dar el giro para realizar la siguiente piscina el cuerpo del nadador debe encontrarse por completo sumergido y tocar la pared de la piscina al hacerlo.</p>
        </div>
        <div class="image">
            <img decoding="async" src="https://todonatacion.net/wp-content/uploads/2018/06/estilo-crawl.gif" data-src="https://todonatacion.net/wp-content/uploads/2018/06/estilo-crawl.gif" alt="Nadar estilo crol" class="wp-image-417 lazyloaded">
        </div>
        <h3>Espalda</h3>
        <div class="description">
            <p>Similar al estilo Crol en cuanto a las brazadas y patadas excepto por la posición del cuerpo, en ésta se está en posición dorsal apoyándola en el agua. Es conocida también como Crol de espalda.</p>
            <p>Debido a la posición del cuerpo la salida no es como en los otros estilos, en esta los nadadores se encuentran agarrados a los asideros de las plataformas de la respectiva salida con los pies sumergidos por completo. Al momento de dar las vueltas este debe volver a su posición de espaldas luego de abandonar la pared.</p>
        </div>
        <div class="image">
            <img decoding="async" src="https://todonatacion.net/wp-content/uploads/2018/06/estilo-espalda.gif" data-src="https://todonatacion.net/wp-content/uploads/2018/06/estilo-espalda.gif" alt="Nadar a espalda" class="wp-image-418 lazyloaded">
        </div>
        <h3>Braza</h3>
        <div class="description">
            <p>Nadar a braza es otro de los estilos de natación más complejos, tanto por su técnica como por su exigencia física. Muchas veces confundimos el estilo de braza con un estilo relajado que se suele realizar en la piscina para descansar de otros tipos de estilo como crol o mariposa.</p>
            <p>Es el estilo más lento, aunque posee una forma de realizarlo bastante natural donde los brazos y piernas se mueven de forma simétrica y simultánea, el nadador se encuentra en posición ventral con movimientos ascendentes y descendentes de hombros y caderas. Las manos se mueven desde impulsarse juntas frente al pecho hasta estar con los brazos en cruz.</p>
            <p>En este caso, el nadador debe flotar boca abajo, al tiempo que mantiene las puntas de sus manos unidas para realizar un movimiento en forma de círculo con sus brazos bajo el agua, primero hacia adelante y luego hacia atrás para tomar impulso. Mientras esto sucede, la cabeza sale del agua para respirar, y las piernas se recogen a la altura de la cintura y luego se estiran para ayudar al desplazamiento.</p>
        </div>
        <div class="image">
            <img decoding="async" src="https://todonatacion.net/wp-content/uploads/2018/06/estilo-braza-300x114.gif" data-src="https://todonatacion.net/wp-content/uploads/2018/06/estilo-braza-300x114.gif" alt="Natación a braza" class="wp-image-420 lazyloaded">
        </div>
        <h3>Mariposa</h3>
        <div class="description">
        <p>Es uno de los estilos más complejos en las modalidades de la natación, destacando por su exigencia técnica y física. La patada característica es la conocida como patada delfín, donde las piernas se mantienen unidas y realizan un movimiento serpenteante. Mientras tanto, los brazos se mueven de forma simultánea, con el recobro realizado por encima del agua para permitir la respiración. Este estilo se caracteriza por un movimiento ondulatorio en todo el cuerpo.</p>
        <p>El estilo mariposa implica mover ambos brazos de manera simultánea hacia adelante, por fuera del agua, y luego llevarlos hacia atrás dentro del agua. Es uno de los estilos de natación más difíciles de dominar debido a su complejidad técnica.</p>
        <p>Las piernas también juegan un papel crucial en el estilo mariposa, moviéndose de manera conjunta con la patada de delfín. Esta patada contribuye al desplazamiento del nadador mediante una ondulación del cuerpo. La respiración se realiza cada vez que la cabeza del nadador sale del agua.</p>
        <p>Debido a su exigencia técnica y física, el estilo mariposa es uno de los más desafiantes de dominar adecuadamente y requiere una gran resistencia física, ya que es el estilo que más calorías consume.</p>
        </div>
        <div class="image">
            <img decoding="async" src="https://todonatacion.net/wp-content/uploads/2018/06/Estilo-mariposa.gif" data-src="https://todonatacion.net/wp-content/uploads/2018/06/Estilo-mariposa.gif" alt="Natación estilo Mariposa" class="wp-image-419 lazyloaded">
        </div>
        <div class="btn-back">
            <a href="{{ route('help') }}">Volver a Ayuda</a>
        </div>
        
       
    </div>
</body>
</html>
