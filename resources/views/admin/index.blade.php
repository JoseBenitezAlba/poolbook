@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Usuarios</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th class="text-center">Sesiones restantes</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @php
                            $restantes = $user->bonos->sum(function ($bono) {
                                return $bono->sesiones_adquiridas - $bono->sesiones_gastadas;
                            });
                        @endphp
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td class="text-center">
                                <span class="{{ $restantes <= 2 ? 'text-danger fw-bold' : '' }}">
                                    {{ $restantes }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection