# 🏊‍♂️ PoolBook – Gestión de Piscina

![Laravel](https://img.shields.io/badge/Laravel-10-red?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-blue?logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-38BDF8?logo=tailwind-css&logoColor=white)
![Spatie Permission](https://img.shields.io/badge/Spatie_Permission-6D28D9?logoColor=white)

Aplicación web para **gestionar reservas de carriles en una piscina**, con panel de administración, roles, permisos y calendario interactivo.

---

## 🌐 Demo & Video

🔗 **Demo Online:** https://poolbook-production.up.railway.app  
🎥 **Video en YouTube:** https://youtu.be/JI9NC-3bdPs

> ⚠️ *Railway es un servidor gratuito: Puede tardar unos segundos en cargar o contener algún error puntual. Para una experiencia completa y estable sigue los pasos de instalación de abajo.*

---

## ✨ Características principales

### 👤 Usuarios
- Registro e inicio de sesión
- Reservar carril (máx. **1 reserva por día**)
- Máximo **2 personas por carril y hora**
- Consultar historial de reservas propias
- Sección de ayuda con consejos y tutoriales en vídeo sobre natación

### 🛡️ Administradores
- Panel con estadísticas de usuarios y reservas activas
- Gestión completa de usuarios y reservas
- Crear nuevos administradores
- Layout propio con menú lateral

---

## 🛠️ Tecnologías utilizadas

| Frontend | Backend | Extras |
|---|---|---|
| Tailwind CSS | Laravel 10 | FullCalendar Scheduler 6.1.11 |
| Bootstrap | PHP 8.2 | SweetAlert2 |
| Vite | MySQL 8 | Spatie Laravel Permission |

---

## ⚙️ Instalación

### Requisitos
- [XAMPP](https://www.apachefriends.org/es/download.html)
- [Composer](https://getcomposer.org/download/)
- [Node.js](https://nodejs.org/)

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
```

### Arrancar el proyecto

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Accede en: **http://localhost:8000**

---

## 🔐 Usuario admin por defecto

| Campo | Valor |
|---|---|
| Email | admin@poolbook.com |
| Contraseña | admin1234 |

> Creado automáticamente por el seeder. Cámbiala si lo usas en producción.

---

## 🗺️ Rutas del panel admin

| Sección | Ruta | Descripción |
|---|---|---|
| Dashboard | `/dashboard` | Métricas de usuarios y reservas |
| Usuarios | `/admin/users` | Listado de todos los usuarios |
| Crear Admin | `/admin/users/create` | Formulario para nuevo admin |
| Calendario | `/calendario` | Reservas por carril y hora |
| Reservas | `/reservas` | Todas las reservas |
| Perfil | `/perfil` | Perfil del usuario |

---

## 🔐 Seguridad

- Contraseñas encriptadas con Laravel Hashing
- Rutas protegidas con middleware `auth`
- Sistema de roles y permisos con Spatie Laravel Permission

---

## 🚧 Mejoras futuras

- Notificaciones por email al crear reservas
- API REST para gestión de reservas
- Tests automatizados
- Mejoras de diseño y experiencia de usuario

---

## 📚 Qué he aprendido con este proyecto

- Implementación de lógica de negocio en backend con Laravel
- Gestión de roles y permisos con Spatie
- Validación de datos y control de errores
- Integración de librerías externas como FullCalendar
- Despliegue de aplicaciones Laravel en Railway con MySQL en la nube

---

## 👨‍💻 Autor

**José Manuel Benítez Alba** → [GitHub](https://github.com/JoseBenitezAlba)