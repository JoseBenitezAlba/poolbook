// ============================================================
// CALENDARIO DE RESERVAS - PoolBook
// Usa FullCalendar (vista de recursos/carriles) + SweetAlert2
// para las confirmaciones, y un chat de asistente con IA (Gemini).
// ============================================================

/**
 * Calcula el primer día "disponible" para mostrar al abrir el calendario.
 * Reglas: los domingos no hay servicio, los sábados cierran a las 14h,
 * y entre semana el horario termina a las 22h — si ya pasó, saltamos al día siguiente.
 */
function getNextAvailableDay() {
    const now = new Date();
    const day = now.getDay();
    const hour = now.getHours();

    let target = new Date(now);

    // Si es domingo, o sábado después del cierre (14h) -> saltar a lunes
    if (day === 0 || (day === 6 && hour >= 14)) {
        target.setDate(now.getDate() + (day === 0 ? 1 : 2));
    }
    // Si es día de semana después del cierre (22h) -> saltar al día siguiente
    else if (day !== 0 && day !== 6 && hour >= 22) {
        target.setDate(now.getDate() + 1);
        if (target.getDay() === 0) {
            target.setDate(target.getDate() + 1);
        }
    }

    // Formato YYYY-MM-DD sin conversión UTC (evita desfases de zona horaria)
    const year = target.getFullYear();
    const month = String(target.getMonth() + 1).padStart(2, '0');
    const day2 = String(target.getDate()).padStart(2, '0');
    return `${year}-${month}-${day2}`;
}

/**
 * Redondea una fecha/hora al múltiplo de minutos indicado.
 * Se usa para que un clic en el calendario "encaje" siempre en horas en punto.
 */
function roundTime(date, minutes) {
    const coeff = 1000 * 60 * minutes;
    return new Date(Math.round(date.getTime() / coeff) * coeff);
}

/**
 * Convierte una cita tal como llega del backend (/citas) al formato
 * que espera FullCalendar. El backend ya decide, según el rol de quien
 * pregunta, si manda el nombre real (admin) o "Ocupado" (usuario normal),
 * y si esa cita se puede gestionar (dueño o admin) via `puede_gestionar`.
 */
function mapearEvento(event) {
    return {
        id: event.id,
        title: event.title,
        start: event.start,
        end: event.end,
        resourceId: event.resource_id,
        // Tu reserva en coral (destaca, es "lo tuyo"); las ocupadas en
        // teal oscuro (parte del sistema, no reclaman atención).
        backgroundColor: event.es_propietaria ? 'var(--coral)' : 'var(--teal-700)',
        borderColor: event.es_propietaria ? 'var(--coral-dark)' : 'var(--teal-900)',
        extendedProps: {
            day_of_week: event.day_of_week,
            date: event.date,
            esPropietaria: event.es_propietaria,
            puedeGestionar: event.puede_gestionar,
        }
    };
}

// ------------------------------------------------------------
// MODO EDICIÓN: al pulsar "Editar" en una reserva propia, guardamos
// aquí qué cita se está moviendo. Mientras esta variable no sea null,
// el siguiente clic en el calendario (dateClick) no crea una reserva
// nueva: mueve esta.
// ------------------------------------------------------------
let citaEnEdicion = null;

function mostrarBannerEdicion() {
    if (document.getElementById('edicion-banner')) return;

    const banner = document.createElement('div');
    banner.id = 'edicion-banner';
    banner.style.cssText = `
        position: fixed; top: 0; left: 0; right: 0; z-index: 1200;
        background: var(--teal-900, #0B4F58); color: #fff;
        padding: 10px 16px; text-align: center; font-family: var(--font-body, sans-serif);
        font-size: 14px;
    `;
    banner.innerHTML = `
        Elige el nuevo hueco para tu reserva
        <button id="edicion-cancelar" style="margin-left: 12px; background: var(--coral, #F2704A); color: #fff; border: none; border-radius: 6px; padding: 4px 10px; cursor: pointer; font-size: 13px;">
            Cancelar edición
        </button>
    `;
    document.body.appendChild(banner);

    document.getElementById('edicion-cancelar').addEventListener('click', ocultarBannerEdicion);
}

