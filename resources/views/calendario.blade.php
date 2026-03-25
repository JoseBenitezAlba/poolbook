<!DOCTYPE html>

<html lang="en">
<head>

  <meta charset="utf-8" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.11/index.global.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <style>
    #calendar {
      width: 100%;
      height: 880px;
      border:blue !important;
    }
    .fc-view-harness{
        border:blue !important;
    }

    .carril-invisible .fc-datagrid-cell-main,
    .carril-invisible .fc-datagrid-cell-content {
      visibility: hidden;
      color: white;
    }

    .fc-datagrid-cell-frame {
      height: 180px;
    }

    .login-link, .register-link, .home-link {
      text-decoration: none;
      padding: 5px 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      transition: background-color 0.3s;
    }

    .login-link:hover, .register-link:hover, .home-link:hover {
      background-color: #f0f0f0;
    }

    .login-link {
      background-color: #007bff;
      color: #fff;
      border-color: #007bff;
    }

    .register-link {
      background-color: #28a745;
      color: #fff;
      border-color: #28a745;
    }

    .home-link {
      background-color: #ffc107;
      color: #000;
      border-color: #ffc107;
    }

    .login-link:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }

    .register-link:hover {
      background-color: #218838;
      border-color: #218838;
    }

    .home-link:hover {
      background-color: #e0a800;
      border-color: #e0a800;
    }
  </style>

