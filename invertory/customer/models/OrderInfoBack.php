<?php
/**
 * user: liding
 */

namespace backend\models;

use common\models\OrderInfo;
use yii\data\ActiveDataProvider;

class OrderInfoBack extends OrderInfo{

    public function attributeLabels(){
        return [
            'order_id'=>'订单ID',
            'goods_id'=>'商品ID',
            'goods_name'=>'商品名称',
            'goods_tag'=>'商品属性',
            'goods_price'=>'商品价格',
            'goods_qty'=>'商品数量',
            'goods_sku'=>'商品编码',
            'goods_status'=>'商品状态',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(OrderBack::className(), ['order_id' => 'order_id']);
    }

    /**
     * 根据订单号 获取该订单的数据详情
     * @return ActiveDataProvider
     */
    public function findOrderInfo()
    {
        $order_id = $_GET['id'];
        $query = static :: find()
                ->where(['order_id' => "$order_id"]);
        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $provider;
    }

    /**
     * 返回订单详情商品的状态
     * @return callable
     */
    public function orderInfoStatus()
    {
        return function($object){
            $result = static :: find($object->info_id);
            if(1 === $result->goods_status){
                return "实物";
            }elseif(2 === $result->goods_status){
                return "虚拟物品";
            }else{
                return false;
            }
        };
    }

} 