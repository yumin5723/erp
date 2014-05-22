<?php

use yii\db\Schema;

class m140508_053239_seo_admin extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('seo_admin', [
          'id' => ' int( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE KEY ',
          'tdk_name' => 'varchar(255) NOT NULL DEFAULT ""',
          'title' => 'varchar(255) NOT NULL DEFAULT ""',
          'keywords' => 'varchar(255) NOT NULL DEFAULT ""' ,
          'description' => 'text NOT NULL DEFAULT ""',
          'scenarios' =>'text NOT NULL DEFAULT ""',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('seo_admin');

    }
}
