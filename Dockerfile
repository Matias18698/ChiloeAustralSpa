# Usa una imagen base de PHP con FPM.
FROM php:8.2-fpm-alpine

# Instala las dependencias del sistema operativo y extensiones PHP.
RUN apk add --no-cache \
    nginx \
    mysql-client \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    libxml2-dev \
    supervisor \
    # ¡Añade linux-headers aquí!
    linux-headers \
    # Configura y habilita las extensiones PHP
    && docker-php-ext-configure gd --with-jpeg=/usr/include --with-freetype=/usr/include \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip exif pcntl bcmath opcache sockets intl \
    # Limpia las herramientas de compilación para reducir el tamaño de la imagen final (opcional, pero buena práctica)
    && apk del --purge --no-cache linux-headers
    # Mantenemos solo el comando && docker-php-ext-install ... en una línea
    # para optimizar las capas de Docker y la limpieza final con apk del.


# Instala Composer globalmente.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define el directorio de trabajo.
WORKDIR /var/www/html

# Copia composer.json y composer.lock primero.
COPY composer.json composer.lock ./

# Instala las dependencias de Composer.
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copia el resto de tu código de aplicación.
COPY . .

# Establece los permisos correctos.
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Genera los archivos de caché de Laravel.
RUN php artisan config:cache \
    && php artisan event:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expone el puerto por defecto de PHP-FPM.
EXPOSE 9000

# Comando por defecto para iniciar PHP-FPM.
CMD ["php-fpm"]