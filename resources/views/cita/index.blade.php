@extends('layouts.app')

@section('template_title')
    Cita
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                // Configuración de FullCalendar
                // Puedes personalizar el calendario según tus necesidades aquí
                initialView: 'dayGridMonth',
                events: @json($eventos) // Pasar los eventos desde el controlador
            });

            calendar.render();
        });
    </script>
@endsection
