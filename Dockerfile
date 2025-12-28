FROM dunglas/frankenphp:latest-php8.3
WORKDIR /app

COPY . ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && yarn install --frozen-lockfile && yarn build \
    && php bin/console importmap:install --force --no-interaction \
    && php bin/console assets:install --symlink --relative

# Préparer dossiers et droits
RUN mkdir -p var/cache var/log public/bundles public/vendor public/build \
    && chown -R www-data:www-data var public/bundles public/vendor public/build

COPY docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
