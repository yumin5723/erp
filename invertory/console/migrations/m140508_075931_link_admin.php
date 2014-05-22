<?php

use yii\db\Schema;

class m140508_075931_link_admin extends \yii\db\Migration
{
    public function up()
    {
    	$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}
		$this->createTable('link_admin', [
		  'id' => ' int( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE KEY ',
		  'name' => 'varchar(255) NOT NULL DEFAULT ""',
		], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('link_admin');
    }
}
