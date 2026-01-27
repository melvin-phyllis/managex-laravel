#!/bin/sh

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start supervisor (manages nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisord.conf
