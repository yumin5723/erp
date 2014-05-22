<?php

use Yii;
use yii\db\Schema;

class m140506_041346_add_column_threadclass extends \yii\db\Migration
{

	public function init()
    {
        $this->db = Yii::$app->get('dogdb');
    }
    public function up()
    {
    	$this->addColumn('pre_forum_threadclass','title','varchar(255) NOT NULL DEFAULT ""');
    	$this->addColumn('pre_forum_threadclass','keywords','varchar(255) NOT NULL DEFAULT ""');
    	$this->addColumn('pre_forum_threadclass','description','varchar(255) NOT NULL DEFAULT ""');
    }

    public function down()
    {
        $this->dropColumn('pre_forum_threadclass','title');
        $this->dropColumn('pre_forum_threadclass','keywords');
        $this->dropColumn('pre_forum_threadclass','keywords');
    }
}
