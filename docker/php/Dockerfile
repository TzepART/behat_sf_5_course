FROM composer:latest AS composer
FROM php:8.1-fpm

RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -yqq apt-utils; \
    apt-get install -y --no-install-recommends \
            g++ \
            curl \
            libssl-dev \
            zip unzip \
            libzip-dev \
            libpq-dev \
            librabbitmq-dev \
            zlib1g-dev libicu-dev \
            libz-dev libmemcached-dev \
            libmcrypt-dev; \
    apt-get clean && \
        rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
        rm /var/log/lastlog /var/log/faillog

RUN set -xe; \
    pecl channel-update pecl.php.net

RUN pecl install -o -f mcrypt apcu && \
    docker-php-ext-install pdo_pgsql && \
    docker-php-ext-enable pdo_pgsql mcrypt

ARG PROJECT_DIR=/var/www
WORKDIR ${PROJECT_DIR}

# COPY project files
COPY ./ ${PROJECT_DIR}

COPY --from=composer /usr/bin/composer /usr/bin/composer
# Update composer to latest version
RUN composer self-update

RUN chmod +x /var/www/bin/console
RUN chmod +x /usr/bin/composer

#RUN cd /var/www && composer install --optimize-autoloader --no-scripts --no-progress --no-plugins

CMD ["php", "-S", "0.0.0.0:8000", "-t", "./public"]
#EXPOSE 9000
