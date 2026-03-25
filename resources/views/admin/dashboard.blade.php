@extends('layouts.admin')

@section('admin-content')
<div class="metrics">
    <div class="info-box">
        <h3>Total de Citas</h3>
        <p>{{ $totalCitas }}</p>
    </div>

    <div class="info-box">
        <h3>Total de Usuarios</h3>
        <p>{{ $totalUsuarios }}</p>
    </div>
</div>

@endsection