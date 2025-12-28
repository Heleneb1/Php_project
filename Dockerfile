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

# Exposer le port 80
EXPOSE 80

# Optionnel : vérifier que le serveur démarre
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]