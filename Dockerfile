FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    sqlite3 \
    libsqlite3-dev

RUN docker-php-ext-install pdo pdo_sqlite mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . /var/www

RUN composer install --no-dev --optimize-autoloader
RUN touch database/database.sqlite
RUN chmod -R 777 storage bootstrap/cache database/database.sqlite
RUN php artisan migrate --force
RUN php artisan db:seed --force

ENV PORT=10000
EXPOSE 10000

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
