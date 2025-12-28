# Base image
FROM dunglas/frankenphp:latest-php8.3 AS frankenphp_prod

WORKDIR /app

# Définir l'environnement prod dès le départ
ENV APP_ENV=prod
ENV APP_DEBUG=0

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

# Créer un .env vide si absent pour éviter Symfony Dotenv error
RUN [ -f .env ] || touch .env

# Installer les dépendances PHP (sans dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Installer et builder le front avec Yarn
RUN yarn install --frozen-lockfile && yarn build

# Installer Stimulus/Turbo et assets
RUN php bin/console importmap:install --force --no-interaction \
    && php bin/console assets:install --symlink --relative

# Préparer cache prod et droits
RUN php bin/console cache:clear --env=prod --no-warmup \
    && php bin/console cache:warmup --env=prod \
    && mkdir -p var/cache var/log public/bundles public/vendor public/build \
    && chown -R www-data:www-data var public/bundles public/vendor public/build

# Copier le Caddyfile
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

# Commande de lancement
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
