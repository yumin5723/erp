<?php
return [
	'components'=>[
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=127.0.0.1;dbname=yltd',
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		],
		'request'=>[
            'cookieValidationKey'=>"cnOOB2bIujtIod1K3th3BETtjP112233",
        ],	
	],
];
