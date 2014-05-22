<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\MallActiveRecord;

class Bonus extends MallActiveRecord{
	/**
	 * [tableName description]
	 * @return [type] [description]
	 */
	public static function tableName(){
		return 'bonus_setting';
	}
}