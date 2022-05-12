FROM php:8.1.5-cli

RUN apt-get update && \
    apt-get install -y \
      git \
      libssh2-1 \
      libzip-dev \
      libicu-dev \
      libpq-dev \
      wget \
      htop \
      procps

# Install PHP core extensions
RUN docker-php-ext-install -j$(nproc) bcmath opcache pcntl pdo_pgsql sockets intl

# Install PHP pecl extensions
RUN pecl install zip && \
    pecl install xdebug

# Enable extensions
RUN docker-php-ext-enable xdebug zip

# Install composer
ADD ./composer-installer.sh /usr/bin/composer-installer.sh
RUN chmod 0775 /usr/bin/composer-installer.sh && \
    /usr/bin/composer-installer.sh && \
    rm -rf /usr/bin/composer-installer.sh && \
    mv composer.phar /usr/bin/composer && \
    chmod 0775 /usr/bin/composer

ARG OWNER_USER
ARG OWNER_USER_ID

# Add owner user if not exists
RUN /bin/bash -c 'id -u ${OWNER_USER} &>/dev/null || useradd -u ${OWNER_USER_ID} ${OWNER_USER}'

# Init dev env
RUN mkdir -p /opt/phpstorm-coverage /home/${OWNER_USER} && \
    chown ${OWNER_USER}:${OWNER_USER} /opt/phpstorm-coverage /home/${OWNER_USER}

# Change user
USER ${OWNER_USER}
