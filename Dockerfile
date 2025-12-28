FROM dunglas/frankenphp

ENV SERVER_NAME=:80

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /app

WORKDIR /app

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var

# Copier votre Caddyfile personnalisé au bon endroit
COPY docker/frankenphp/Caddyfile /etc/frankenphp/Caddyfile

EXPOSE 80