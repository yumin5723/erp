<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use backend\components\BackendActiveRecord;

class Channel extends BackendActiveRecord {
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'channel';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            [['connect_number','channel_number','goods_name','goods_quantity','goods_weight','goods_volume'],'required'],
            [['expected_time','actual_time','receiver','order_receiver','image','packing_details','info'],'safe'],
            ['goods_quantity','integer'],
        ];
    }
    public function behaviors()
    {
        return BaseArrayHelper::merge(
            parent::behaviors(),
            [
                'timestamp' => [
                    'class' => 'yii\behaviors\TimestampBehavior',
                    'attributes' => [
                        ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'modified'],
                        ActiveRecord::EVENT_BEFORE_UPDATE => 'modified',
                    ],
                    'value' => function (){ return date("Y-m-d H:i:s");}
                ],
           ]
        );
    }
    /**
     * [saveOrderNumber description]
     * @return [type] [description]
     */
    public function saveOrderNumber($order_number){
        $orders = @explode("|", $order_number);
        $result = OrderChannel::find()->where(['connect_number'=>$this->connect_number])->one();
        if(!empty($result)){
            OrderChannel::deleteAll(['connect_number'=>$this->connect_number]);
        }
        foreach($orders as $order){
                $orderChannel = new OrderChannel;
                $orderChannel->connect_number = $this->connect_number;
                $orderChannel->order_id = $order;
                $orderChannel->save();
        }
    }
    public function getOrders(){
        return $this->hasMany(OrderChannel::className(),['connect_number'=>'connect_number']);
    }
}