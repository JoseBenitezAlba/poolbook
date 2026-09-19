{{--
    Página de material de natación.
    Antes tenía todo su propio <head>/<style>/menú a mano (duplicando
    el navbar que ya existe en layouts.app). Ahora extiende layouts.app
    igual que help.blade.php, así que el navbar (login/logout/nombre
    de usuario) es el mismo de siempre y no hay que mantenerlo dos veces.
--}}
@extends('layouts.app')

@section('content')
{{-- Reutilizamos el mismo CSS que la página de ayuda principal --}}
<link rel="stylesheet" href="{{ asset('css/ayuda.css') }}">

<div class="container">

    {{-- Título + intro, con la misma clase .ayuda-hero que en help.blade.php
         para que el bloque de texto de arriba se vea igual en todas las
         páginas de ayuda (ancho máximo, centrado, mismo color de texto). --}}
    <div class="ayuda-hero">
        <h1>Material de natación</h1>
        <p>
            Para practicar natación se necesita tener un mínimo de material: bañador y gafas (tus ojos te lo
            agradecerán). Sin embargo, cuando nos preparamos para mejorar nuestra técnica, para alguna competición o
            travesía, necesitamos material tanto para los entrenamientos como el día de la prueba que queramos
            realizar.
        </p>
    </div>

    {{--
        Las 6 tarjetas de material. Uso .ayuda-card-container (flex + wrap)
        para que se acomoden solas en filas según el ancho de pantalla,
        en vez de fijar manualmente "33.333% - 20px" como tenía antes
        (eso se rompía en pantallas medianas, tipo tablet).
    --}}
    <div class="ayuda-card-container">
        <div class="ayuda-card">
            <img src="{{ asset('images/aletas.jpg') }}" alt="Aletas de natación">
            <h2>Aletas</h2>
            <p>Material muy útil para nuestro entrenamiento.</p>
        </div>

        <div class="ayuda-card">
            <img src="{{ asset('images/gafas.jpg') }}" alt="Gafas de natación">
            <h2>Gafas de natación</h2>
            <p>Porque nuestra visión cuando nadamos es esencial.</p>
        </div>

        <div class="ayuda-card">
            <img src="{{ asset('images/gorro.jpg') }}" alt="Gorro de natación">
            <h2>Gorro de natación</h2>
            <p>Una prenda imprescindible para tus entrenamientos.</p>
        </div>

        <div class="ayuda-card">
            <img src="{{ asset('images/tabla.jpg') }}" alt="Tabla de nadar">
            <h2>Tabla de natación</h2>
            <p>Utiliza este complemento para un mejor nado.</p>
        </div>

        <div class="ayuda-card">
            <img src="{{ asset('images/Pull.jpg') }}" alt="Pull buoy">
            <h2>Pull buoy</h2>
            <p>Mejora la técnica de nado y tu posición en el agua.</p>
        </div>

        <div class="ayuda-card">
            <img src="{{ asset('images/Tubo.jpg') }}" alt="Tubo de natación">
            <h2>Tubo de natación</h2>
            <p>Para mejorar tu técnica en tus entrenamientos.</p>
        </div>
    </div>

    {{-- Botón de vuelta, igual estilo que usamos en el resto del panel
         (btn-outline-primary, definido en theme.css) en vez del azul
         suelto #007bff que tenía antes. --}}
    <div class="ayuda-cta">
        <a href="{{ route('help') }}" class="btn btn-outline-primary">Volver a ayuda</a>
    </div>

</div>
@endsection