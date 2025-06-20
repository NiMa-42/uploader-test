FROM phpdockerio/php:8.4-fpm
WORKDIR "/application"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.4-bcmath \
        php8.4-bz2 \
        php8.4-gd \
        php8.4-http \
        php8.4-imagick \
        php8.4-imap \
        php8.4-intl \
        php8.4-ldap \
        php8.4-mysql \
        php8.4-uploadprogress \
        php8.4-uuid \
        php8.4-xmlrpc \
        php8.4-yaml \
        php8.4-raphf \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
