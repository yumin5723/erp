<?php

use yii\db\Schema;

class m140516_095508_add_shipping_config_column extends \yii\db\Migration
{
    public function init()
    {
        $this->db = Yii::$app->get('malldb');
    }


    public function up()
    {
        $this->addColumn('shipping_config','no_first_weight',' tinyint(1) NOT NULL DEFAULT "0"');
    }


    public function down()
    {
        $this->dropColumn('shipping_config','no_first_weight');
    }
}
