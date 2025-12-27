FROM dunglas/frankenphp:latest-php8.3 AS frankenphp_prod

WORKDIR /app



# Dépendances système
RUN apt-get update && apt-get install -y git unzip libpq-dev libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP
RUN install-php-extensions pdo_mysql intl zip opcache apcu redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le code
COPY . ./

# Créer un fichier .env vide pour éviter l'erreur
RUN echo "APP_ENV=prod" > .env

# Préparer les dossiers et droits
RUN mkdir -p var/cache var/log public/bundles \
    && chown -R www-data:www-data var public/bundles

# Installer les dépendances sans lancer les scripts
RUN composer install --no-dev --optimize-autoloader --no-scripts \
    && composer dump-autoload --classmap-authoritative


# Copier le Caddyfile
COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

# Configuration FrankenPHP
ENV APP_ENV=prod

# Commande de lancement
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
