FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --optimize-autoloader --no-interaction

# Setup .env jika belum ada
RUN cp -n .env.example .env || true
RUN php artisan key:generate --force --no-interaction || true

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database

EXPOSE 8000

CMD php artisan config:clear && \
    php artisan migrate --seed --force && \
    php artisan l5-swagger:generate && \
    php artisan serve --host=0.0.0.0 --port=8000

