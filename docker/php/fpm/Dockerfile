FROM php:8.1.5-fpm

RUN apt-get update && \
    apt-get install -y libzip-dev libicu-dev libpq-dev wget htop procps

# Install PHP core extensions
RUN docker-php-ext-install -j$(nproc) bcmath opcache pcntl pdo_pgsql sockets intl

# Install PHP pecl extensions
RUN pecl install zip && \
    pecl install xdebug

# Enable extensions
RUN docker-php-ext-enable zip xdebug

ARG OWNER_USER
ARG OWNER_USER_ID

# Add owner user if not exists
RUN /bin/bash -c 'id -u ${OWNER_USER} &>/dev/null || useradd -u ${OWNER_USER_ID} ${OWNER_USER}'

# Change user
USER ${OWNER_USER}
