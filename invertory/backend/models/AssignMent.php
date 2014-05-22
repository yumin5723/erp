<?php
namespace backend\models;

use yii\db\ActiveRecord;

class AssignMent extends ActiveRecord{
	public static function tableName(){
		return "auth_assignment";
	}
}