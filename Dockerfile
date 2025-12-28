# ---- Base image ----
FROM dunglas/frankenphp:latest-php8.3 AS frankenphp_prod

WORKDIR /app

# Copier le env prod
COPY .env.prod .env

# Définir l'environnement prod
ENV APP_ENV=prod
ENV APP_DEBUG=0

# ---- Variables d'environnement prod ----
ENV APP_ENV=prod
ENV APP_DEBUG=0

# ---- Dépendances système ----
RUN apt-get update && apt-get install -y git unzip libzip-dev curl \
    && rm -rf /var/lib/apt/lists/*

# ---- Node.js + Yarn ----
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g yarn

# ---- Extensions PHP ----
RUN install-php-extensions pdo_mysql intl zip opcache apcu redis

# ---- Composer ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---- Copier le code ----
COPY . ./

# ---- Entrypoint custom ----
COPY docker/frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# ---- Installer dépendances PHP et JS (prod) ----
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress
RUN yarn install --frozen-lockfile && yarn build

# ---- Installer Stimulus/Turbo et assets ----
RUN php bin/console importmap:install --force --no-interaction
RUN php bin/console assets:install --symlink --relative

# ---- Cache prod et droits ----
RUN php bin/console cache:clear --env=prod --no-warmup
RUN php bin/console cache:warmup --env=prod
RUN mkdir -p var/cache var/log public/bundles public/vendor public/build \
    && chown -R www-data:www-data var public/bundles public/vendor public/build

# ---- Caddy ----
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

# ---- Commande de lancement ----
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
