# syntax=docker/dockerfile:1

#############################################
# Stage 1: Frontend assets (Vite + Tailwind)
#############################################
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources ./resources

RUN npm run build

#############################################
# Stage 2: PHP dependencies (Composer)
#############################################
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-interaction \
        --no-progress \
        --optimize-autoloader \
        --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

#############################################
# Stage 3: Runtime image (PHP-FPM + Nginx)
#############################################
FROM php:8.4-fpm-alpine AS runtime

# Librerías runtime (quedan instaladas en la imagen final) separadas de las
# -dev/headers (solo se usan para compilar y se purgan al final como grupo
# "virtual" .build-deps, sin arrastrarse las runtime como huérfanas).
RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        gettext \
        libpng \
        libzip \
        libwebp libwebpdemux libwebpmux \
        freetype \
        libjpeg-turbo \
        icu-libs icu-data-full \
        oniguruma \
    && apk add --no-cache --virtual .build-deps \
        libpng-dev \
        libzip-dev \
        libwebp-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        icu-dev \
        oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        gd \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        zip \
        intl \
        opcache \
    && apk del --no-cache .build-deps

WORKDIR /var/www/html

# Código de la app + vendor (sin deps de dev) + assets ya compilados por Vite
COPY --from=vendor /app /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build

# Config de PHP / Nginx / Supervisor
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/laravel.ini /usr/local/etc/php/conf.d/laravel.ini
COPY docker/nginx.conf.template /etc/nginx/templates/default.conf.template
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh \
    && mkdir -p /var/log/nginx /run/nginx /etc/nginx/http.d \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    # Nginx corre por defecto como usuario "nginx" en Alpine; lo pasamos a
    # "www-data" (el mismo que usa PHP-FPM) para que pueda leer, a través
    # del symlink public/storage, los archivos que Laravel guarda con ese
    # owner — evita 403 al servir imágenes de productos/combos/logo.
    && sed -i 's/^user nginx;/user www-data;/' /etc/nginx/nginx.conf

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
