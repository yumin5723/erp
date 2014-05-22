<?php
// This is global bootstrap for autoloading 


defined('YII_DEBUG') or define('YII_DEBUG', true);

defined('YII_ENV') or define('YII_ENV', 'test');

require_once(__DIR__ . '/../../vendor/autoload.php');

require_once(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

// set correct script paths

Yii::setAlias('@tests', __DIR__);
Yii::setAlias('@common', dirname(__DIR__));