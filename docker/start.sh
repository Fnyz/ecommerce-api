#!/bin/sh
set -e

echo "Caching config..."
php artisan config:cache
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force

echo "Starting supervisord..."
exec supervisord -c /etc/supervisord.conf
