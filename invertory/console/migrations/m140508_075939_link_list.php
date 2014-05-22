<?php

use yii\db\Schema;

class m140508_075939_link_list extends \yii\db\Migration
{
    public function up()
    {
    	$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}
    	$this->createTable('link_list', [
		  'link_id' => ' int( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE KEY ',
		  'link_text' => 'varchar(255) NOT NULL DEFAULT ""',
		  'link_url' => 'varchar(255) NOT NULL DEFAULT ""',
		  'link_type' => 'int(11) NOT NULL DEFAULT "0"' ,
		], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('link_list');
    }
}
