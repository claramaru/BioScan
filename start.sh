
#!/bin/sh

php-fpm85 --allow-to-run-as-root -D
sleep 3

# Dar permisos al socket para que nginx pueda accederlo
chmod 777 /run/php-fpm.sock

# Permisos de storage para sesiones y logs
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache

# Ejecutar migraciones
php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan db:seed --class=DataBaseSeeder --force


nginx -g 'daemon off;'