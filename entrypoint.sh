#!/bin/sh
set -e

# Ensure permissions on storage and bootstrap cache
chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Generate storage link for public images
php artisan storage:link --force || true

# Run database migrations and seeding if available
if [ "$RUN_MIGRATIONS" != "false" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration skipped or database not connected."
    php artisan db:seed --force || echo "Seeding skipped."
fi

echo "Starting Pocket Showroom API server on port ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
