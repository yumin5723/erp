<?php
namespace common\models;

use Yii;
use common\components\MallActiveRecord;

class Order extends MallActiveRecord{

    protected $pk = 'order_id';

	public static function tableName(){
		return 'order';
	}


    /**
     * 取消订单检查操作
     * @param array $condition
     *
     * $condition = [
     *      order_id 订单ID
     *      uid 用户ID
     *      cp_id 商家ID
     *      admin 是否是管理员 1 是 0 不是
     * ]
     * @return bool true 取消成功 false 取消失败
     */
    public function cancelOrder($condition = [])
    {
        $order_id = $_REQUEST['id'];
        if(empty($order_id)){
            if(empty($condition['order_id'])){
                return false;
            }
            $order_id = $condition['order_id'];
        }
        if(!empty($condition['admin'])){
            //管理员操作 没有额外条件
        }elseif(!empty($condition['cp_id'])){
            //商家操作
            $where['cp_id'] = intval($condition['cp_id']);
        }else{
            //用户操作
            if(empty($condition['uid'])){
                return false;
            }
            $where['uid'] = intval($condition['uid']);
        }
        $post = static :: find()
                ->where($where)
                ->one();
        return $this->performCancelOrder($post);
    }


    public function deleteOrder($condition = [])
    {

    }


    private function performCancelOrder($object)
    {
        if(1 === $object->shipping_status){
            $message['status'] = 0;
            $message['code'] = 1;
            $message['hint'] = "商品已经发货，不能取消";
            return $message;
        }

        if(0 === $object->pay_status){
            //TODO 用户没有付款
        }elseif(2 === $object->pay_status){
            //TODO 用户已经付款的退还金额
        }
    }


    /**
     * 退还金钱
     * @param $order 订单对象
     * @return bool 成功返回 true 失败返回 false
     */
    protected function refundAction($order)
    {
        //TODO 未完成
        return true;
    }


    /**
     * 修改库存操作
     * @param $orderInfo 数组对象
     * @return bool 成功返回true 失败返回false
     */
    public function updateStock($orderInfo)
    {
        foreach($orderInfo as $value){
            //TODO 未完成
            $sku = $value->goods_sku;
            $goods_id = $value->goods_id;
        }

        return true;
    }
    
}