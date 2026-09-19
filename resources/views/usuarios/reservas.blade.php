@extends('layouts.app')

@section('content')
<!-- Mismo fondo azul que el dashboard -->
<link rel="stylesheet" href="{{ asset('menu.css') }}">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-5">
                <div class="card-header">{{ __('Reservas realizadas') }}</div>

                <div class="card-body">
                    <div id="reservas-realizadas">
                        <p class="reserva__vacio">{{ __('Cargando tus reservas…') }}</p>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">{{ __('Atrás') }}</a>
                        <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Inicio') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2, para confirmar la cancelación igual que en el resto de la app -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- route() solo existe en Blade, no en un .js compilado por Vite:
     se lo pasamos a reservas.js como variables globales. -->
<script>
    window.misReservasUrl = "{{ route('citas.reservas') }}";
    window.citasBaseUrl = "{{ url('/citas') }}";
</script>

@vite(['resources/css/reservas.css', 'resources/js/reservas.js'])
@endsection