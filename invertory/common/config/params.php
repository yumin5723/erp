<?php

Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('customer', dirname(dirname(__DIR__)) . '/customer');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('gcommon', dirname(dirname(__DIR__)) . '/gcommon');

return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',

    'components.cache' => [
        'class' => 'yii\caching\FileCache',
    ],

    'components.mail' => [
        'class' => 'yii\swiftmailer\Mailer',
        'viewPath' => '@common/mails',
    ],
];
