# ---- Etapa 1: Build PHP y Node ----
FROM php:8.3-fpm-alpine AS build

# Instalar dependencias del sistema y extensiones PHP necesarias
RUN apk add --no-cache \
    bash git curl zip unzip \
    libzip-dev oniguruma-dev icu-dev \
    libjpeg-turbo-dev libpng-dev freetype-dev \
    autoconf g++ make \
    nodejs npm

# Instalar extensiones PHP
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo pdo_mysql mbstring zip intl gd

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copiar archivos de la app
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias frontend y compilar assets
RUN npm install && npm run build

# Limpiar cachés de Laravel
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

---

# ---- Etapa 2: Imagen final con PHP + Nginx ----
FROM php:8.3-fpm-alpine

# Instalar Nginx y otras herramientas necesarias
RUN apk add --no-cache nginx bash curl


# Crear directorio de trabajo
WORKDIR /var/www

# Copiar app desde la etapa de build
COPY --from=build /var/www /var/www

# Copiar config de nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copiar script de arranque
COPY docker/startup.sh /startup.sh
RUN chmod +x /startup.sh

# Establecer permisos correctos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exponer el puerto donde correrá Nginx
EXPOSE 8080

# Comando de inicio
CMD ["/startup.sh"]
