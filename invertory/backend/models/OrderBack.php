<?php
namespace backend\models;

use common\models\Order;
use yii\data\ActiveDataProvider;

class OrderBack extends Order
{

    public function rules(){
        return [
            ['order_price','required'],
            ['ship_price','required'],
            ['ship','required'],
            ['kdgs','required'],
            ['memo','required'],
            ['order_status','required'],
            ['best_time','required'],
        ];
    }
    public function attributeLabels(){
        return [
            'order_id'=>'订单ID',
            'order_sn'=>'订单流水号',
            'uid'=>'用户ID',
            'cp_id'=>'商家ID',
            'order_price'=>'订单价格',
            'pay_price'=>'支付价格',
            'ship_price'=>'快递费用',
            'bonus_price'=>'使用红包',
            'integral'=>'使用积分',
            'money_paid'=>'使用余额',
            'weight'=>'订单重量',
            'ship'=>'发货方式',
            'kdgs'=>'发货公司',
            'memo'=>'订单留言',
            'order_status'=>'订单状态',
            'pay_type'=>'支付类型',
            'best_time'=>'送货时间',
            'order_date'=>'订单生存时间',
            'flag'=>'订单标志',
            'is_delete'=>'是否删除',
        ];
    }


    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getOrderInfo()
    {
        return $this->hasMany(OrderInfoBack::className(), ['order_id' => 'order_id']);
    }

    /**
     * 获取全部订单
     * @return ActiveDataProvider
     */
    public function selectOrder()
    {
        $provider = new ActiveDataProvider([
            'query' => static::find()
                    ->orderby('order_id asc'),
            'sort' => [
                'attributes' => ['order_id','order_sn','uid','order_price','pay_price',],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $provider;
    }

    /**
     * 获取一条订单数据
     * @return mixed
     */
    public function findOrder()
    {
        $orderId = $_GET['id'];
        $result = static :: find()
                ->where([$this->pk => $orderId])
                ->one();
        $result->order_date = date('Y-m-d H:i:s',$result->order_date);
        return $result;
    }


    /**
     * 修改订单操作
     * @return bool
     */
    public function updateOrder()
    {
        $post = static :: find($_POST[$this->formName()][$this->pk]);
        if($post->load($_POST) && $post->save()){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 返回订单状态
     * @return callable
     */
    public function orderStatus()
    {
        return function($data){
            switch($data->order_status){
                case 0:
                    return '未付款';
                case 1:
                    return '付款中';
                case 2:
                    return '已付款';
                case 3:
                    return '备货中';
                case 4:
                    return '已发货';
                case 5:
                    return '已收货';
                case 6:
                    return '已取消';
                case 7:
                    return '无效';
                case 8:
                    return '申请退货';
                case 9:
                    return '确认退货';
                case 10:
                    return '完成退货';
            }
        };
    }

    /**
     * 获取订单可以修改的状态
     * @return callable
     */
    public function getOrderStatus()
    {
        return function($data){
            if($data->order_status === 0){
                return "
                        <a href='/order/changeorderstatus?id=$data->order_id&act=2'>待处理</a> &nbsp;
                        <a href='/order/changeorderstatus?id=$data->order_id&act=7'>无效</a> &nbsp;
                        ";
            }elseif($data->order_status === 1){
                return "
                        <a href='/order/changeorderstatus?id=$data->order_id&act=2'>待处理</a> &nbsp;
                        <a href='/order/changeorderstatus?id=$data->order_id&act=7'>无效</a> &nbsp;
                        ";
            }elseif($data->order_status === 2){
                return "
                        <a href='/order/changeorderstatus?id=$data->order_id&act=7'>无效</a> &nbsp;
                        ";
            } elseif($data->order_status === 8){
                return "
                        <a href='/order/changeorderstatus?id=$data->order_id&act=9'>确认退货</a> &nbsp;
                        ";
            }elseif($data->order_status === 9){
                return "
                        <a href='/order/changeorderstatus?id=$data->order_id&act=10'>完成退货</a> &nbsp;
                        ";
            }else{
                return '无法操作';
            }
        };
    }


    /**
     * 修改订单状态
     * @return array
     * message[
     *      status : 操作状态 1 成功 0 失败
     *      inform : 操作说明
     * ]
     * @throws \Exception
     */
    public function changeOrderStatus()
    {
        $order_id = $_GET['id'];
        $action = $_GET['act'];
        if(!$order_id && !$action){
            $message['status'] = 0;
            $message['hint'] = '数据错误,无法操作';
            return $message;
        }
        $order =  static :: find($this->pk = $order_id);
        $message = [];
        if(2 == $action){ //把订单改为已经付款，待处理状态
            if(0 === $order->order_status || 1 === $order->order_status){
                $order->order_status = 2;
                $result = $order->save();
                if($result){
                    $message['status'] = 1;
                    $message['hint'] = '操作成功';
                }
            }else{
                $message['status'] = 0;
                $message['hint'] = '订单当前状态无法执行该操作';
            }
        }elseif(7 == $action){ //管理员取消订单操作
            if(0 === $order->order_status || 1 === $order->order_status || 2 === $order->order_status){
                //开启事务处理
                $transaction = static::getDb()->beginTransaction();
                try{
                    $order->order_status = 7;
                    $result = $order->save();
                    if($result){
                        if($this->updateStock($order->orderInfo)){ //减去库存
                            if(1 === $order->order_status || 2 === $order->order_status){ //需要退还金额
                                if($this->refundAction($order)){ //退还金额
                                    $transaction->commit();
                                }else{
                                    $transaction->rollBack();
                                }
                            }else{
                                $transaction->commit();
                                $message['status'] = 0;
                                $message['hint'] = '操作成功';
                            }
                        }else{
                            $transaction->rollBack();
                        }
                    }else{
                        $transaction->rollBack();
                    }
                }catch (\Exception $e){
                    $transaction->rollBack();
                    throw $e;
                }
            }else{
                $message['status'] = 0;
                $message['hint'] = '订单当前状态无法执行该操作';
            }
        }elseif(9 == $action){
            if(8 === $order->order_status){
                $order->order_status = 9;
                $result = $order->save();
                if($result){
                    $message['status'] = 1;
                    $message['hint'] = '操作成功';
                }
            }else{
                $message['status'] = 0;
                $message['hint'] = '订单当前状态无法执行该操作';
            }
        }elseif(10 === $action){
            if(9 === $order->order_status){
                $order->order_status = 10;
                $result = $order->save();
                if($result){
                    $message['status'] = 1;
                    $message['hint'] = '操作成功';
                }
            }else{
                $message['status'] = 0;
                $message['hint'] = '订单当前状态无法执行该操作';
            }
        }

        if(empty($message)){
            $message['status'] = 0;
            $message['hint'] = '操作失败';
        }
        return $message;
    }
}