<?php
return [
	'components' => [
  		'db' => [
  			'class' => 'yii\db\Connection',
  			'charset' => 'utf8',
  		],
  		'gqueue' => [
            'class' => 'gcommon\components\gqueue\GQueue',
        ],
        'redis' => [
             'class' => 'yii\redis\Connection',
             'port' => '6379',
        ],
	  ],
];