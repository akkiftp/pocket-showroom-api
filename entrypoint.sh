#!/bin/sh

# Ensure storage, cache, and database directories exist with permissions
mkdir -p /var/www/database /var/www/storage/framework/cache/data /var/www/storage/framework/sessions /var/www/storage/framework/views
touch /var/www/database/database.sqlite
chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database 2>/dev/null || true

# Dynamically generate .env file based on environment
if [ -n "$DATABASE_URL" ]; then
    echo "Using DATABASE_URL for PostgreSQL connection..."
    cat <<EOT > /var/www/.env
APP_NAME="Pocket Showroom"
APP_ENV=production
APP_KEY=base64:OMXbpxjceLqOkSB9haO2huC+iBR4V6/wi0EvwlE85UY=
APP_DEBUG=false
APP_URL=${APP_URL:-https://pocket-showroom-api.onrender.com}
DATABASE_URL=${DATABASE_URL}
DB_CONNECTION=pgsql
CACHE_STORE=array
SESSION_DRIVER=file
EOT
else
    echo "No remote DATABASE_URL configured. Using local SQLite database..."
    cat <<EOT > /var/www/.env
APP_NAME="Pocket Showroom"
APP_ENV=production
APP_KEY=base64:OMXbpxjceLqOkSB9haO2huC+iBR4V6/wi0EvwlE85UY=
APP_DEBUG=false
APP_URL=${APP_URL:-https://pocket-showroom-api.onrender.com}
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/database/database.sqlite
CACHE_STORE=array
SESSION_DRIVER=file
EOT
fi

php artisan config:clear || true
php artisan route:clear || true
php artisan storage:link --force || true

# Run database migrations
if [ "$RUN_MIGRATIONS" != "false" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration skipped or failed."
fi

echo "Starting Pocket Showroom API server on port ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
