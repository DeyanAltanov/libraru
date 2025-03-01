<?php

// comment out the following two lines when deployed to production

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

$app = new class($config) extends yii\web\Application
{
    protected function bootstrap()
    {
        parent::bootstrap();
        require(__DIR__ . '/../config/aliases.php');
    }
};

$app->run();