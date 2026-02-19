# PHP 8.2 - Nixpacks PHP 7.4 hatasını bypass
FROM php:8.2-cli-alpine

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Extensions: pdo, pdo_mysql, mbstring (json built-in PHP 8)
# oniguruma required for mbstring
RUN apk add --no-cache oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer update --no-dev --optimize-autoloader --no-interaction

COPY . .

EXPOSE 8000
# Railway sets PORT env
CMD php -S 0.0.0.0:${PORT:-8000} -t public
