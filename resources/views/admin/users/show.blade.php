@extends('layouts.admin')

@section('title', $user->name . ' · PoolBook Admin')

@section('admin-content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $user->name }}</h1>
            <p class="text-muted mb-0">{{ $user->email }}</p>
        </div>
        <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Volver al listado</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Bonos -->
    <div class="card mb-4">
        <div class="card-header fw-semibold">Bonos</div>
        <div class="card-body p-0">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="text-center">Sesiones</th>
                        <th>Caduca</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bonos as $bono)
                        @php
                            $restantes = $bono->sesiones_adquiridas - $bono->sesiones_gastadas;
                            $caducado = $bono->fecha_caducidad && \Carbon\Carbon::parse($bono->fecha_caducidad)->isPast();
                        @endphp
                        <tr>
                            <td class="text-center">{{ $restantes }} / {{ $bono->sesiones_adquiridas }}</td>
                            <td>
                                {{ $bono->fecha_caducidad ? \Carbon\Carbon::parse($bono->fecha_caducidad)->format('d/m/Y') : 'Sin caducidad' }}
                            </td>
                            <td class="text-center">
                                @if (!$bono->activo)
                                    <span class="badge bg-secondary">Desactivado</span>
                                @elseif ($caducado)
                                    <span class="badge bg-danger">Caducado</span>
                                @elseif ($restantes <= 0)
                                    <span class="badge bg-warning text-dark">Agotado</span>
                                @else
                                    <span class="badge bg-success">Activo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if ($bono->activo)
                                    <form method="POST" action="{{ route('admin.bonos.desactivar', $bono) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-warning">Desactivar</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.bonos.destroy', $bono) }}" class="d-inline confirmar-borrado">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-1">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Este usuario no tiene bonos todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Añadir bono -->
    <div class="card mb-4">
        <div class="card-header fw-semibold">Añadir bono</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.bonos.store', $user) }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Nº de sesiones</label>
                    <input type="number" name="sesiones_adquiridas" min="1" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de caducidad (opcional)</label>
                    <input type="date" name="fecha_caducidad" class="form-control">
                    <div class="form-text">Déjalo en blanco si el bono no caduca.</div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Añadir bono</button>
                </div>
            </form>
            @error('sesiones_adquiridas')
                <p class="text-danger small mt-2">{{ $message }}</p>
            @enderror
            @error('fecha_caducidad')
                <p class="text-danger small mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Historial de reservas -->
    <div class="card">
        <div class="card-header fw-semibold">Últimas reservas</div>
        <div class="card-body p-0">
            <table class="table mb-0 small">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Carril</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($citas as $cita)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($cita->start)->format('d/m/Y H:i') }}</td>
                            <td>{{ $cita->resource_id }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-4">Sin reservas todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    // Confirmación antes de eliminar un bono por completo (acción destructiva).
    // Usa un confirm() nativo para no depender de que SweetAlert2 esté cargado
    // en este layout (tu layouts.admin no lo incluye).
    document.querySelectorAll('.confirmar-borrado').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('¿Eliminar este bono? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection