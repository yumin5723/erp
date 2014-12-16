<?php
namespace gcommon\cms\components;
use Yii;
class GData{
	/**
	 * [getDb description]
	 * @return [type] [description]
	 */
	public static function getDb(){
		return \Yii::$app->get("dogdb");
	}
}