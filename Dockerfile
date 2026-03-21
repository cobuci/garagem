FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader


FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    git \
    curl \
    libzip-dev \
    oniguruma-dev \
    icu-dev

RUN docker-php-ext-install \
    pdo_mysql \
    bcmath \
    pcntl \
    intl \
    opcache

WORKDIR /var/www

COPY --from=vendor /app/vendor /var/www/vendor

COPY . .

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/http.d/default.conf

COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
