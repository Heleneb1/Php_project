# Base image
FROM dunglas/frankenphp:latest-php8.3 AS frankenphp_prod

WORKDIR /app

# Définir l'environnement prod dès le départ
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV DATABASE_URL="mysql://user:password@db:3306/my_database"

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

# Installer les dépendances PHP sans dev et sans scripts pour éviter les erreurs
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Installer et builder le front avec Yarn
RUN yarn install && yarn build

# Installer Stimulus/Turbo et assets
RUN mkdir -p public/vendor \
    && php bin/console importmap:install --force --no-interaction || true \
    && php bin/console assets:install --symlink --relative \
    && php bin/console cache:clear --env=prod --no-warmup

# Préparer les dossiers et droits
RUN mkdir -p var/cache var/log public/bundles \
    && chown -R www-data:www-data var public/bundles \
    && chown -R www-data:www-data var

# Copier le Caddyfile
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

# Commande de lancement
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
