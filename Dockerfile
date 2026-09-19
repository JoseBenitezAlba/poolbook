# Fase 1: compilar assets con Node/Vite
FROM node:20 AS assets
WORKDIR /app
COPY package*.json vite.config.js ./
RUN npm install
COPY resources ./resources
RUN npm run build

# Fase 2: aplicación PHP
FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Copiamos los assets ya compilados desde la fase de Node
COPY --from=assets /app/public/build ./public/build

ENV COMPOSER_NO_SECURITY_BLOCKING=1

RUN composer update --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080
CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
