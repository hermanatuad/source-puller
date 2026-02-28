<?php

$dbConfig = require __DIR__ . '/db.php';

// test database configuration
// Important: Do not run tests on production or development databases

// MySQL Test Database
$dbConfig['db']['dsn'] = 'mysql:host=localhost;dbname=yii_test_db';

// PostgreSQL Test Database (DataWarehouse)
$dbConfig['dbDataWarehouse']['dsn'] = 'pgsql:host=localhost;port=5432;dbname=yii_test_dw';

return $dbConfig;
