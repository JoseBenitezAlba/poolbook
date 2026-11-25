@extends('layouts.app')

@section('title', 'Ayuda · PoolBook')
@section('meta_description', 'Guías, material y estilos de natación, además de tutoriales para usar PoolBook.')

@section('content')
<link rel="stylesheet" href="{{ asset('css/ayuda.css') }}">

<div class="container">
    <div class="ayuda-hero">
        <h1>¡Bienvenid@ a PoolBook!</h1>
        <p>Tu guía completa para el mundo de la natación. Aquí encontrarás todo lo que necesitas saber para empezar y mejorar en este maravilloso deporte.</p>
    </div>

    <div class="ayuda-card-container">
        <div class="ayuda-card">
            <h2>Material de natación</h2>
            <p>Descubre el equipo esencial para tus entrenamientos y competiciones.</p>
            <a href="{{ route('natacion.material') }}" class="btn btn-outline-primary">Ver más</a>
        </div>
        <div class="ayuda-card">
            <h2>Estilos de natación</h2>
            <p>Entrenamientos adaptados a todos los niveles.</p>
            <a href="{{ route('natacion.entrenamiento') }}" class="btn btn-outline-primary">Ver más</a>
        </div>
    </div>

    <div class="ayuda-cta">
        <a href="{{ route('ayuda.app') }}" class="btn btn-cta">Ayuda de la aplicación</a>
    </div>
</div>
@endsection