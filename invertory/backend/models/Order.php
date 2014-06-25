<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use backend\components\BackendActiveRecord;
use backend\models\Stock;
use backend\models\OrderChannel;

class Order extends BackendActiveRecord {
    const NEW_ORDER = 0;
    const PACKAGE_ORDER = 1;
    const SHIPPING_ORDER = 2;
    const SIGN_ORDER = 3;
    public $goods_code;
    public $goods_quantity;
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'order';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            [['goods_code','owner_id','goods_quantity','recipients','recipients_address','recipients_contact'],'required'],
            [['goods_active','storeroom_id','info','status'],'safe'],
            ['goods_quantity','integer'],
            ['goods_quantity','checkQuantity']
            // ['goods_quantity',]/
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
    /**
     * [getCanUseStorerooms description]
     * @return [type] [description]
     */
    public function getCanUseGoodsByOwnerId($owner_id,$storeroom_id){
        $rs = Stock::find()->where(['owner_id'=>$owner_id,'storeroom_id'=>$storeroom_id])->all();
        $arr = [];
        if($rs){
            foreach($rs as $key=>$v){
                $arr[$v->material['code']]="物料编号:  ".$v->material['code']."  ".$v->material['name']."  现有库存:".$v->stocktotal->total;
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
    /**
     * [getCanUseStorerooms description]
     * @return [type] [description]
     */
    public function getCanUseOwner(){
        $rs = Owner::find()->all();
        $arr = [];
        if($rs){
            foreach($rs as $key=>$v){
                $arr[$v['id']]=$v['english_name'];
            }

        }
        return $arr;
    }
    public function getOptLink(){
        return '
            return \yii\helpers\Html::a("操作","/package/operate?id=$model->id");
        ';
    }
    public function getPackageInfo(){
        return $this->hasOne(Package::className(),['id'=>'package_id'])
                    ->viaTable('order_package',['order_id'=>'id']);
    }
    // public function getOrderPackage(){
    //     return $this->hasMany(OrderPackage::className(),['order_id'=>'id']);
    // }
    public function getMethodText(){
        $methods = (new Package())->getMethod();
        if(isset($methods[$this->packageInfo->method])){
            return $methods[$this->packageInfo->method];
        }
        return "undefined";
    }
    public function getChannel(){
        return $this->hasOne(Channel::className(),['connect_number'=>'connect_number'])
                    ->viaTable('order_channel',['order_id'=>'id']);
    }
    /**
     * [getOrderStatus description]
     * @return [type] [description]
     */
    public function getOrderStatus(){
        if($this->status == self::NEW_ORDER){
            return "未处理";
        }
        if($this->status == self::PACKAGE_ORDER){
            return "已包装";
        }
        if($this->status == self::SHIPPING_ORDER){
            return "已发货";
        }
        if($this->status == self::SIGN_ORDER){
            return "已签收";
        }
    }
}