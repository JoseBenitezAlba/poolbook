@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Dashboard</h1>

    <div class="metrics-row">
        <div class="metric-card">
            <div class="metric-value">{{ $totalCitas }}</div>
            <div class="metric-label">Citas próximas</div>
        </div>

        <div class="metric-card">
            <div class="metric-value">{{ $totalUsuarios }}</div>
            <div class="metric-label">Usuarios registrados</div>
        </div>
    </div>
</div>
@endsection