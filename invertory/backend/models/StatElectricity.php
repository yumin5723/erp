<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use backend\components\StatActiveRecord;

class StatElectricity extends StatActiveRecord
{

     public static function tableName()
    {
        return 'electricity';
    }

    public function getAllElectricityData($status=false){
        if($status){
            $page = ['pageSize'=>30];
        }else{
            $page = false;
        }
        $provider = new ActiveDataProvider([
              'query'      => StatElectricity::find()->orderBy('id desc')->limit(30),
            'pagination' => $status,
        ]);
        return $provider;
    }
    public function attributeLabels(){
        return [
            'statistics_date'=>'统计日期',
            'mobile_sales'=>'移动端销售额(元)',
            'buy_nums'=>'移动端购买人数(人)',
            'visitors'=>'移动端访问人数(人)',
            'dau'=>'日访问人数(人)',
            'second_retain'=>'次日留存(人)',
            'week_retain'=>'七日留存(人)',
            'increasing'=>'日新增用户(人)',
            'create_date'=>'记录日期',
        ];
    }
}