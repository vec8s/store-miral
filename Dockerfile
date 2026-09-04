# =========================================================
# Stage 1 — Frontend Build
# =========================================================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
COPY postcss.config.js ./
COPY tailwind.config.js ./

RUN npm run build


# =========================================================
# Stage 2 — Laravel
# =========================================================
FROM php:8.4-cli

WORKDIR /var/www

# ---------------------------------------------------------
# System dependencies
# ---------------------------------------------------------
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# ---------------------------------------------------------
# Composer
# ---------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# ---------------------------------------------------------
# Composer dependencies
# ---------------------------------------------------------
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts


# ---------------------------------------------------------
# Application
# ---------------------------------------------------------
COPY . .


# ---------------------------------------------------------
# Composer autoload
# ---------------------------------------------------------
RUN composer dump-autoload \
    --optimize \
    --no-scripts


# ---------------------------------------------------------
# Vite production assets
# ---------------------------------------------------------
COPY --from=frontend /app/public/build ./public/build


# ---------------------------------------------------------
# Laravel permissions
# ---------------------------------------------------------
RUN mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        bootstrap/cache \
    && chown -R www-data:www-data \
        storage \
        bootstrap/cache \
        public


# ---------------------------------------------------------
# Railway
# ---------------------------------------------------------
EXPOSE 8080

CMD ["sh", "-c", "php artisan migrate --force; exec php -S 0.0.0.0:${PORT:-8080} -t public"]