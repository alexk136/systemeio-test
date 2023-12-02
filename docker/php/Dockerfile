FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    procps \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libicu-dev  \
    libonig-dev \
    libxslt1-dev

RUN docker-php-ext-install \
    pdo pdo_mysql zip mysqli


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www
COPY Makefile /var/www/
COPY /docker/php/php.ini /usr/etc/php/conf.d/docker-php-config.ini

RUN composer install --no-dev --optimize-autoloader && \
    composer clear-cache

CMD ["php-fpm"]