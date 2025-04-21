FROM serversideup/php:8.2-fpm-nginx

WORKDIR /var/www/html

USER root

# Install PostgreSQL dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

USER www-data

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY --chown=www-data:www-data composer.json composer.lock ./

RUN composer install --no-interaction --optimize-autoloader --no-scripts --ignore-platform-reqs

COPY --chown=www-data:www-data ../../ .