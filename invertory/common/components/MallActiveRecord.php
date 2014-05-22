<?php
namespace common\components;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class MallActiveRecord extends ActiveRecord{

    public static function getDb(){
        return \Yii::$app->get("malldb");//malldb
    }

}