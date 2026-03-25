# PoolBook

Aplicación web para gestionar reservas de carriles en una piscina. Hecha con Laravel.

## Demo
Puedes probar la aplicación aquí → https://poolbook-production.up.railway.app

Al estar en un servidor gratuito puede tardar unos segundos en cargar o contener algún error puntual. Para una experiencia completa y estable sigue los pasos de instalación de abajo.
## ¿Qué hace?

- Los usuarios pueden registrarse y reservar un carril en el calendario
- Cada usuario puede hacer 1 reserva por día
- En cada carril caben máximo 2 personas por hora
- El admin puede ver y eliminar cualquier reserva
- Hay una sección de ayuda con info sobre natación

## Tecnologías usadas

- Laravel 10
- MySQL
- Vite
- Tailwind CSS
- FullCalendar Scheduler
- SweetAlert2
- Spatie Laravel Permission

## Instalación

Lo primero, necesitas tener instalado en tu PC:
- XAMPP → https://www.apachefriends.org/es/download.html
- Composer → https://getcomposer.org/download/
- Node.js → https://nodejs.org/

Una vez tienes todo eso, clona el proyecto y dentro de la carpeta ejecuta:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Abre el `.env` y cambia la base de datos:
```env
DB_USERNAME=root
DB_PASSWORD=
```

Arranca XAMPP, crea una base de datos llamada `piscina` en phpMyAdmin y luego:

```bash
php artisan migrate
php artisan db:seed --class=RoleSeeder
```

## Arrancar el proyecto

Necesitas dos terminales abiertas:

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Y ya puedes entrar en http://localhost:8000

## Usuario admin por defecto

Al hacer el seeder se crea un usuario administrador:
- **Email:** admin@poolbook.com
- **Contraseña:** admin1234

(Cámbiala después si lo subes a producción)