<?php

use yii\db\Schema;

class m140516_042830_add_bbsad_column extends \yii\db\Migration
{
    public function up()
    {
        $this->addColumn('bbs_ad','type','smallint(6) NOT NULL DEFAULT "0"');
    }

    public function down()
    {
        $this->dropColumn('bbs_ad','type');

    }
}
