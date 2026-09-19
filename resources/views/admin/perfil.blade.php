@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Perfil de administrador</h1>

    <div class="card mb-4">
        <div class="card-body perfil-header">
            <div class="perfil-avatar">
                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <h4 class="mb-1">{{ auth()->user()->name }}</h4>
                <p class="text-muted mb-0">{{ auth()->user()->email }} · <span class="badge bg-secondary">Administrador</span></p>
            </div>
        </div>
    </div>

    <h2 class="h5 mb-3">Estadísticas rápidas</h2>
    <div class="metrics-row mb-4">
        <div class="metric-card">
            <div class="metric-value">{{ \App\Models\User::count() }}</div>
            <div class="metric-label">Usuarios registrados</div>
        </div>
        <div class="metric-card">
            <div class="metric-value">{{ \App\Models\Cita::count() }}</div>
            <div class="metric-label">Reservas totales</div>
        </div>
    </div>
</div>

<style>
    /* Mismo patrón de avatar que usuarios/perfil.blade.php, pero definido
       aquí porque este layout (admin) no carga perfil.css. */
    .perfil-header {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }
    .perfil-avatar {
        width: 56px;
        height: 56px;
        flex-shrink: 0;
        border-radius: 50%;
        background: var(--teal-900);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-display);
        font-size: 1.3rem;
        font-weight: 600;
    }
</style>
@endsection