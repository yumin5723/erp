<?php
namespace backend\components;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use backend\models\Manager;

class BackendActiveRecord extends ActiveRecord{

    public function getCreateduser(){
    	return $this->hasOne(Manager::className(), ['id' => 'created_uid']);
    }
    public function getModifieduser(){
    	return $this->hasOne(Manager::className(), ['id' => 'modified_uid']);
    }

}