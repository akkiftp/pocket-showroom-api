#!/bin/sh

# Ensure permissions on storage, cache, and database
chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database 2>/dev/null || true

# Generate storage link for public images
php artisan storage:link --force || true

# Detect DB configuration: fallback to SQLite if remote Postgres is not configured
if [ -n "$DATABASE_URL" ]; then
    echo "Using DATABASE_URL for Postgres connection."
    export DB_CONNECTION=pgsql
elif [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "Using DB_HOST ($DB_HOST) for Postgres connection."
    export DB_CONNECTION=pgsql
else
    echo "No remote Postgres configured. Using local SQLite database."
    export DB_CONNECTION=sqlite
    export DB_DATABASE=/var/www/database/database.sqlite
    touch /var/www/database/database.sqlite
    chmod 777 /var/www/database/database.sqlite
fi

# Run database migrations and seeding
if [ "$RUN_MIGRATIONS" != "false" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration skipped or failed."
    echo "Seeding database..."
    php artisan db:seed --force || echo "Seeding skipped."
fi

# Clear stale config/route caches
php artisan config:clear || true
php artisan route:clear || true

echo "Starting Pocket Showroom API server on port ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
