# 🏊‍♂️ PoolBook

Aplicación web para la gestión y reserva de carriles de una piscina.

Es mi proyecto final de **Desarrollo de Aplicaciones Web (DAW)** y está desarrollado con **Laravel, MySQL y JavaScript**. La idea era hacer algo más que un simple calendario de reservas: usuarios, bonos de sesiones, administración, permisos y un asistente con IA capaz de consultar disponibilidad y realizar reservas.

## 🌐 Demo

**Aplicación:** https://poolbook.onrender.com/

**Vídeo de demostración:** https://youtu.be/JI9NC-3bdPs

También hay una sección `/proyecto` dentro de la aplicación donde explico con más detalle cómo está construido el proyecto y algunas de las decisiones técnicas que he tomado.

### Usuario de prueba

```text
Email: prueba@prueba.com
Contraseña: prueba
```

La aplicación está desplegada en el plan gratuito de Render, por lo que el primer acceso puede tardar unos segundos.

---

## ¿Qué se puede hacer con PoolBook?

### Como usuario

- Crear una cuenta e iniciar sesión.
- Consultar los carriles disponibles desde un calendario.
- Reservar un carril.
- Modificar o cancelar las propias reservas.
- Consultar las reservas realizadas.
- Gestionar los bonos de sesiones disponibles y su caducidad.
- Realizar reservas periódicas, por ejemplo, todos los lunes hasta una fecha determinada.
- Utilizar un asistente de IA para consultar disponibilidad y realizar reservas mediante lenguaje natural.
- Consultar una sección de ayuda con consejos y tutoriales relacionados con la natación.

El calendario también está adaptado para móviles, cambiando su distribución para que resulte más cómodo utilizarlo desde una pantalla pequeña.

### Como administrador

- Consultar estadísticas.
- Gestionar las reservas.
- Gestionar usuarios.
- Crear usuarios y asignarles roles.
- Gestionar bonos y sesiones.

---

## Algunas de las partes que más trabajo me han dado

### Reglas de reserva centralizadas

Las reglas de negocio de una reserva no están repartidas entre los distintos controladores.

La clase `Cita` dispone de `validarReserva()`, que comprueba, entre otras cosas:

- Horario permitido.
- Límite de una reserva diaria por usuario.
- Capacidad máxima del carril.
- Disponibilidad.

La misma lógica se utiliza tanto desde el calendario como desde las reservas periódicas y el asistente de IA.

Esto evita que una reserva hecha desde un sitio pueda saltarse las reglas que se aplican en otro.

### Consumo de bonos y concurrencia

Los usuarios pueden tener diferentes bonos de sesiones con distintas fechas de caducidad.

Cuando se consume una sesión, `BonoService` utiliza el bono que caduca antes. Además, la operación se realiza dentro de una transacción utilizando `lockForUpdate()` para evitar que dos peticiones simultáneas puedan consumir la misma sesión.

### Un problema con las fechas y FullCalendar

Una de las cosas que más tiempo me llevó fue un problema con las reservas nocturnas.

FullCalendar estaba trabajando con la zona horaria `Europe/Madrid`, pero determinadas fechas llegaban al backend como UTC. Al volver a interpretarlas como hora local, algunas reservas se desplazaban de día.

La solución fue adaptar el tratamiento de las fechas en `CitaController` y añadir una prueba específica para evitar que el problema volviera a aparecer.

El test de regresión se encuentra en `CitaTimezoneTest`.

### El asistente de IA

El asistente no decide por su cuenta qué carriles están disponibles ni inventa fechas.

Cuando necesita información, utiliza funciones del backend para consultar:

- Disponibilidad.
- Reservas.
- Bonos y sesiones disponibles.
- Fechas.

Una reserva realizada mediante el chat termina pasando por las mismas reglas de validación que una reserva realizada desde el calendario.

También hay control de peticiones, reintentos ante determinados errores del proveedor y reducción del historial enviado al modelo para controlar el consumo de tokens.

### Roles y permisos

La aplicación utiliza **Spatie Permission** junto con Policies de Laravel.

Un usuario puede modificar o cancelar sus propias reservas, mientras que un administrador puede gestionar las reservas de cualquier usuario.

---

## 🛠️ Tecnologías

| Parte | Tecnologías |
|---|---|
| Backend | Laravel 10 · PHP 8.2 |
| Base de datos | MySQL 8 |
| Frontend | Bootstrap · JavaScript · Vite |
| Calendario | FullCalendar Scheduler |
| Permisos | Spatie Permission |
| Alertas | SweetAlert2 |
| IA | Groq API · Function Calling |
| Tests | PHPUnit |



---

## 💻 Ejecutarlo en local

### Requisitos

- PHP 8.2 o superior
- Composer
- Node.js y npm
- MySQL

### Instalación

```bash
git clone https://github.com/JoseBenitezAlba/poolbook.git
cd poolbook

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configura en `.env` la conexión a tu base de datos y crea la base de datos `piscina`.

Después ejecuta:

```bash
php artisan migrate

php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=DemoUsersSeeder
```

Para iniciar el proyecto:

```bash
php artisan serve
npm run dev
```

### Asistente de IA

Para utilizar el asistente hay que configurar la API de Groq en `.env`:

```env
GROQ_API_KEY=tu_clave
GROQ_MODEL=openai/gpt-oss-20b
```

Sin configurar la API, el resto de la aplicación sigue funcionando.

### Tests

```bash
php artisan test
```

---

## 📌 Rutas principales

```text
/dashboard
/calendario
/reservas
/perfil
/proyecto

/admin/users
/admin/users/create
```

---

## 🚀 Despliegue

La aplicación está desplegada en **Render** y utiliza una base de datos MySQL en la nube.

Durante el despliegue hay que tener en cuenta que los archivos utilizados por Vite deben estar incluidos correctamente como entradas en `vite.config.js`. En desarrollo pueden funcionar referencias que posteriormente provoquen errores de manifest al ejecutar `npm run build`.

---

## 🔮 Próximas mejoras

- Notificaciones por correo electrónico.
- Una API REST.
- Mensajes de validación completamente traducidos al español.
- Mejoras en la interfaz de administración de reservas.
- Seguir ampliando las funciones del asistente de IA.

---

## Lo que me ha aportado este proyecto

Con PoolBook he podido llevar a la práctica bastantes de las cosas aprendidas durante DAW, pero también me ha obligado a resolver problemas que no aparecen simplemente siguiendo un tutorial.

Sobre todo, me ha servido para trabajar con:

- Lógica de negocio en Laravel.
- Relaciones y transacciones en MySQL.
- Concurrencia y bloqueo de registros.
- Roles, permisos y Policies.
- JavaScript y calendarios interactivos.
- Diseño responsive.
- Integración de APIs externas y modelos de IA.
- Tests y regresiones.
- Despliegue de una aplicación Laravel en la nube.

---

## 👨‍💻 Autor

**José Manuel Benítez Alba**

[GitHub](https://github.com/JoseBenitezAlba)
