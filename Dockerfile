FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    sqlite3 \
    libsqlite3-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo pdo_pgsql pdo_sqlite mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . /var/www

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV DB_CONNECTION=pgsql
ENV CACHE_STORE=file
ENV SESSION_DRIVER=file

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs
RUN chmod +x /var/www/entrypoint.sh
RUN chmod -R 777 /var/www/storage /var/www/bootstrap/cache

ENV PORT=10000
EXPOSE 10000

ENTRYPOINT ["/var/www/entrypoint.sh"]
