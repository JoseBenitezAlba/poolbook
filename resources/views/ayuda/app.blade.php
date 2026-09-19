{{--
    Ayuda de la aplicación: tutoriales en vídeo. Misma base que el resto
    de páginas de ayuda (extiende layouts.app, usa ayuda.css), con un
    componente nuevo para las tarjetas de vídeo (.ayuda-video-card),
    manteniendo el mismo truco de "padding-bottom: 56.25%" que ya tenías
    para que el iframe de YouTube sea responsive en 16:9.
--}}
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/ayuda.css') }}">

<div class="container" style="max-width: 720px;">

    <div class="ayuda-hero">
        <h1>Ayuda de la aplicación</h1>
        <p>Aquí encontrarás tutoriales en vídeo para aprender a usar PoolBook paso a paso.</p>
    </div>

    <div class="ayuda-video-card">
        <h2>📅 Cómo crear y eliminar una cita</h2>
        <p>Aprende a reservar un carril en el calendario y a eliminar una reserva existente.</p>
        <div class="ayuda-video-wrapper">
            <iframe src="https://www.youtube.com/embed/xGOlchSwi8w"
                title="Crear y eliminar cita - PoolBook"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>
    </div>

    <div class="ayuda-video-card">
        <h2>👤 Cómo ver tus reservas</h2>
        <p>Descubre cómo consultar todas las reservas que tienes realizadas desde tu perfil.</p>
        <div class="ayuda-video-wrapper">
            <iframe src="https://www.youtube.com/embed/-4ylQlJrt-8"
                title="Ver reservas - PoolBook"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>
    </div>

    <div class="ayuda-cta">
        <a href="{{ route('help') }}" class="btn btn-outline-primary">&larr; Volver a ayuda</a>
    </div>

</div>
@endsection