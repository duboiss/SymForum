ARG PHP_VERSION=8.0

FROM numberninecms/php:${PHP_VERSION}-fpm-dev AS symforum_php

COPY --from=composer /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
RUN find /tmp -type d -exec chmod -v 1777 {} +
