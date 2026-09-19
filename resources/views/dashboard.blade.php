@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('menu.css') }}">

<style>
    .accion-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-align: center;
        padding: 2rem 1.5rem;
    }
    .accion-card h4 {
        margin-bottom: 0.5rem;
    }
    .accion-card p {
        color: var(--ink-soft);
        font-size: 0.92rem;
        margin-bottom: 1.5rem;
    }
</style>

    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-md-10">
                <h2 class="mb-0">{{ __('Mi cuenta') }}</h2>
            </div>
        </div>

        <div class="row justify-content-center g-4">
            <div class="col-md-10">
                <div class="row g-4">
                    <!-- Nueva reserva -->
                    <div class="col-md-4">
                        <div class="card accion-card">
                            <div>
                                <h4>{{ __('Nueva reserva') }}</h4>
                                <p>{{ __('Elige día, hora y carril en el calendario.') }}</p>
                            </div>
                            <a href="{{ route('calendario') }}" class="btn btn-cta">
                                {{ __('Ir al calendario') }}
                            </a>
                        </div>
                    </div>

                    <!-- Reservas realizadas -->
                    <div class="col-md-4">
                        <div class="card accion-card">
                            <div>
                                <h4>{{ __('Mis reservas') }}</h4>
                                <p>{{ __('Consulta o cancela tus reservas actuales.') }}</p>
                            </div>
                            <a href="{{ route('reservas.index') }}" class="btn btn-outline-primary">
                                {{ __('Ver reservas') }}
                            </a>
                        </div>
                    </div>

                    <!-- Perfil -->
                    <div class="col-md-4">
                        <div class="card accion-card">
                            <div>
                                <h4>{{ __('Perfil') }}</h4>
                                <p>{{ __('Tus datos y el estado de tu cuenta.') }}</p>
                            </div>
                            <a href="{{ route('perfil') }}" class="btn btn-outline-primary">
                                {{ __('Ver perfil') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection