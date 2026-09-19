{{--
    Página de estilos de natación. Es distinta a material.blade.php:
    ahí eran tarjetas cortas en rejilla, aquí es texto largo (varios
    párrafos por estilo) + una imagen cada uno, así que en vez de forzarlo
    dentro de .ayuda-card uso un componente nuevo, .estilo-block, pensado
    para artículo (ver ayuda.css).

    Igual que en help.blade.php y material.blade.php: extiende layouts.app
    en vez de traer su propio <nav> a mano, así el navbar es el mismo en
    toda la app.
--}}
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/ayuda.css') }}">

<div class="container">

    <div class="ayuda-hero">
        <h1>Los principales estilos de natación</h1>
    </div>

    <div class="estilo-block">
        <h3>Crol</h3>
        <p>Conocida también como "Crawl" proveniente de su origen inglés que significa arrastrarse, es la modalidad más común practicada y enseñada, además de ser considerada como la básica dentro de la disciplina.</p>
        <p>La posición característica que debe tener el cuerpo del nadador al llevar a cabo este estilo es: en posición boca abajo realizando brazadas de forma alternativa, es decir primero utilizando un brazo y luego el otro combinado a un movimiento continuo de patadas cuyo número es variable. Es hasta ahora la modalidad más rápida de las cuatro existentes, con los mejores tiempos registrados.</p>
        <p>Se debe tener en cuenta que al momento de dar el giro para realizar la siguiente piscina el cuerpo del nadador debe encontrarse por completo sumergido y tocar la pared de la piscina al hacerlo.</p>
        <img decoding="async" src="https://todonatacion.net/wp-content/uploads/2018/06/estilo-crawl.gif" alt="Nadar estilo crol">
    </div>

    <div class="estilo-block">
        <h3>Espalda</h3>
        <p>Similar al estilo Crol en cuanto a las brazadas y patadas excepto por la posición del cuerpo, en ésta se está en posición dorsal apoyándola en el agua. Es conocida también como Crol de espalda.</p>
        <p>Debido a la posición del cuerpo la salida no es como en los otros estilos, en esta los nadadores se encuentran agarrados a los asideros de las plataformas de la respectiva salida con los pies sumergidos por completo. Al momento de dar las vueltas este debe volver a su posición de espaldas luego de abandonar la pared.</p>
        <img decoding="async" src="https://todonatacion.net/wp-content/uploads/2018/06/estilo-espalda.gif" alt="Nadar a espalda">
    </div>

    <div class="estilo-block">
        <h3>Braza</h3>
        <p>Nadar a braza es otro de los estilos de natación más complejos, tanto por su técnica como por su exigencia física. Muchas veces confundimos el estilo de braza con un estilo relajado que se suele realizar en la piscina para descansar de otros tipos de estilo como crol o mariposa.</p>
        <p>Es el estilo más lento, aunque posee una forma de realizarlo bastante natural donde los brazos y piernas se mueven de forma simétrica y simultánea, el nadador se encuentra en posición ventral con movimientos ascendentes y descendentes de hombros y caderas. Las manos se mueven desde impulsarse juntas frente al pecho hasta estar con los brazos en cruz.</p>
        <p>En este caso, el nadador debe flotar boca abajo, al tiempo que mantiene las puntas de sus manos unidas para realizar un movimiento en forma de círculo con sus brazos bajo el agua, primero hacia adelante y luego hacia atrás para tomar impulso. Mientras esto sucede, la cabeza sale del agua para respirar, y las piernas se recogen a la altura de la cintura y luego se estiran para ayudar al desplazamiento.</p>
        <img decoding="async" src="https://todonatacion.net/wp-content/uploads/2018/06/estilo-braza-300x114.gif" alt="Natación a braza">
    </div>

    <div class="estilo-block">
        <h3>Mariposa</h3>
        <p>Es uno de los estilos más complejos en las modalidades de la natación, destacando por su exigencia técnica y física. La patada característica es la conocida como patada delfín, donde las piernas se mantienen unidas y realizan un movimiento serpenteante. Mientras tanto, los brazos se mueven de forma simultánea, con el recobro realizado por encima del agua para permitir la respiración. Este estilo se caracteriza por un movimiento ondulatorio en todo el cuerpo.</p>
        <p>El estilo mariposa implica mover ambos brazos de manera simultánea hacia adelante, por fuera del agua, y luego llevarlos hacia atrás dentro del agua. Es uno de los estilos de natación más difíciles de dominar debido a su complejidad técnica.</p>
        <p>Las piernas también juegan un papel crucial en el estilo mariposa, moviéndose de manera conjunta con la patada de delfín. Esta patada contribuye al desplazamiento del nadador mediante una ondulación del cuerpo. La respiración se realiza cada vez que la cabeza del nadador sale del agua.</p>
        <p>Debido a su exigencia técnica y física, el estilo mariposa es uno de los más desafiantes de dominar adecuadamente y requiere una gran resistencia física, ya que es el estilo que más calorías consume.</p>
        <img decoding="async" src="https://todonatacion.net/wp-content/uploads/2018/06/Estilo-mariposa.gif" alt="Natación estilo Mariposa">
    </div>

    <div class="ayuda-cta">
        <a href="{{ route('help') }}" class="btn btn-outline-primary">Volver a ayuda</a>
    </div>

</div>
@endsection