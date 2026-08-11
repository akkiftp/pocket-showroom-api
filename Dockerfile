FROM php:8.4-cli

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

ENV APP_ENV=production
ENV APP_KEY=base64:OMXbpxjceLqOkSB9haO2huC+iBR4V6/wi0EvwlE85UY=
ENV APP_DEBUG=true
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/var/www/database/database.sqlite

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs
RUN touch /var/www/database/database.sqlite
RUN chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database

RUN php artisan migrate --force
RUN php artisan db:seed --force

ENV PORT=10000
EXPOSE 10000

CMD ["sh", "-c", "chmod -R 777 /var/www/database /var/www/storage && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
