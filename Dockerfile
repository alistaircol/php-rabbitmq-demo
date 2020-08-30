FROM php:7.4-apache
RUN apt-get update \
    && apt-get install -y \
        zip \
        git \
    && docker-php-ext-install sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
