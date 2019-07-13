#!/usr/bin/env bash

git pull origin master
composer install --optimize-autoloader --no-dev
php artisan route:cache
php artisan config:cache
php artisan view:clear
php artisan queue:restart
php artisan migrate
