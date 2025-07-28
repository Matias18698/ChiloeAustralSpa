#!/bin/sh

# Reemplaza LISTEN_PORT en nginx.conf por el valor de $PORT (o 8080 por defecto)
PORT=${PORT:-8080}
sed -i "s/LISTEN_PORT/$PORT/g" /etc/nginx/nginx.conf

# Ajustar permisos por seguridad
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Arrancar PHP-FPM en background
php-fpm -D

# Esperar que PHP-FPM esté listo antes de iniciar Nginx
while ! nc -w 1 -z 127.0.0.1 9000; do
  echo "Esperando PHP-FPM..."
  sleep 0.5
done

# Iniciar Nginx en primer plano
nginx -g "daemon off;"