<script>
  window.csrfToken = "{{ csrf_token() }}";
  window.isAuthenticated = @json(auth()->check());
  window.userId = @json(auth()->id());
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   function getNextAvailableDay() {
    const now = new Date();
    const day = now.getDay();
    const hour = now.getHours();

    let target = new Date(now);

    // Si es domingo O es sábado después de las 14 → lunes
    if (day === 0 || (day === 6 && hour >= 14)) {
        target.setDate(now.getDate() + (day === 0 ? 1 : 2));
    }
    // Si es día de semana después de las 22 → día siguiente
    else if (day !== 0 && day !== 6 && hour >= 22) {
        target.setDate(now.getDate() + 1);
        if (target.getDay() === 0) {
            target.setDate(target.getDate() + 1);
        }
    }

    // Devolver en formato YYYY-MM-DD sin UTC
    const year = target.getFullYear();
    const month = String(target.getMonth() + 1).padStart(2, '0');
    const day2 = String(target.getDate()).padStart(2, '0');
    return `${year}-${month}-${day2}`;
}

  document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');

      fetch('/citas')
    .then(response => response.json())
    .then(data => {
        var events = data.map(event => ({
            id: event.id,
            title: event.title,
            start: event.start,
            end: event.end,
            resourceId: event.resource_id,
            backgroundColor: event.user_id === window.userId ? 'blue' : 'red',
            extendedProps: {
                day_of_week: event.day_of_week,
                date: event.date,
            }
        }));

        var calendar = new FullCalendar.Calendar(calendarEl, {
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            timeZone: 'Europe/Madrid',
            initialView: 'resourceTimelineDay',
            initialDate: getNextAvailableDay(),
            aspectRatio: 1.5,
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: 'buttonHome resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
            },
            customButtons: {
                buttonHome: {
                    text: 'Home',
                    click: function() {
                        window.location.href = '/home';
                    }
                }
            },
            editable: false,
            selectable: true,
            resourceAreaHeaderContent: 'Carriles',
            slotMinTime: '00:00:00',
            slotMaxTime: '24:00:00',
            slotDuration: '01:00:00',
            dayMaxEvents: true,
            businessHours: [
                {
                    daysOfWeek: [1, 2, 3, 4, 5],
                    startTime: '09:00',
                    endTime: '22:00'
                },
                {
                    daysOfWeek: [6],
                    startTime: '09:00',
                    endTime: '14:00'
                }
            ],
            resources: [
                { id: 'carril1', title: 'Carril 1' },
                { id: 'carril2', title: 'Carril 2' },
                { id: 'carril3', title: 'Carril 3' },
                { id: 'carril4', title: 'Carril 4' },
                { id: 'carril5', title: 'Carril 5' },
                { id: 'carrilInvisible', title: ' ', className: 'carril-invisible' }
            ],
            events: events,
            dateClick: function(info) {
                if (window.isAuthenticated) {
                    var resourceId = info.resource.id;

                    // Bloquear si la hora ya pasó
                    const ahora = new Date();
                    if (info.date < ahora) {
                        Swal.fire('Hora no disponible', 'Esta hora ya ha pasado.', 'warning');
                        return;
                    }

                    Swal.fire({
                        title: '¿A nombre de quién irá la reserva?',
                        input: 'text',
                        showCancelButton: true,
                        confirmButtonText: 'Reservar',
                        cancelButtonText: 'Cancelar',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Debes escribir un nombre para la reserva';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var title = result.value;
                            var startTime = roundTime(info.date, 60);
                            var endTime = new Date(startTime.getTime() + 55 * 60 * 1000);

                            if (info.date.getDay() === 0) {
                                Swal.fire('No se pueden programar citas los domingos.');
                                return;
                            }

                            var hour = startTime.getUTCHours();

                            if (info.date.getDay() === 6 && hour >= 14) {
                                Swal.fire('No se pueden programar citas después de las 2 p.m. los sábados.');
                                return;
                            }

                            if (hour < 9 || hour >= 22) {
                                Swal.fire('No se pueden programar citas antes de las 9 a.m. o después de las 10 p.m.');
                                return;
                            }

                            var existingEvents = calendar.getEvents().filter(event => {
                                return (
                                    event.resourceId !== 'carrilInvisible' &&
                                    event.start < endTime && event.end > startTime &&
                                    event.resourceId === resourceId
                                );
                            });

                            var conflictingHourCounts = {};
                            existingEvents.forEach(event => {
                                var eventStartTime = new Date(event.start);
                                var eventHour = eventStartTime.getHours();
                                conflictingHourCounts[eventHour] = (conflictingHourCounts[eventHour] || 0) + 1;
                            });

                            var hour = startTime.getUTCHours();
                            if (conflictingHourCounts[hour] >= 2) {
                                Swal.fire('Conflicto', 'Ya existen dos citas en ese horario y carril.', 'error');
                                return;
                            }

                            var newEvent = {
                                title: title,
                                start: startTime.toISOString(),
                                end: endTime.toISOString(),
                                resourceId: resourceId,
                                extendedProps: {
                                    day_of_week: info.date.getUTCDay(),
                                    date: info.date.toISOString().split('T')[0],
                                }
                            };

                            fetch('/citas', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': window.csrfToken,
                                },
                                body: JSON.stringify(newEvent)
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(error => {
                                        throw new Error(error.error || 'Error desconocido');
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                newEvent.id = data.id;
                                calendar.addEvent(newEvent);
                            })
                            .catch((error) => {
                                Swal.fire('Error', error.message, 'error');
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Inicia sesión',
                        text: 'Debes iniciar sesión para crear citas.',
                        icon: 'info',
                        showCancelButton: false,
                        confirmButtonText: 'Entendido'
                    });
                }
            },
            eventClick: function(info) {
                if (window.isAuthenticated) {
                    Swal.fire({
                        title: '¿Estás seguro de que deseas eliminar esta cita?',
                        text: "Esta acción no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'No, cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/citas/${info.event.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': window.csrfToken,
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(error => {
                                        if (error.message) {
                                            throw new Error(error.message);
                                        }
                                        throw new Error(error.error || 'Error desconocido');
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                info.event.remove();
                                Swal.fire('Eliminado', 'La cita ha sido eliminada.', 'success');
                            })
                            .catch((error) => {
                                Swal.fire('Error', error.message, 'error');
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Inicia sesión',
                        text: 'Debes iniciar sesión para eliminar citas.',
                        icon: 'info',
                        showCancelButton: false,
                        confirmButtonText: 'Entendido'
                    });
                }
            }
        });

        calendar.render();

        function roundTime(date, minutes) {
            var coeff = 1000 * 60 * minutes;
            return new Date(Math.round(date.getTime() / coeff) * coeff);
        }
    });
  });
</script>
</head>
<body>
<div class="flex flex-col items-center bg-gray-100 p-4">
  <!-- Menú -->
  <div class="flex justify-end mb-4 w-full">
      @if (Route::has('login'))
          <div class="hidden sm:flex items-center space-x-4">
              @auth
                  <a href="{{ url('/dashboard') }}" class="py-2 px-4 bg-blue-500 text-white rounded-md">Dashboard</a>
                  <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="py-2 px-4 bg-red-500 text-white rounded-md">Logout</button>
                  </form>
              @else
                  <a href="{{ route('login') }}" class="py-2 px-4 bg-green-500 text-white rounded-md">Log in</a>
              @endif
          </div>
      @endif
  </div>
  <!-- Calendario -->
  <div id="calendar" class="mx-auto"></div>
</div>
</body>
</html>