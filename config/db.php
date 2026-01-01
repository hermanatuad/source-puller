<?php

// Get environment variables with fallback to defaults
$dbHost = getenv('db_host') ?: '34.143.253.172';
$dbPort = getenv('db_port') ?: '1234';
$dbName = getenv('db_name') ?: 'yii_test';
$dbUsername = getenv('db_username') ?: 'root';
$dbPassword = getenv('db_password') ?: 'example';

return [
    'class' => 'yii\db\Connection',
    'dsn' => "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}",
    'username' => $dbUsername,
    'password' => $dbPassword,
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
