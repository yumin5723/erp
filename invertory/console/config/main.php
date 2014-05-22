<?php
$rootDir = __DIR__ . '/../..';

$params = array_merge(
	require($rootDir . '/common/config/params.php'),
	require($rootDir . '/common/config/params-local.php'),
	require(__DIR__ . '/params.php'),
	require(__DIR__ . '/params-local.php')
);

return [
	'id' => 'app-console',
	'basePath' => dirname(__DIR__),
	'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
	'controllerNamespace' => 'console\controllers',
	'modules'=>[
		'cms'=>[
			'class'=>'gcommon\cms\CmsModule',
      		'domain' => 'www.goumin.com',
		]
	],
	'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
	'components' => [
		'user' => [
		  	'class' => backend\components\ManagerUser::className(),
				'identityClass' => 'backend\models\Manager',//common\models\User
				'enableAutoLogin' => true,
		],
		'db' => $params['components.db'],
        'dogdb' => $params['components.dogdb'],
        'malldb' => $params['components.malldb'],
		'cmsdb' => $params['components.cmsdb'],
		'dogdb' => $params['components.dogdb'],
		'statisticaldb'=>$params['components.statisticaldb'],
		'cache' => $params['components.cache'],
		'mail' => $params['components.mail'],
		 'autoloader' => [
            'class' => 'gcommon\extensions\gautoloader\EAutoloader',
    	],
	    'publisher'=>$params['components.publisher'],
        'stringview'=>[
               'class' => 'gcommon\extensions\ETwigViewRenderer',
               'extensions'=>['common\extensions\Twig\GTwigExtension'],
    	],
        'cmsEvent' => [
            'class' => 'gcommon\cms\components\CmsEvent',
    	],
	    'cmsRenderer'=>[
            'class'=>'gcommon\cms\components\CmsRenderer',
    	],
		'log' => [
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning','info'],
				],
			],
		],
	],
	'params' => $params,
];
