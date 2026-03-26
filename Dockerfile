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

# Copy Oracle Instant Client
COPY instantclient-basic-linux.x64-21.10.0.0.0.zip /tmp/
COPY instantclient-sdk-linux.x64-21.10.0.0.0.zip /tmp/

RUN unzip /tmp/instantclient-basic-linux.x64-*.zip -d /usr/local/ \
    && unzip /tmp/instantclient-sdk-linux.x64-*.zip -d /usr/local/ \
    && ln -s /usr/local/instantclient_* /usr/local/instantclient \
    && rm -rf /tmp/*.zip

ENV LD_LIBRARY_PATH=/usr/local/instantclient
ENV ORACLE_HOME=/usr/local/instantclient

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Allow composer to run as root inside container and set home
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/tmp

# Ensure composer is executable
RUN chmod +x /usr/bin/composer || true

# Set working directory
WORKDIR /var/www/html

# Copy composer files first to leverage Docker layer caching
COPY composer.json composer.lock /var/www/html/

# Install dependencies (include dev packages for dev environment)
RUN composer install --optimize-autoloader --no-interaction --prefer-dist

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/runtime/cache /var/www/html/runtime/tmp \
    && chown -R www-data:www-data /var/www/html/runtime /var/www/html/runtime/cache /var/www/html/runtime/tmp /var/www/html/web/assets \
    && chmod -R 775 /var/www/html/runtime /var/www/html/runtime/cache /var/www/html/runtime/tmp \
    && chmod -R 755 /var/www/html/web/assets

# Expose port 9000 for PHP-FPM
# Copy entrypoint and make it executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
