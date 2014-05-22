<?php

// set correct script paths
$rootDir = __DIR__ . '/../../..';

$params = array_merge(
	require($rootDir . '/common/config/params.php'),
	require($rootDir . '/common/config/params-local.php')
);

return yii\helpers\ArrayHelper::merge(
    [
        'id' => 'app-frontend',
        'basePath' => dirname(__DIR__),
        'vendorPath' => $rootDir . '/vendor',
        'controllerNamespace' => 'frontend\controllers',
        'extensions' => require($rootDir . '/vendor/yiisoft/extensions.php'),
        'components' => [
            'db' => $params['components.db'],
            'cache' => $params['components.cache'],
            'mail' => $params['components.mail'],
            'user' => [
                'identityClass' => 'common\models\User',
                'enableAutoLogin' => true,
            ],
            'log' => [
                'traceLevel' => 3,
                'targets' => [
                    [
                        'class' => 'yii\log\FileTarget',
                        'levels' => ['error', 'warning'],
                    ],
                ],
            ],
            'errorHandler' => [
                'errorAction' => 'site/error',
            ],
        ],
	'params' => $params,
    ],
	require(__DIR__ . '/../_config.php'),
	[
		'components' => [
			'db' => [
				'dsn' => 'mysql:host=127.0.0.1;dbname=yii2advanced',
                'username' => 'root',
                'password' => 'password',
			],
            'fixture' => [
                'class' => 'yii\test\DbFixtureManager',
                'basePath' => '@common/tests/unit/fixtures',
            ],
		],
	]
);