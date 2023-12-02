FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    make \
    libpq-dev \
    php-mysqli
    && docker-php-ext-install \
    pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www
COPY Makefile /var/www/

RUN composer install --no-dev --optimize-autoloader && \
    composer clear-cache \

RUN chown -R www-data:www-data /var/www

CMD ["php-fpm"]

#RUN make install