FROM php:8.3-fpm-alpine

ARG ENABLE_ORACLE=false
ARG ORACLE_BASIC_ZIP_URL=""
ARG ORACLE_SDK_ZIP_URL=""

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    mysql-client \
    postgresql-dev \
    libaio \
    libnsl \
    bash \
    ca-certificates \
    openssh-client

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mysqli \
    pdo_pgsql \
    zip \
    gd \
    intl \
    opcache

# Optional Oracle support (pdo_oci + oci8) using Oracle Instant Client ZIP URLs
RUN if [ "$ENABLE_ORACLE" = "true" ]; then \
            if [ -z "$ORACLE_BASIC_ZIP_URL" ] || [ -z "$ORACLE_SDK_ZIP_URL" ]; then \
                echo "ENABLE_ORACLE=true requires ORACLE_BASIC_ZIP_URL and ORACLE_SDK_ZIP_URL"; \
                exit 1; \
            fi; \
            mkdir -p /opt/oracle && cd /opt/oracle; \
            curl -fsSL "$ORACLE_BASIC_ZIP_URL" -o instantclient-basic.zip; \
            curl -fsSL "$ORACLE_SDK_ZIP_URL" -o instantclient-sdk.zip; \
            unzip -q instantclient-basic.zip; \
            unzip -q instantclient-sdk.zip; \
            IC_DIR="$(find /opt/oracle -maxdepth 1 -type d -name 'instantclient_*' | head -n 1)"; \
            if [ -z "$IC_DIR" ]; then echo "Instant Client directory not found"; exit 1; fi; \
            ln -sf "$IC_DIR" /opt/oracle/instantclient; \
            export LD_LIBRARY_PATH=/opt/oracle/instantclient:$LD_LIBRARY_PATH; \
            echo "instantclient,/opt/oracle/instantclient" | pecl install oci8; \
            docker-php-ext-enable oci8; \
            docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient,21.1; \
            docker-php-ext-install -j$(nproc) pdo_oci; \
            rm -f /opt/oracle/instantclient-basic.zip /opt/oracle/instantclient-sdk.zip; \
        fi

ENV LD_LIBRARY_PATH=/opt/oracle/instantclient:$LD_LIBRARY_PATH

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
