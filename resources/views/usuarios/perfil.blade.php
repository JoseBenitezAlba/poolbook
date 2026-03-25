@extends('layouts.app')

@section('content')
<style>
    /* Estilo para el fondo de la página */
    body {
    background-image: url('/images/piscina.png'); /* Ruta a tu foto de fondo */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 100vh; /* Asegura que el fondo ocupe toda la altura de la pantalla */
    margin: 0; /* Elimina el margen predeterminado del cuerpo */
    padding: 0; /* Elimina el relleno predeterminado del cuerpo */
}

</style>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center display-4">{{ __('Perfil de Usuario') }}</div>

                <div class="card-body">
                    <h5 class="display-5 mb-4">{{ __('Información Personal') }}</h5>
                    <div class="mb-4 fs-5">
                        <p><strong>{{ __('Nombre:') }}</strong> {{ Auth::user()->name }}</p>
                        <p><strong>{{ __('Correo Electrónico:') }}</strong> {{ Auth::user()->email }}</p>
                        <p><strong>{{ __('Teléfono:') }}</strong> {{ Auth::user()->phone ?? 'N/A' }}</p>
                        <p><strong>{{ __('Fecha de creación de la cuenta:') }}</strong> {{ Auth::user()->created_at->format('d-m-Y') }}</p>
                    </div>
                    
                    <div class="mt-5">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg">{{ __('Atrás') }}</a>
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg">{{ __('Inicio') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
