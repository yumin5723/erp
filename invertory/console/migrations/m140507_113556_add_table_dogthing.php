<?php

use yii\db\Schema;

class m140507_113556_add_table_dogthing extends \yii\db\Migration
{

	public function init()
    {
        $this->db = Yii::$app->get('cmsdb');
    }
    public function up()
    {

    	$this->dropTable("dog_newthings");
    	$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		$this->createTable('dog_newthings', [
		  'id' => ' int( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY',
		  'spe_id' => 'int( 11 ) NOT NULL DEFAULT "0"',
		  'forum_id' => 'int( 11 ) NOT NULL DEFAULT "0"',
		], $tableOptions);
    }

    public function down()
    {
        echo "m140507_113556_add_table_dogthing cannot be reverted.\n";

        return false;
    }
}
