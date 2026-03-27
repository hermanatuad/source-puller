#!/bin/sh
set -e

# If config/db.php doesn't exist, create it from environment variables
CONFIG_FILE=/var/www/html/config/db.php
if [ ! -f "$CONFIG_FILE" ]; then
  echo "Creating $CONFIG_FILE from environment variables"
  cat > "$CONFIG_FILE" <<'PHP'
<?php

// Multi-database configuration generated from environment variables

$mysqlHost = getenv('mysql_host') ?: getenv('db_host') ?: '127.0.0.1';
$mysqlPort = getenv('mysql_port') ?: getenv('db_port') ?: '3456';
$mysqlDbName = getenv('mysql_db_name') ?: getenv('db_name') ?: 'yii_test';
$mysqlUsername = getenv('mysql_username') ?: getenv('db_username') ?: 'root';
$mysqlPassword = getenv('mysql_password') ?: getenv('db_password') ?: '';

$postgresHost = getenv('postgres_host') ?: '127.0.0.1';
$postgresPort = getenv('postgres_port') ?: '5432';
$postgresDbName = getenv('postgres_db_name') ?: 'datawarehouse';
$postgresUsername = getenv('postgres_username') ?: 'postgres';
$postgresPassword = getenv('postgres_password') ?: '';

return [
    'db' => [
        'class' => 'yii\\db\\Connection',
        'dsn' => "mysql:host={$mysqlHost};port={$mysqlPort};dbname={$mysqlDbName}",
        'username' => $mysqlUsername,
        'password' => $mysqlPassword,
        'charset' => 'utf8mb4',
    ],
    'dbDataWarehouse' => [
        'class' => 'yii\\db\\Connection',
        'dsn' => "pgsql:host={$postgresHost};port={$postgresPort};dbname={$postgresDbName}",
        'username' => $postgresUsername,
        'password' => $postgresPassword,
        'charset' => 'UTF8',
    ],
];
PHP

  chown www-data:www-data "$CONFIG_FILE" || true
  chmod 640 "$CONFIG_FILE" || true
fi

# Ensure runtime and web/assets directories exist and are writable
mkdir -p /var/www/html/runtime/cache /var/www/html/web/assets
chown -R www-data:www-data /var/www/html/runtime /var/www/html/web/assets || true
chmod -R 0777 /var/www/html/runtime /var/www/html/web/assets || true

# Ensure vendor dependencies are present (handles stale docker volume vendor)
if [ ! -f /var/www/html/vendor/autoload.php ] || [ ! -f /var/www/html/vendor/yiisoft/yii2-httpclient/Client.php ]; then
  echo "Installing composer dependencies in container startup..."
  cd /var/www/html
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Execute the main process (php-fpm)
exec "$@"