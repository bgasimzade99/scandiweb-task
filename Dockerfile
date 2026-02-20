FROM php:8.0-cli

WORKDIR /app

# Build deps for PHP extensions (mbstring needs oniguruma)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip \
    pkg-config \
    libonig-dev \
    libzip-dev \
    zlib1g-dev \
  && docker-php-ext-install pdo pdo_mysql mbstring \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install deps first for cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# Copy app
COPY . .

EXPOSE 8080

# Serve from public, route via index.php
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public public/index.php"]