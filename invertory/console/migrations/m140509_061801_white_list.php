<?php

use yii\db\Schema;

class m140509_061801_white_list extends \yii\db\Migration
{
    public function up()
    {
    	$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}
    	$this->createTable('white_list', [
		  'id' => ' int( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE KEY ',
		  'keyword' => 'varchar(255) NOT NULL DEFAULT ""',
		], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('white_list');
    }
}
