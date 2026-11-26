# Stage 1: Build frontend assets with Vite
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

# Salin file project agar konfigurasi Vite ikut tersedia
COPY . .

RUN npm run build


# Stage 2: Laravel PHP + Apache
FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    && docker-php-ext-install \
       pdo_mysql \
       pdo_pgsql \
       mbstring \
       exif \
       pcntl \
       bcmath \
       gd \
       zip \
       intl \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Copy compiled Vite assets
COPY --from=frontend /app/public/build ./public/build

# Install production dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

# Set Laravel public directory as Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" \
    /etc/apache2/sites-available/*.conf \
    && sed -ri "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" \
    /etc/apache2/apache2.conf \
    && sed -ri "s/AllowOverride None/AllowOverride All/g" \
    /etc/apache2/apache2.conf

# Set permissions
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configure Render port
RUN printf '%s\n' \
    '#!/bin/sh' \
    'set -e' \
    'PORT="${PORT:-10000}"' \
    'sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf' \
    'sed -i "s/\\*:80>/*:${PORT}>/" /etc/apache2/sites-available/000-default.conf' \
    'php artisan config:clear' \
    'php artisan storage:link --quiet || true' \
    'exec apache2-foreground' \
    > /usr/local/bin/render-start \
    && chmod +x /usr/local/bin/render-start

EXPOSE 10000

CMD ["/usr/local/bin/render-start"]
