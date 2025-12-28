FROM dunglas/frankenphp:latest-php8.3 AS frankenphp_prod

WORKDIR /app

# Dépendances système
RUN apt-get update && apt-get install -y git unzip libpq-dev libzip-dev curl \
    && rm -rf /var/lib/apt/lists/*

# Installer Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Installer Yarn
RUN npm install -g yarn

# Extensions PHP
RUN install-php-extensions pdo_mysql intl zip opcache apcu redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le code
COPY . ./

# Installer dépendances PHP (crée vendor/)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Installer dépendances front (vendor/ existe maintenant)
RUN yarn install
RUN yarn build

# Vérifier build
RUN ls -al public/build

# Préparer les dossiers et droits
RUN mkdir -p var/cache var/log public/bundles \
    && chown -R www-data:www-data var public/bundles

# Copier le Caddyfile
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

ENV APP_ENV=prod

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
