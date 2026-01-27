#!/bin/bash
set -e

echo "=== ManageX Container Starting ==="

# Run migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migration warning (continuing...)"

# Cache configuration
echo "Caching configuration..."
php artisan config:cache || echo "Config cache warning"
php artisan route:cache || echo "Route cache warning"
php artisan view:cache || echo "View cache warning"

# Start the server
echo "Starting server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
