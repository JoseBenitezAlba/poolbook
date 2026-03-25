@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('menu.css') }}">


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-5">
                    <div class="card-header">{{ __('Mi cuenta') }}</div>

                    <div class="card-body">
                        <!-- Nueva reserva -->
                        <div class="section py-4">
                            <h4 class="mb-3">{{ __('Nueva reserva') }}</h4>
                            <a href="{{ route('calendario') }}"
                                class="btn btn-lg btn-primary">{{ __('Ir al calendario') }}</a>
                        </div>
                        <hr>

                        <!-- Reservas realizadas -->
                        <div class="section py-4">
                            <h4 class="mb-3">{{ __('Reservas realizadas') }}</h4>
                            <a href="{{ route('reservas.index') }}"
                                class="btn btn-lg btn-primary">{{ __('Ver reservas') }}</a>
                        </div>
                        <hr>


                        <!-- Perfil -->
                        <div class="section py-4">
                            <h4 class="mb-3">{{ __('Perfil') }}</h4>
                            <a href="{{ route('perfil') }}" class="btn btn-lg btn-primary">{{ __('Ver perfil') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
