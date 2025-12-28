FROM dunglas/frankenphp

ENV SERVER_NAME=:80
ENV NODE_VERSION=20

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Installer git, unzip et Node.js
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Installer l'extension PDO MySQL
RUN install-php-extensions pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /app

WORKDIR /app

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Compiler les assets Webpack
RUN npm install && npm run build

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var

COPY docker/frankenphp/Caddyfile /etc/frankenphp/Caddyfile

EXPOSE 80