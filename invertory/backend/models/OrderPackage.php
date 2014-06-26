<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use backend\components\BackendActiveRecord;

class OrderPackage extends BackendActiveRecord {
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'order_package';
    }
    public function getOrders(){
    	return $this->hasOne(Order::className(),['id'=>'order_id']);
    }
    public function getDetail(){
    	return $this->hasMany(OrderDetail::className(),['order_id'=>'order_id']);
    }
    public function getPackages(){
    	return $this->hasOne(Package::className(),['id'=>'package_id']);
    }
}