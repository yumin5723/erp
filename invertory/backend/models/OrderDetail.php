<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use backend\components\BackendActiveRecord;
use backend\models\Stock;
use backend\models\OrderChannel;

class OrderDetail extends BackendActiveRecord {
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'order_detail';
    }
    public function getMaterial(){
    	return $this->hasOne(Material::className(),['code'=>'goods_code']);
    }
    public function getOrders(){
        return $this->hasOne(Order::className(),['id'=>'order_id']);
    }
    public function getStoreroom(){
        return $this->hasOne(Storeroom::className(),['id'=>'storeroom_id'])->via('orders');
    }
    public function getOwners(){
        return $this->hasOne(Owner::className(),['id'=>'owner_id'])->via('orders');
    }
}