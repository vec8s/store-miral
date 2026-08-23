FROM php:8.4-fpm

# تثبيت متطلبات النظام و Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# تنظيف الكاش
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# تثبيت امتدادات PHP لقاعدة البيانات
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# نسخ Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# نسخ ملفات المشروع كاملة
COPY . /var/www

# تثبيت الاعتماديات الخاصة بـ Composer و NPM
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install
RUN npm run build

# إعطاء الصلاحيات اللازمة
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public

# أمر التشغيل المباشر
CMD php artisan serve --host 0.0.0.0 --port ${PORT:-8080}