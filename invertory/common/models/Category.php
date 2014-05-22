<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\MallActiveRecord;
use yii\data\ActiveDataProvider;
use common\extensions\NestedSetBehavior;
use yii\helpers\BaseArrayHelper;

class Category extends MallActiveRecord {

    public static function tableName(){
    	return "category";
    }

    public function getCategory(){
    	$data = static::find()->where("level>1")->all();
    	$rs = [];
    	if($data){
    		foreach ($data as $value) {
    			if($value['level']==1){
    				$rs[$value['id']] = $value['name'];
    			}else{
    				$rs['cat_son'][$value['id']] = $value['name'];
     			}
    		}
    	}
    	return $data;
    }

}