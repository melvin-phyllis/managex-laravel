#!/bin/sh
set -e

echo "=== ManageX Container Starting ==="

# Generate app key if missing
if [ -z "$APP_KEY" ]; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migration warning (continuing...)"

# Cache configuration
echo "Caching configuration..."
php artisan config:clear
php artisan config:cache || echo "Config cache warning"
php artisan route:cache || echo "Route cache warning"
php artisan view:cache || echo "View cache warning"

# Start the server
echo "Starting server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
