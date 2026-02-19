# PHP 8.0 - compatible with deps (phpdotenv 5.x); avoids PHP 8.3 ParseError
FROM php:8.0-cli-alpine

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Extensions: pdo, pdo_mysql, mbstring (json built-in PHP 8)
# oniguruma required for mbstring
RUN apk add --no-cache oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY . .

EXPOSE 8000
# Seed DB on start, then serve. Railway backend Variables: DB_HOST, DB_NAME, DB_USER, DB_PASS
CMD ["/bin/sh", "-c", "php scripts/import-schema.php 2>/dev/null || true; exec php -S 0.0.0.0:${PORT:-8000} -t public public/index.php"]
