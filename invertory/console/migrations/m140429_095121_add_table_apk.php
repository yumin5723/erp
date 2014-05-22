<?php

use yii\db\Schema;

class m140429_095121_add_table_apk extends \yii\db\Migration
{
	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		$this->createTable('apk', [
		  'id' => ' int( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE KEY ',
		  'name' => 'varchar(64) NOT NULL DEFAULT ""',
		  'version' => 'varchar(64) NOT NULL DEFAULT ""',
		  'message' => 'text NOT NULL DEFAULT ""',
		  'path' => 'varchar(255) NOT NULL DEFAULT ""' ,
		  'created' => 'datetime NOT NULL',
		  'modified' =>' datetime NOT NULL',
		], $tableOptions);
	}

	public function down()
	{
		$this->dropTable('apk');
	}
}
