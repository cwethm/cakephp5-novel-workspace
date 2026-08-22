FROM mcr.microsoft.com/devcontainers/php:1-8.3-bookworm

RUN rm -f /etc/apt/sources.list.d/yarn.list \
    && apt-get update \
    && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y --no-install-recommends \
        unzip \
        zip \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libsqlite3-dev \
        sqlite3 \
        default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        intl \
        pdo_mysql \
        pdo_sqlite \
        gd \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY .devcontainer/php/xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="/home/vscode/.composer/vendor/bin:${PATH}"
