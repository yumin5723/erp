<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use backend\components\BackendActiveRecord;

class Package extends BackendActiveRecord {
    const METHOD_HIGHWAY = 1;
    const METHOD_RAILWAY = 2;
    const METHOD_AIR = 3;
    const METHOD_EXPRESS = 4;
    public $order_ids = [];
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'package';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            [['num','actual_weight','throw_weight','volume','method','trunk','delivery','price'],'required'],
            [['box','info','order_ids'],'safe'],
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
    public function getMethod(){
        return [
            self::METHOD_EXPRESS=>'快递',
            self::METHOD_AIR=>'航空',
            self::METHOD_RAILWAY=>'铁路',
            self::METHOD_HIGHWAY=>'公路',
        ];
    }
    public function saveOrderPackage(){
        $orders = $this->order_ids;
        foreach($orders as $order){
            $orderPackage = new OrderPackage;
            $orderPackage->order_id = $order;
            $orderPackage->package_id = $this->id;
            $orderPackage->save();

            $as = Order::findOne($order);
            $as->status = Order::PACKAGE_ORDER;
            $as->save(false);
        }
    }
    /**
    * Validates the password.
    * This method serves as the inline validation for password.
    */
    public function checkQuantity()
    {
        if (!$this->hasErrors()) {
            $code = $this->goods_code;
            $quantity = Stock::find()->where(['code'=>$code])->sum('actual_quantity');
            if ($this->goods_quantity > $quantiy) {
                $this->addError('goods_quantity', '库存不足.');
            }
        }
    }
    /**
     * [getCanUseStorerooms description]
     * @return [type] [description]
     */
    public function getCanUseStorerooms(){
        $rs = Storeroom::find()->all();
        $arr = [];
        if($rs){
            foreach($rs as $key=>$v){
                $arr[$v['id']]=$v['name'];
            }

        }
        return $arr;
    }
    public function getStoreroom(){
        return $this->hasOne(Storeroom::className(),['id'=>'storeroom_id']);
    }
        /**
     * [getCanUseStorerooms description]
     * @return [type] [description]
     */
    public function getCanUseActive(){
        $rs = Stock::find()->all();
        $arr = [];
        if($rs){
            foreach($rs as $key=>$v){
                $arr[$v['active']]=$v['active'];
            }

        }
        return $arr;
    }
}