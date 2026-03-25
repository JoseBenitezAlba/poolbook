@extends('layouts.admin')

@section('admin-content')
<link rel="stylesheet" href="{{ asset('perfil-admin.css') }}"> <!-- opcional -->

<div class="container2">
    <div class="content">
        <h1 class="mb-4">Perfil de Administrador</h1>

        <div class="card mb-4" style="padding: 20px; background: #164C75; color: #fff; border-radius: 10px;">
            <h3>Información del admin</h3>
            <p><strong>Nombre:</strong> {{ auth()->user()->name }}</p>
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
            <p><strong>Rol:</strong> Administrador</p>
        </div>

        <div class="card mb-4" style="padding: 20px; background: #1A4B6E; color: #fff; border-radius: 10px;">
            <h3>Estadísticas rápidas</h3>
            <p><strong>Total de usuarios:</strong> {{ \App\Models\User::count() }}</p>
            <p><strong>Total de reservas:</strong> {{ \App\Models\Cita::count() }}</p>
        </div>
    </div>
</div>
@endsection