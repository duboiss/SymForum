ARG PHP_VERSION=8.1

FROM php:${PHP_VERSION}-fpm AS symforum_php

SHELL ["/bin/bash", "-c"]

# since we're starting non-interactive shell,
# we wil need to tell bash to load .bashrc manually
ENV BASH_ENV ~/.bashrc

RUN apt update && \
    apt upgrade -y && \
    apt install -y \
      apt-transport-https \
      ca-certificates \
      curl \
      git \
      gnupg2 \
      lsb-release \
      openssl \
      software-properties-common \
      unzip \
      wget \
      xxd \
      zip && \
    # PHP extensions
    apt install -y libfreetype6-dev libicu-dev libjpeg62-turbo-dev libpng-dev libwebp-dev libxml2-dev libzip-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-configure zip --with-zip && \
    docker-php-ext-install -j$(nproc) gd intl pcntl pdo_mysql zip

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1
ENV PATH "/root/.config/composer/vendor/bin:${PATH}"

# Install Volta and use the latest LTS node version by default
# needed by volta() function
ENV VOLTA_HOME /root/.volta
# make sure packages managed by volta will be in PATH
ENV PATH $VOLTA_HOME/bin:$PATH

RUN curl https://get.volta.sh | bash && volta install node yarn
