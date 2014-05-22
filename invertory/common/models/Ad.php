<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\MallActiveRecord;

class Ad extends MallActiveRecord{
	public static function tableName(){
		return "ad_home_label";
	}
}