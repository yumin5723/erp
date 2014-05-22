<?php
namespace backend\components;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class StatActiveRecord extends ActiveRecord{

    public static function getDb(){
        return \Yii::$app->get("statisticaldb");
    }

}