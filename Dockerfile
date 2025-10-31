FROM php:8.4-fpm-alpine

RUN apk update && apk add --no-cache \
    oniguruma-dev \
    git \
    linux-headers \
    $PHPIZE_DEPS \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        mbstring \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN mkdir /.composer \
    && chown -R www-data:www-data /.composer

COPY php.ini /usr/local/etc/php/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

ENV PATH /var/www/html/bin:/var/www/html/vendor/bin:$PATH
