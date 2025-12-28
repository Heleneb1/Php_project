FROM dunglas/frankenphp:latest-php8.3 AS frankenphp_prod

WORKDIR /app

# Dépendances système
RUN apt-get update && apt-get install -y git unzip libzip-dev curl \
    && rm -rf /var/lib/apt/lists/*

# Node.js + Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g yarn

# Extensions PHP nécessaires
RUN install-php-extensions pdo_mysql intl zip opcache apcu redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le code
COPY . ./

RUN touch .env

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Installer importmap pour Stimulus/Turbo si utilisé
RUN php bin/console importmap:install --force \
    && php bin/console assets:install --symlink --relative \
    && php bin/console cache:clear --env=prod


# Installer et builder le front
RUN yarn install && yarn build

# Préparer les dossiers et droits
RUN mkdir -p var/cache var/log public/bundles \
    && chown -R www-data:www-data var public/bundles \
    && chown -R www-data:www-data var


# Copier le Caddyfile
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

# Variables d'environnement
ENV APP_ENV=prod

# Commande de lancement
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
