<?php

use yii\db\Schema;

class m140509_092246_add_column_seo_admin extends \yii\db\Migration
{
    public function up()
    {
        $this->addColumn('seo_admin','scenarios','text NOT NULL DEFAULT ""');
    }

    public function down()
    {
        $this->dropColumn('seo_admin','scenarios');

    }
}
