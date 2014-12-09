<?php
namespace backend\models;

use backend\components\BackendActiveRecord;

/**
 * Class MallCity
 * @package common\models
 */
class City extends BackendActiveRecord
{

    public static function tableName()
    {
        return 'city';
    }
    public static function getCityByPid($pro_id){
    	$city = static::find()->where(['pid'=>$pro_id])->asArray()->all();
    	$ret = [];
    	return array_map(function($a) use ($ret){
    		$ret = ['id'=>$a['id'],'name'=>$a['name']];
    		return $ret;
    	},$city);

    	// return \yii\helpers\ArrayHelper::map($city,'id','name');
    }
}