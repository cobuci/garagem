FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    unzip \
    curl \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libpng-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_mysql \
    bcmath \
    pcntl \
    intl \
    opcache \
    zip

RUN pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

COPY . .

RUN composer dump-autoload --no-interaction --no-dev --optimize

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
