document.addEventListener('DOMContentLoaded', function () {
    const contenedor = document.getElementById('reservas-realizadas');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function formatoFechaHora(fechaIso) {
        return new Date(fechaIso).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function nombreCarril(resourceId) {
        // resourceId llega como "carril3"; lo convertimos en "Carril 3"
        // en vez del redundante "Carril carril3".
        const numero = String(resourceId).replace(/[^0-9]/g, '');
        return numero ? `Carril ${numero}` : resourceId;
    }

    function pintarReservas(reservas) {
        contenedor.innerHTML = '';

        if (!reservas.length) {
            contenedor.innerHTML = '<p class="reserva__vacio">Todavía no tienes ninguna reserva.</p>';
            return;
        }

        reservas.forEach(function (reserva) {
            const div = document.createElement('div');
            div.className = 'reserva';
            div.innerHTML = `
                <div class="reserva__info">
                    <h3>${nombreCarril(reserva.resource_id)}</h3>
                    <p>${formatoFechaHora(reserva.start)} &rarr; ${formatoFechaHora(reserva.end)}</p>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm" data-id="${reserva.id}">
                    Cancelar
                </button>
            `;
            contenedor.appendChild(div);
        });

        contenedor.querySelectorAll('button[data-id]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                cancelarReserva(btn.dataset.id, btn);
            });
        });
    }

    function cancelarReserva(id, btn) {
        Swal.fire({
            title: '¿Cancelar esta reserva?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            fetch(`${window.citasBaseUrl}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
                .then(function (response) {
                    if (!response.ok) {
                        return response.json().then(function (error) {
                            throw new Error(error.message || error.error || 'Error desconocido');
                        });
                    }
                    return response.json();
                })
                .then(function () {
                    btn.closest('.reserva').remove();
                    if (!contenedor.querySelector('.reserva')) {
                        contenedor.innerHTML = '<p class="reserva__vacio">Todavía no tienes ninguna reserva.</p>';
                    }
                    Swal.fire('Cancelada', 'Tu reserva ha sido eliminada.', 'success');
                })
                .catch(function (error) {
                    Swal.fire('Error', error.message, 'error');
                });
        });
    }

    fetch(window.misReservasUrl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Error en la solicitud');
            }
            return response.json();
        })
        .then(function (data) {
            if (Array.isArray(data)) {
                pintarReservas(data);
            } else {
                console.error('Error: reservas no es un array');
                contenedor.innerHTML = '<p class="reserva__vacio">No se pudieron cargar tus reservas.</p>';
            }
        })
        .catch(function (error) {
            console.error('Error al obtener las reservas:', error);
            contenedor.innerHTML = '<p class="reserva__vacio">No se pudieron cargar tus reservas.</p>';
        });
});