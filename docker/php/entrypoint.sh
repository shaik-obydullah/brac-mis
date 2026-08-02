#!/bin/sh

if [ ! -f /var/www/artisan ]; then
    echo "Creating Laravel project..."
    mkdir -p /var/www-tmp
    composer create-project --prefer-dist laravel/laravel /var/www-tmp
    cp -r /var/www-tmp/. /var/www/
    rm -rf /var/www-tmp
    echo "Laravel project created."
fi

if [ ! -f /var/www/.env ]; then
    cp /var/www/.env.example /var/www/.env
fi

sed -i "s|APP_URL=.*|APP_URL=http://localhost:8087|" /var/www/.env
sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=mysql|" /var/www/.env
sed -i "s|DB_HOST=.*|DB_HOST=mysql|" /var/www/.env
sed -i "s|DB_PORT=.*|DB_PORT=3306|" /var/www/.env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=brac_mis|" /var/www/.env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=brac_mis_user|" /var/www/.env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=secret|" /var/www/.env
sed -i "s|REDIS_HOST=.*|REDIS_HOST=redis|" /var/www/.env
sed -i "s|REDIS_PASSWORD=.*|REDIS_PASSWORD=null|" /var/www/.env
sed -i "s|SESSION_DRIVER=.*|SESSION_DRIVER=redis|" /var/www/.env
sed -i "s|CACHE_STORE=.*|CACHE_STORE=redis|" /var/www/.env

grep -q "^APP_KEY=base64:" /var/www/.env || php artisan key:generate

[ -L /var/www/public/storage ] || php artisan storage:link

php-fpm
