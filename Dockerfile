FROM dunglas/frankenphp

# Be sure to replace "your-domain-name.example.com" by your domain name
ENV SERVER_NAME=myseries.lesmysteresdelegypteantique.fr:443
# If you want to disable HTTPS, use this value instead:
ENV SERVER_NAME=:80

# If your project is not using the "public" directory as the web root, you can set it here:
# ENV SERVER_ROOT=web/

# Enable PHP production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy the PHP files of your project in the public directory
COPY . /app/public
# If you use Symfony or Laravel, you need to copy the whole project instead:
COPY . /app