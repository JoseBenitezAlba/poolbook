# 🏊‍♂️ PoolBook – Gestión de Piscina

![Laravel](https://img.shields.io/badge/Laravel-10-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-646CFF?logo=vite&logoColor=white)
![Spatie Permission](https://img.shields.io/badge/Spatie-Permission-4B5563)

Aplicación web para gestionar reservas de carriles de una piscina: calendario interactivo, panel de administración, roles y permisos, bonos de sesiones y un asistente de chat con IA que reserva por ti.

## 🌐 Demo y vídeo

- 🔗 **Demo online:** https://poolbook.onrender.com/
- 🎥 **Vídeo en YouTube:** https://youtu.be/JI9NC-3bdPs
- 👩‍💻 **Detalle técnico del proyecto (para reclutadores):** `/proyecto` en la propia demo

| Cuenta de prueba | Valor |
|---|---|
| Email | `prueba@prueba.com` |
| Contraseña | `prueba` |
| Bono | 99 sesiones |

> ⚠️ **Hosting gratuito (Render):** la primera carga puede tardar unos segundos mientras el servidor "despierta". El asistente usa el plan gratuito de Groq, así que si le escribes muchos mensajes seguidos puede avisarte de que se ha alcanzado el límite y pedirte que esperes unos segundos.

## ✨ Características principales

### 👤 Usuarios

- Registro e inicio de sesión
- Reservar carril desde un calendario interactivo (máx. 1 reserva por día)
- Máximo 2 personas por carril y hora
- **Editar** (mover) o **cancelar** una reserva propia sin tener que crear otra
- Ver sus reservas y sus bonos (sesiones restantes y caducidad)
- **Asistente de chat con IA**: consulta disponibilidad, reserva un hueco suelto o series recurrentes ("todos los lunes hasta diciembre")
- Sección de ayuda con consejos y tutoriales en vídeo sobre natación
- Diseño adaptado a móvil (el calendario cambia a vista de columnas por carril)

### 🛡️ Administradores

- Panel con estadísticas de usuarios y reservas
- Ver y gestionar **cualquier** reserva, con el nombre real de cada usuario
- Gestión de usuarios y de sus **bonos** (añadir, desactivar, eliminar) con historial
- Crear usuarios eligiendo su rol (Admin o Usuario)
- Layout propio con menú lateral (menú hamburguesa en móvil)

## 🛠️ Tecnologías utilizadas

| Frontend | Backend | Extras |
|---|---|---|
| Bootstrap + sistema de diseño propio (`theme.css`) | Laravel 10 | FullCalendar Scheduler 6.1.11 |
| Tailwind CSS (CDN, solo en el calendario) | PHP 8.2 | SweetAlert2 |
| Vite | MySQL 8 | Spatie Laravel Permission |
| Tipografías Fraunces + IBM Plex Sans | Tests de feature con PHPUnit | Groq API (function calling) |

## 🧠 Puntos técnicos destacados

- **Reglas de negocio en un único sitio.** `Cita::validarReserva()` valida horario, límite diario y capacidad por carril. Lo usan el controlador del calendario, el asistente de IA y las reservas recurrentes, así que las reglas no se repiten ni se contradicen.
- **Bonos con lógica FIFO.** `BonoService::consumirSesion()` gasta primero el bono que antes caduca y bloquea la fila con `lockForUpdate()` dentro de una transacción para evitar descuentos duplicados.
- **Bug de zona horaria, corregido y con test de regresión.** FullCalendar con `timeZone: 'Europe/Madrid'` codifica la hora local en los componentes UTC de la fecha. El backend la reinterpretaba y desplazaba las reservas de tarde/noche. Se corrigió en `CitaController` y hay un test (`CitaTimezoneTest`) que falla si alguien reintroduce el error.
- **Permisos con Policy y roles de Spatie.** Un usuario solo puede editar o cancelar sus reservas; un admin, cualquiera.
- **Asistente con function calling.** El modelo no inventa datos: llama a herramientas que consultan disponibilidad, saldo y calculan fechas de forma exacta en el servidor, y las reservas pasan por las mismas validaciones que el calendario. Incluye reintentos automáticos ante fallos pasajeros del proveedor, espera ante el límite por minuto, historial compactado para ahorrar tokens y límite de peticiones por usuario (`throttle:12,1`).
- **Tests automatizados.** Cubren las reglas de reserva, la lógica FIFO de bonos y la regresión de zona horaria.

## ⚙️ Instalación

### Requisitos

- XAMPP (o cualquier entorno con PHP 8.2 y MySQL)
- Composer
- Node.js

### Pasos

```bash
# Clonar el repositorio
git clone https://github.com/JoseBenitezAlba/poolbook.git
cd poolbook

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate
```

Edita el `.env` con los datos de tu base de datos:

```env
DB_DATABASE=piscina
DB_USERNAME=root
DB_PASSWORD=
```

Crea la base de datos `piscina` en phpMyAdmin y ejecuta:

```bash
php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=DemoUsersSeeder   # opcional: cuenta de prueba con bono de sesiones
```

### Asistente de IA (opcional)

El asistente usa la API de Groq. Sin clave, la app funciona igual y el chat avisa de que no está configurado. Para activarlo, crea una clave gratuita en [console.groq.com](https://console.groq.com) y añade al `.env`:

```env
GROQ_API_KEY=tu_clave_aqui
GROQ_MODEL=openai/gpt-oss-20b
```

> No subas nunca tu clave real al repositorio.

### Arrancar el proyecto

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Accede en: http://localhost:8000

### Tests

```bash
php artisan test
```

## 🔐 Usuarios por defecto (entorno local)

| Rol | Email | Contraseña |
|---|---|---|
| Admin | `admin@poolbook.com` | `admin1234` |
| Usuario de prueba | `prueba@prueba.com` | `prueba` |

> Los crean los seeders. **Cambia la contraseña del admin en cualquier despliegue público.**

## 🗺️ Rutas principales

| Sección | Ruta | Descripción |
|---|---|---|
| Dashboard | `/dashboard` | Métricas de usuarios y reservas |
| Usuarios | `/admin/users` | Listado de todos los usuarios |
| Crear usuario | `/admin/users/create` | Formulario de alta con elección de rol |
| Calendario | `/calendario` | Reservas por carril y hora |
| Reservas | `/reservas` | Reservas del usuario (todas, en el panel de admin) |
| Perfil | `/perfil` | Perfil del usuario |
| Proyecto | `/proyecto` | Detalle técnico para reclutadores |

## 🚀 Despliegue

La demo está desplegada en **Render** con base de datos MySQL en la nube. Algunas notas útiles:

- Las variables de entorno (`APP_KEY`, base de datos, `GROQ_API_KEY`, `GROQ_MODEL`...) se configuran en el panel de Render; el `.env` local no se sube.
- **Vite:** todo archivo que una vista cargue con `@vite([...])` debe estar también en el `input` de `vite.config.js`. En local (`npm run dev`) funciona sin ello, pero el build de producción solo compila lo declarado y, si falta algo, la página da error 500 ("Unable to locate file in Vite manifest").
- Los archivos enlazados con `asset('...')` deben vivir en `public/`, no en `resources/`.

## 🔐 Seguridad

- Contraseñas encriptadas con Laravel Hashing
- Rutas protegidas con middleware `auth`
- Roles y permisos con Spatie Laravel Permission y Policies para las reservas
- Validación definitiva de todas las reglas en el backend (el frontend solo da feedback inmediato)
- Límite de peticiones en el asistente para evitar abusos

## 🚧 Mejoras futuras

- Notificaciones por email al crear reservas
- API REST para gestión de reservas
- Mensajes de validación en español en todos los formularios
- Rediseño de la vista de reservas del panel de administración

## 📚 Qué he aprendido con este proyecto

- Implementación de lógica de negocio en backend con Laravel y control de concurrencia con transacciones y bloqueos
- Gestión de roles, permisos y Policies con Spatie
- Depuración de un bug real de zonas horarias entre frontend y backend, y cómo blindarlo con un test de regresión
- Integración de librerías externas como FullCalendar y de un modelo de lenguaje con function calling
- Diseño responsive: adaptar una vista de calendario por completo a móvil
- Despliegue de aplicaciones Laravel con MySQL en la nube y las diferencias entre desarrollo y producción (Vite, variables de entorno)

## 👨‍💻 Autor

**José Manuel Benítez Alba** → [GitHub](https://github.com/JoseBenitezAlba)
