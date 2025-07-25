# ---- Etapa 1: Dependencias PHP ----
FROM php:8.3-fpm-alpine AS php-build

# Instalar dependencias del sistema y extensiones PHP necesarias para Laravel
RUN apk add --no-cache \
    bash git curl \
    libzip-dev zip unzip oniguruma-dev \
    autoconf g++ make \
    libjpeg-turbo-dev libpng-dev freetype-dev icu-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip intl \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias Laravel optimizadas (sin dev)
RUN composer install --no-dev --optimize-autoloader

# Limpiar caches
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

# ---- Etapa 2: Build frontend con Node ----
FROM node:20-alpine AS node-build
WORKDIR /var/www
COPY . .
RUN npm install && npm run build

# ---- Etapa 3: Imagen final con Nginx + PHP ----
FROM php:8.3-fpm-alpine

# Instalar Nginx y utilidades
RUN apk add --no-cache nginx bash curl netcat-openbsd

# Copiar configuración Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www

# Copiar app Laravel y assets compilados
COPY --from=php-build /var/www /var/www
COPY --from=node-build /var/www/public /var/www/public

# Copiar script de arranque
COPY docker/startup.sh /startup.sh
RUN chmod +x /startup.sh

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8080
CMD ["/startup.sh"]
