FROM php:8.0-cli

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip pkg-config libonig-dev libzip-dev zlib1g-dev \
  && docker-php-ext-install pdo pdo_mysql mbstring \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

COPY . .

EXPOSE 8080

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public public/index.php"]
