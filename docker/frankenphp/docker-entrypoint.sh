#!/bin/sh
set -e

# Installer les dépendances si vendor/ est vide
if [ ! -d "vendor" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction --no-progress
fi

# Builder les assets si public/build est vide
if [ ! -d "public/build" ]; then
    yarn install --frozen-lockfile
    yarn build
    php bin/console importmap:install --force --no-interaction
    php bin/console assets:install --symlink --relative
fi

# Préparer le cache prod
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod

# S'assurer que les droits sont corrects
mkdir -p var/cache var/log public/bundles public/vendor public/build
chown -R www-data:www-data var public/bundles public/vendor public/build

exec docker-php-entrypoint "$@"
