FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    default-mysql-client \
    libpq-dev \
    libaio1 \
    bash \
    ca-certificates \
    openssh-client \
    && rm -rf /var/lib/apt/lists/*

# --- Oracle Instant Client dulu (WAJIB sebelum compile) ---
COPY instantclient-basic-linux.x64-21.10.0.0.0.zip /tmp/
COPY instantclient-sdk-linux.x64-21.10.0.0.0.zip /tmp/

RUN unzip /tmp/instantclient-basic-linux.x64-*.zip -d /usr/local/ \
    && unzip /tmp/instantclient-sdk-linux.x64-*.zip -d /usr/local/ \
    && ln -s /usr/local/instantclient_* /usr/local/instantclient \
    && rm -rf /tmp/*.zip

ENV LD_LIBRARY_PATH=/usr/local/instantclient
ENV ORACLE_HOME=/usr/local/instantclient

# --- Baru compile extension ---
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-configure pdo_oci \
        --with-pdo-oci=instantclient,/usr/local/instantclient \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mysqli \
        pdo_pgsql \
        pdo_oci \
        zip \
        gd \
        intl \
        opcache

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/tmp

WORKDIR /var/www/html

COPY composer.json composer.lock /var/www/html/
RUN composer install --optimize-autoloader --no-interaction --prefer-dist

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]