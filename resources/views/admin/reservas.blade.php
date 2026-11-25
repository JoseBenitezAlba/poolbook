@extends('layouts.admin')

@section('admin-content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="my-4">Todas las citas</h1>
                @if($citas->isEmpty())
                    <p>No hay citas registradas.</p>
                @else
                    <table class="table table-striped">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Fecha y Hora de Inicio</th>
                                <th>Fecha y Hora de Fin</th>
                                <th>Carril</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($citas as $cita)
                                <tr>
                                    <td>{{ $cita->id }}</td>
                                    <td>{{ $cita->user->name }}</td>
                                    <td>{{ $cita->start }}</td>
                                    <td>{{ $cita->end }}</td>
                                    <td>{{ $cita->resource_id }}</td>
                                    <td>
                                        <form action="{{ route('citas.destroy', $cita->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
