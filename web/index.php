<?php

require __DIR__ . '/../config/loadenv.php';
loadEnv(__DIR__ . '/../.env');

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', getenv('yii_debug'));
defined('YII_ENV') or define('YII_ENV', getenv('yii_env'));

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
