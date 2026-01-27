#!/bin/sh
set -e

echo "=== ManageX Container Starting ==="

# Create log directory for supervisor
mkdir -p /var/log/supervisor

# Generate app key if missing
if [ -z "$APP_KEY" ]; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migration warning (continuing...)"

# Clear and cache configuration
echo "Caching configuration..."
php artisan config:clear
php artisan config:cache || echo "Config cache warning"
php artisan route:cache || echo "Route cache warning"
php artisan view:cache || echo "View cache warning"

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Starting nginx + php-fpm via supervisor..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
