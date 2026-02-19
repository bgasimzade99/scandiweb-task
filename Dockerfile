# PHP 8.0 - Debian base (Alpine can cause DNS/network quirks on Railway)
FROM php:8.0-cli

WORKDIR /app

# Optional system deps
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip \
  && rm -rf /var/lib/apt/lists/*

# PHP extensions: pdo, pdo_mysql, mbstring (required by GraphQL deps)
RUN docker-php-ext-install pdo pdo_mysql mbstring

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Composer deps (cache-friendly)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY . .

EXPOSE 8080
# DO NOT run import scripts on start. Only start the server.
# Router script required for /graphql routing
CMD ["sh", "-c", "exec php -S 0.0.0.0:${PORT:-8080} -t public public/index.php"]
