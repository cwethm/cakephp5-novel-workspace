FROM mcr.microsoft.com/devcontainers/php:1-8.3-bookworm

RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y --no-install-recommends \
        git unzip zip \
        libicu-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libonig-dev libxml2-dev libsqlite3-dev sqlite3 default-mysql-client rsync \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql pdo_sqlite gd zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY .devcontainer/php/xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini
ENV COMPOSER_ALLOW_SUPERUSER=1
