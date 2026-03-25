<!-- resources/views/admin/users/index.blade.php -->

@extends('layouts.admin')
@section('admin-content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Usuarios</h1>
                @if($users->isEmpty())
                    <p>No hay usuarios registrados.</p>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
