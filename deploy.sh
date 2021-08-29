#!/usr/bin/env bash

git pull origin master
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
php artisan migrate --no-interaction --force
php artisan route:cache
php artisan config:cache
php artisan view:clear
php artisan queue:restart
