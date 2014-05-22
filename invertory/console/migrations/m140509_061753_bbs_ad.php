<?php

use yii\db\Schema;

class m140509_061753_bbs_ad extends \yii\db\Migration
{
    public function up()
    {
    	$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}
    	$this->createTable('bbs_ad', [
		  'ad_id' => ' int( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE KEY ',
		  'ad_image' => 'varchar(255) NOT NULL DEFAULT ""',
		  'ad_url' => 'varchar(255) NOT NULL DEFAULT ""',
		  'ad_order' => 'int(11) NOT NULL DEFAULT "0"' ,
		], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('bbs_ad');
    }
}
