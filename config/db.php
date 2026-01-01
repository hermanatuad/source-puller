<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . getenv('db_host') . ';port=' . getenv('db_host') . ';dbname=' . getenv('db_name'),
    'username' => getenv('db_username'),
    'password' => getenv('db_password'),
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
