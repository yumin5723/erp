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
  ],
  'components' => [
      'log' => [
              'targets' => [
                  'file' => [
                      'class' => 'yii\log\FileTarget',
                      'levels' => ['trace', 'info'],
                      'categories' => ['yii\*'],
                  ],
                  'email' => [
                      'class' => 'yii\log\EmailTarget',
                      'levels' => ['error', 'warning'],
                      'message' => [
                          'to' => 'admin@example.com',
                      ],
                  ],
              ],
          ],

  ],
  'params' => $params,
];
