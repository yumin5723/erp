<?php
namespace backend\models;

use Yii;
use backend\models\ExpressBack;

class ExpressInfo extends ExpressBack{
    public static function tableName(){
        return "shipping_area_info";
    }
    public function rules(){
        return [
            ['info_id','safe'],
             ['area_id','required'],
             ['first_weight_price','required'],
             ['continued_weight','required'],
             ['continued_weight_price','required'],
        ];
    }
    public function addInfo(){
        
    }

}