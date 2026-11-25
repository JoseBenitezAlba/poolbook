@extends('layouts.app')

@section('title', 'Mi perfil · PoolBook')

@section('content')
<link rel="stylesheet" href="{{ asset('css/perfil.css') }}">

@php
    $usuario = Auth::user();
    $bonosActivos = $usuario->bonos()
        ->where('activo', true)
        ->where(function ($q) {
            $q->whereNull('fecha_caducidad')
                ->orWhereDate('fecha_caducidad', '>=', now()->toDateString());
        })
        ->get();
    $iniciales = collect(explode(' ', $usuario->name))
        ->map(fn ($palabra) => mb_strtoupper(mb_substr($palabra, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<div class="container-fluid perfil-wrap">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body perfil-header">
                    <div class="perfil-avatar">{{ $iniciales }}</div>
                    <div>
                        <h4 class="mb-1">{{ $usuario->name }}</h4>
                        <p class="text-muted mb-0">{{ __('Miembro desde') }} {{ $usuario->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">{{ __('Información personal') }}</div>
                <div class="card-body">
                    <div class="perfil-dato">
                        <span class="perfil-dato__label">{{ __('Correo electrónico') }}</span>
                        <span>{{ $usuario->email }}</span>
                    </div>
                    <div class="perfil-dato">
                        <span class="perfil-dato__label">{{ __('Teléfono') }}</span>
                        <span>{{ $usuario->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="perfil-dato">
                        <span class="perfil-dato__label">{{ __('Cuenta creada') }}</span>
                        <span>{{ $usuario->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="card mb-5">
                <div class="card-header">{{ __('Tus bonos') }}</div>
                <div class="card-body">
                    @forelse ($bonosActivos as $bono)
                        <div class="perfil-bono">
                            <div>
                                <strong>{{ $bono->sesiones_adquiridas - $bono->sesiones_gastadas }}</strong>
                                {{ __('sesiones restantes de') }} {{ $bono->sesiones_adquiridas }}
                            </div>
                            <span class="text-muted">
                                {{ $bono->fecha_caducidad
                                    ? __('Caduca el') . ' ' . \Carbon\Carbon::parse($bono->fecha_caducidad)->format('d/m/Y')
                                    : __('Sin caducidad') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No tienes ningún bono activo ahora mismo.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="d-flex gap-2 mb-5">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">{{ __('Atrás') }}</a>
                <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Inicio') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection