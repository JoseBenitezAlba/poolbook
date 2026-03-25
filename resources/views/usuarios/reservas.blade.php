@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reservas realizadas') }}</div>

                <div class="card-body">
                    <style>
                        /* Estilos para el fondo de pantalla */
                        body {
                            background-color: #164C75; /* Azul claro */
                            color: #333; /* Color de texto */
                        }

                        /* Estilos para la tarjeta de reserva */
                        .reserva {
                            margin-bottom: 20px;
                            padding: 20px;
                            border-radius: 10px;
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
                            background-color: #FFF; /* Fondo blanco */
                        }

                        /* Estilos para el título de la tarjeta de reserva */
                        .reserva h3 {
                            margin-bottom: 10px;
                            color: #333; /* Color de texto */
                        }

                        /* Estilos para los detalles de la reserva */
                        .reserva p {
                            font-weight: bold;
                            font-size: 16px;
                            color: #333; /* Color de texto */
                            margin-bottom: 5px;
                        }

                        /* Estilos para el contenedor de las reservas */
                        #reservas-realizadas {
                            margin-top: 20px;
                        }

                        /* Estilos para el botón de Atrás */
                        .btn-atras {
                            background-color: #e74c3c;
                            border-color: #e74c3c;
                        }

                        /* Estilos para el botón de Inicio */
                        .btn-inicio {
                            background-color: #3498db;
                            border-color: #3498db;
                            margin-left: 10px;
                        }

                        /* Estilos para los botones */
                        .btn {
                            color: #fff;
                            font-weight: bold;
                            border-radius: 5px;
                            padding: 10px 20px;
                            transition: background-color 0.3s;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3); /* Sombra suave */
                        }

                        /* Estilos para los botones al pasar el ratón por encima */
                        .btn:hover {
                            opacity: 0.8;
                        }
                        .card-body {
                            background: #cadce2;
                        }
                    </style>


                    <div id="reservas-realizadas">
                        <!-- Las reservas se agregarán aquí mediante JavaScript -->
                    </div>

                    <!-- Botones -->
                    <div class="mt-5">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg">{{ __('Atrás') }}</a>
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg">{{ __('Inicio') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para mostrar las reservas en la página
    function mostrarReservas(reservas) {
        let reservasContainer = document.getElementById('reservas-realizadas');
        reservasContainer.innerHTML = '';

        reservas.forEach(function(reserva, index) {
            let reservaNumero = index + 1;
            let fechaInicio = new Date(reserva.start);
            let fechaFin = new Date(reserva.end);

            let reservaDiv = document.createElement('div');
            reservaDiv.classList.add('reserva');
            reservaDiv.innerHTML = `
                <h3>Reserva ${reservaNumero}</h3>
                <p>Ficha de inicio: ${formatoFechaHora(fechaInicio)}</p>
                <p>Fecha de finalización: ${formatoFechaHora(fechaFin)}</p>
                <p>Nombre de la reserva: ${reserva.title}</p>
                <p>Carril: ${reserva.resource_id}</p>
            `;

            reservasContainer.appendChild(reservaDiv);

            // Agregar línea separadora entre reservas
            if (index < reservas.length - 1) {
                let separador = document.createElement('hr');
                reservasContainer.appendChild(separador);
            }
        });
    }

    // Función para formatear la fecha y la hora
    function formatoFechaHora(fecha) {
        let fechaFormateada = fecha.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
        return fechaFormateada;
    }

    // Obtener las reservas del servidor
    fetch('{{ route("citas.reservas") }}', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            
        }
    })
    .then(response => {
        // Verifica si la respuesta es válida
        if (response.ok) {
            return response.json(); // Convierte la respuesta a JSON
        } else {
            throw new Error('Error en la solicitud');
        }
    })
    .then(data => {
        // Verifica si data es un array
        if (Array.isArray(data)) {
            mostrarReservas(data);
        } else {
            console.error('Error: reservas no es un array');
        }
    })
    .catch(error => {
        console.error('Error al obtener las reservas:', error);
    });
});
</script>
@endsection
