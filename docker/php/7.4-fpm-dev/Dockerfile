ARG PHP_VERSION=7.4

FROM kreait/php:${PHP_VERSION}-fpm-dev AS symforum_php

RUN apk add --no-cache \
        php7-apcu \
        php7-opcache \
        php7-redis \
    ;

COPY --from=composer /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
RUN find /tmp -type d -exec chmod -v 1777 {} +
