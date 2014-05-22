<?php
namespace common\models;

use Yii;
use common\components\MallActiveRecord;

class  City extends MallActiveRecord{
	public static function tableName(){
		return "city";
	}
}