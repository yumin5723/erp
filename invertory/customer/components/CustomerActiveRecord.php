<?php
namespace customer\components;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use customer\models\Manager;

class CustomerActiveRecord extends ActiveRecord{

    public function getCreateduser(){
    	return $this->hasOne(Manager::className(), ['id' => 'created_uid']);
    }
    public function getModifieduser(){
    	return $this->hasOne(Manager::className(), ['id' => 'modified_uid']);
    }

}