FROM php:5.6-apache

RUN apt-get update -y && \
    apt-get install -y \
        curl \
        git \
        zip unzip \
        libmcrypt-dev \
        libzip-dev \
        zlib1g-dev
RUN docker-php-ext-install -j$(nproc) \
    pdo pdo_mysql \
    mbstring \
    mcrypt \
    zip
RUN a2enmod rewrite

ENV APP_ENV=prod

WORKDIR /app
COPY --chown=www-data:www-data . /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-dev --no-interaction --no-progress --prefer-dist

COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY docker/apache-ports.conf /etc/apache2/ports.conf

EXPOSE 80/tcp
