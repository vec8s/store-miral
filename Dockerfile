FROM php:8.4-fpm

# تثبيت متطلبات النظام ولارافيل
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# تنظيف الكاش
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# تثبيت امتدادات PHP لقاعدة البيانات
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# نسخ Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# [1] نسخ ملفات المشروع كاملة إلى داخل الحاوية
COPY . /var/www

# [2] تثبيت الاعتماديات وإعطاء الصلاحيات
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# [3] أمر تشغيل خادم لارافيل المباشر بدلاً من php-fpm
CMD php artisan serve --host 0.0.0.0 --port ${PORT:-8000}