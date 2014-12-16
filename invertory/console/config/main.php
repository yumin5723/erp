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
  'controllerMap'=>[
      'resque' => 'gcommon\components\gqueue\commands\ResqueController',
  ],
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
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath'=>'@app/views/mail',
            'htmlLayout'=>false,
            'transport' => [
              'class' => 'Swift_SmtpTransport',
              'host' => 'smtp.163.com',
              'username' => 'liuwanglei2001@163.com',
              'password' => 'lwl7301294',
              'port' => '25',
              'encryption' => 'tls',
            ],
        ],  
  ],
  'params' => $params,
];
