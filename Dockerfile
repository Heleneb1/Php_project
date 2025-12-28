# Base image
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

# Créer .env si besoin
RUN touch .env

# Installer les dépendances sans lancer les scripts (évite les erreurs DATABASE_URL)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Installer et builder le front avec Yarn
RUN yarn install --frozen-lockfile && yarn build

# Installer Stimulus/Turbo et assets
RUN php bin/console importmap:install --force --no-interaction \
    && php bin/console assets:install --symlink --relative

# Préparer les dossiers et droits
RUN mkdir -p var/cache var/log public/bundles public/vendor public/build \
    && chown -R www-data:www-data var public/bundles public/vendor public/build

# Copier le Caddyfile
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

# Commande de lancement : ici le cache sera clear/warmup à runtime
CMD ["sh", "-c", "\
    php bin/console cache:clear --env=prod --no-warmup && \
    php bin/console cache:warmup --env=prod && \
    php bin/console importmap:install --force --no-interaction && \
    php bin/console assets:install --symlink --relative && \
    frankenphp run --config /etc/caddy/Caddyfile \
    "]
