FROM dunglas/frankenphp

# Configuration serveur (HTTP uniquement, CapRover gère le HTTPS)
ENV SERVER_NAME=:80

# PHP en production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier TOUT le projet à la racine /app
COPY . /app

WORKDIR /app

# Installer les dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Créer les répertoires nécessaires et définir les permissions
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var

