<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use backend\components\StatActiveRecord;

class StatOperation extends StatActiveRecord
{

     public static function tableName()
    {
        return 'operations';
    }

    public function getAllOperationData($status=false){
        if($status){
            $page = ['pageSize'=>30];
        }else{
            $page = false;
        }
        $provider = new ActiveDataProvider([
              'query'      => StatOperation::find()->orderBy('id desc')->limit(30),
            'pagination' => $page,
        ]);
        return $provider;
    }
    public function attributeLabels(){
        return [
            'statistics_date'=>'统计日期',
            'register_nums'=>'注册用户数(人)',
            'lottery_nums'=>'抽奖人数(人)',
            'auction_nums'=>'竞拍人数(人)',
            'posts'=>'论坛发帖量',
            'ask_nums'=>'知道参与人数(人)',
            'health_nums'=>'医疗参与人数(人)',
            'create_date'=>'记录日期',
        ];
    }
}