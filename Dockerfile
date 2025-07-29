# Usa una imagen base de PHP con FPM, optando por la versión Alpine para ligereza.
FROM php:8.2-fpm-alpine

# Instala las dependencias del sistema operativo y las extensiones PHP necesarias.
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
    linux-headers \
    && docker-php-ext-configure gd --with-jpeg=/usr/include --with-freetype=/usr/include \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip exif pcntl bcmath opcache sockets intl \
    && apk del --purge --no-cache linux-headers

# Instala Composer globalmente desde una imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia todo el proyecto al contenedor (incluye artisan)
COPY . .

# Instala las dependencias de Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Establece los permisos correctos para Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Genera cachés de Laravel
RUN php artisan config:cache \
    && php artisan event:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expone el puerto por defecto de PHP-FPM
EXPOSE 9000

# Define el comando de inicio
CMD ["php-fpm"]