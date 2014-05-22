<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Assign extends ActiveRecord{
	public static function tableName(){
		return "auth_item_child";
	}
}