function ocultarBannerEdicion() {
    citaEnEdicion = null;
    const banner = document.getElementById('edicion-banner');
    if (banner) banner.remove();
}

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    fetch('/citas')
        .then(response => response.json())
        .then(data => {
            const events = data.map(mapearEvento);

            const calendar = new FullCalendar.Calendar(calendarEl, {
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
                        click: function () {
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
                // NOTA: estas franjas son solo visuales (sombrean el calendario).
                // Las reglas reales de negocio están validadas en el backend (Cita::validarReserva),
                // así que aunque esto cambiara, el backend seguiría protegiendo.
                businessHours: [
                    { daysOfWeek: [1, 2, 3, 4, 5], startTime: '09:00', endTime: '22:00' },
                    { daysOfWeek: [6], startTime: '09:00', endTime: '14:00' }
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

                // Clic en una celda del calendario -> intentar crear una reserva
                dateClick: function (info) {
                    if (!window.isAuthenticated) {
                        Swal.fire({
                            title: 'Inicia sesión',
                            text: 'Debes iniciar sesión para crear citas.',
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }

                    const resourceId = info.resource.id;
                    const ahora = new Date();

                    if (info.date < ahora) {
                        Swal.fire('Hora no disponible', 'Esta hora ya ha pasado.', 'warning');
                        return;
                    }

                    const startTime = roundTime(info.date, 60);
                        const endTime = new Date(startTime.getTime() + 55 * 60 * 1000);

                        // Comprobación final con la hora exacta que se enviará.
                        // Impide reservar una franja de hoy que ya ha empezado.
                        if (startTime <= new Date()) {
                            Swal.fire('Hora no disponible', 'Esta hora ya ha pasado.', 'warning');
                            return;
                        }

                        // Validaciones rápidas en el frontend (solo para dar feedback
                        // inmediato sin esperar al servidor; la validación real y
                        // definitiva vive en el backend, en Cita::validarReserva).
                        if (info.date.getDay() === 0) {
                            Swal.fire('No se pueden programar citas los domingos.');
                            return;
                        }

                        const hour = startTime.getUTCHours();

                        if (info.date.getDay() === 6 && hour >= 14) {
                            Swal.fire('No se pueden programar citas después de las 2 p.m. los sábados.');
                            return;
                        }

                        if (hour < 9 || hour >= 22) {
                            Swal.fire('No se pueden programar citas antes de las 9 a.m. o después de las 10 p.m.');
                            return;
                        }

                        const existingEvents = calendar.getEvents().filter(event => {
                            return (
                                event.resourceId !== 'carrilInvisible' &&
                                event.start < endTime && event.end > startTime &&
                                event.resourceId === resourceId
                            );
                        });

                        const conflictingHourCounts = {};
                        existingEvents.forEach(event => {
                            const eventHour = new Date(event.start).getHours();
                            conflictingHourCounts[eventHour] = (conflictingHourCounts[eventHour] || 0) + 1;
                        });

                        if (conflictingHourCounts[hour] >= 2) {
                            Swal.fire('Conflicto', 'Ya existen dos citas en ese horario y carril.', 'error');
                            return;
                        }

                        // Banner de confirmación: evita crear una reserva por un
                        // misclic, mostrando claramente fecha, hora y carril antes
                        // de enviar nada al backend.
                        const resourceTitle = info.resource.title || resourceId;
                        const fechaFormateada = startTime.toLocaleDateString('es-ES', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            timeZone: 'Europe/Madrid'
                        });
                        const horaFormateada = startTime.toLocaleTimeString('es-ES', {
                            hour: '2-digit',
                            minute: '2-digit',
                            timeZone: 'Europe/Madrid'
                        });

                        // --- MODO EDICIÓN: moviendo una cita ya existente ---
                        if (citaEnEdicion) {
                            const citaId = citaEnEdicion;

                            Swal.fire({
                                title: 'Confirmar cambio',
                                html: `¿Mover tu reserva al <b>${fechaFormateada}</b> a las <b>${horaFormateada}</b> en <b>${resourceTitle}</b>?`,
                                showCancelButton: true,
                                confirmButtonText: 'Sí, mover',
                                cancelButtonText: 'Cancelar'
                            }).then((confirmResult) => {
                                if (!confirmResult.isConfirmed) return;

                                const datosActualizados = {
                                    start: startTime.toISOString(),
                                    end: endTime.toISOString(),
                                    resourceId: resourceId,
                                    extendedProps: {
                                        day_of_week: info.date.getUTCDay(),
                                        date: info.date.toISOString().split('T')[0],
                                    }
                                };

                                fetch(`/citas/${citaId}`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': window.csrfToken,
                                    },
                                    body: JSON.stringify(datosActualizados)
                                })
                                    .then(response => {
                                        if (!response.ok) {
                                            return response.json().then(error => {
                                                throw new Error(error.error || 'Error desconocido');
                                            });
                                        }
                                        return response.json();
                                    })
                                    .then(() => {
                                        ocultarBannerEdicion();
                                        if (window.refrescarEventosCalendario) window.refrescarEventosCalendario();
                                        Swal.fire('Movida', 'Tu reserva se ha actualizado.', 'success');
                                    })
                                    .catch((error) => {
                                        Swal.fire('Error', error.message, 'error');
                                    });
                            });
                            return;
                        }

                        // --- MODO NORMAL: crear una reserva nueva ---
                        Swal.fire({
                            title: 'Confirmar reserva',
                            html: `¿Quieres reservar el <b>${fechaFormateada}</b> a las <b>${horaFormateada}</b> en <b>${resourceTitle}</b>?`,
                            showCancelButton: true,
                            confirmButtonText: 'Sí, reservar',
                            cancelButtonText: 'Cancelar'
                        }).then((confirmResult) => {
                            if (!confirmResult.isConfirmed) return;

                            const newEvent = {
                                title: 'Mi reserva',
                                start: startTime.toISOString(),
                                end: endTime.toISOString(),
                                resourceId: resourceId,
                                backgroundColor: 'var(--coral)',
                                borderColor: 'var(--coral-dark)',
                                extendedProps: {
                                    day_of_week: info.date.getUTCDay(),
                                    date: info.date.toISOString().split('T')[0],
                                    esPropietaria: true,
                                    puedeGestionar: true,
                                }
                            };

                            // La validación DEFINITIVA de todas estas reglas ocurre aquí,
                            // en el backend (CitaController -> Cita::validarReserva).
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
                        });
                },

                // Clic en una cita ya existente -> ofrecer eliminarla
                // (dueño de la reserva, o admin, que puede gestionar cualquiera)
                eventClick: function (info) {
                    if (!window.isAuthenticated) {
                        Swal.fire({
                            title: 'Inicia sesión',
                            text: 'Debes iniciar sesión para eliminar citas.',
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }

                    if (!info.event.extendedProps.puedeGestionar) {
                        Swal.fire('Horario ocupado', 'Esta reserva pertenece a otro usuario.', 'info');
                        return;
                    }

                    // Si es admin viendo la reserva de otra persona, el título
                    // ya trae el nombre real (lo manda así el backend), así que
                    // se ve claramente de quién es antes de cancelarla o moverla.
                    const nombreReserva = info.event.title;

                    Swal.fire({
                        title: nombreReserva,
                        text: '¿Qué quieres hacer con esta reserva?',
                        icon: 'question',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Editar',
                        denyButtonText: 'Cancelar reserva',
                        cancelButtonText: 'Cerrar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // --- Entrar en modo edición ---
                            citaEnEdicion = info.event.id;
                            mostrarBannerEdicion();
                            Swal.fire({
                                icon: 'info',
                                title: 'Modo edición activado',
                                text: 'Haz clic en el nuevo día/hora/carril donde quieres mover esta reserva.',
                                timer: 3000,
                                showConfirmButton: false
                            });
                            return;
                        }

                        if (!result.isDenied) return; // ni editar ni cancelar -> "Cerrar"

                        // --- Cancelar la reserva (comportamiento de siempre) ---
                        Swal.fire({
                            title: '¿Estás seguro de que deseas eliminar esta cita?',
                            html: `Reserva de: <b>${nombreReserva}</b><br>Esta acción no se puede deshacer.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'No, cancelar'
                        }).then((confirmResult) => {
                            if (!confirmResult.isConfirmed) return;

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
                                            throw new Error(error.message || error.error || 'Error desconocido');
                                        });
                                    }
                                    return response.json();
                                })
                                .then(() => {
                                    info.event.remove();
                                    Swal.fire('Eliminado', 'La cita ha sido eliminada.', 'success');
                                })
                                .catch((error) => {
                                    Swal.fire('Error', error.message, 'error');
                                });
                        });
                    });
                }
            });

            calendar.render();

            // Se exponen en window para que el chat del asistente pueda refrescar
            // el calendario tras crear una reserva por voz/texto.
            window.poolCalendar = calendar;
            window.refrescarEventosCalendario = function () {
                fetch('/citas')
                    .then(response => response.json())
                    .then(data => {
                        const eventosFrescos = data.map(mapearEvento);
                        calendar.removeAllEvents();
                        eventosFrescos.forEach(e => calendar.addEvent(e));
                    });
            };
        });
});

// ============================================================
// CHAT DEL ASISTENTE (Gemini)
// Este bloque de HTML solo se renderiza si el usuario está logueado
// (@auth en calendario.blade.php), así que si no lo está, los elementos
// #asistente-btn, #asistente-panel, etc. no existen en la página.
// Por eso comprobamos que 'btn' exista antes de seguir: sin esta guarda,
// un usuario no logueado provocaría un error de JS ("Cannot read
// properties of null") al intentar hacer btn.addEventListener(...).
// ============================================================
(function () {
    const btn = document.getElementById('asistente-btn');

    if (!btn) {
        // No hay usuario logueado (o el botón no existe por otro motivo) -> no hacemos nada
        return;
    }

    let historialGemini = [];
    const panel = document.getElementById('asistente-panel');
    const mensajesEl = document.getElementById('asistente-mensajes');
    const form = document.getElementById('asistente-form');
    const input = document.getElementById('asistente-input');

    btn.addEventListener('click', function () {
        panel.classList.toggle('open');
        if (panel.classList.contains('open')) input.focus();
    });

    function agregarMensaje(texto, clase) {
        const div = document.createElement('div');
        div.className = clase;
        div.textContent = texto;
        mensajesEl.appendChild(div);
        mensajesEl.scrollTop = mensajesEl.scrollHeight;
        return div;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const texto = input.value.trim();
        if (!texto) return;

        agregarMensaje(texto, 'msg-usuario');
        input.value = '';
        const pensando = agregarMensaje('Pensando...', 'msg-ia pensando');

        fetch('/asistente', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
            },
            body: JSON.stringify({ message: texto, history: historialGemini })
        })
            .then(res => res.json())
            .then(data => {
                pensando.remove();
                agregarMensaje(data.reply || 'No he podido responder.', 'msg-ia');
                historialGemini = data.history || historialGemini;
                if (window.refrescarEventosCalendario) window.refrescarEventosCalendario();
            })
            .catch(() => {
                pensando.remove();
                agregarMensaje('Error de conexión con el asistente.', 'msg-ia');
            });
    });
})();