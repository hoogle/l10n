FROM php:7.2-fpm

RUN apt-get update && \
    apt-get install -y \
        build-essential \
        apt-utils \
        libssl-dev \
        zlib1g-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libmcrypt-dev \
	libzip-dev \
        curl \
        && pecl install mcrypt-1.0.1 \
        && docker-php-ext-enable mcrypt \
        && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
        && docker-php-ext-install gd \
        && docker-php-ext-install mysqli \
        && docker-php-ext-install pdo_mysql \
	&& docker-php-ext-install zip \
        && mkdir -p /usr/src/php/ext/redis \
        && curl -L https://github.com/phpredis/phpredis/archive/4.3.0.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
        && echo 'redis' >> /usr/src/php-available-exts \
        && docker-php-ext-install redis

RUN rm /etc/apt/preferences.d/no-debian-php
