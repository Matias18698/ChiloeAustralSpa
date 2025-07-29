FROM php:8.2-fpm-alpine

# Instala las dependencias del sistema operativo y extensiones PHP necesarias para Laravel.
# Usamos 'apk' para Alpine Linux, es similar a 'apt' en Debian/Ubuntu.
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
    # Configura y habilita las extensiones PHP
    && docker-php-ext-configure gd --with-jpeg=/usr/include --with-freetype=/usr/include \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip exif pcntl bcmath opcache sockets intl

# Instala Composer globalmente.
# Utilizamos una imagen multi-stage para copiar Composer de forma eficiente.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define el directorio de trabajo dentro del contenedor.
# Aquí es donde se copiará tu aplicación.
WORKDIR /var/www/html

# Copia solo los archivos de Composer primero.
# Esto permite a Docker cachear la capa de instalación de dependencias
# y solo reconstruirla si composer.json o composer.lock cambian.
COPY composer.json composer.lock ./

# Instala las dependencias de Composer.
# --no-dev: Excluye dependencias de desarrollo.
# --optimize-autoloader: Optimiza el autoloader para un mejor rendimiento.
# --no-interaction: Previene preguntas interactivas.
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copia el resto de tu código de aplicación.
# Debido al volumen que montas en docker-compose, esto es más para tener una imagen completa,
# pero en desarrollo los cambios se reflejarán directamente desde tu host.
COPY . .

# Establece los permisos correctos para la aplicación.
# Esto es crucial para que Laravel pueda escribir en los directorios de caché, logs, etc.
# Ajusta el usuario y grupo 'www-data' si tu servidor web usa otro.
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Genera los archivos de caché de configuración, rutas y vistas de Laravel.
# Esto optimiza el rendimiento de la aplicación.
# NOTA: Si usas variables de entorno que cambian fuera del Dockerfile (ej. en .env),
# y las cachés del Dockerfile no se invalidan, podrías necesitar un `php artisan config:clear`
# en un script de entrada o manualmente después de iniciar el contenedor.
# Para Play with Docker, con el volumen montado, los archivos locales tienen prioridad.
RUN php artisan config:cache \
    && php artisan event:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expone el puerto por defecto de PHP-FPM.
EXPOSE 9000

# Comando por defecto para iniciar PHP-FPM cuando el contenedor arranca.
CMD ["php-fpm"]