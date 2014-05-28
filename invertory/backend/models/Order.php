<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use backend\components\BackendActiveRecord;
use backend\models\Stock;

class Order extends BackendActiveRecord {
    const NEW_ORDER = 0;
    const PACKAGE_ORDER = 1;
    const SHIPPING_ORDER = 2;
    const SIGN_ORDER = 3;
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
            [['goods_code','goods_quantity','recipients','recipients_address','recipients_contact'],'required'],
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
    public function getOptLink(){
        return '
            return \yii\helpers\Html::a("操作","/package/operate?id=$model->id",["target"=>"_blank"]);
        ';
    }
    public function getPackageInfo(){
        return $this->hasOne(Package::className(),['id'=>'package_id'])
                    ->via('orderPackage',['order_id'=>'id']);
    }
    public function getOrderPackage(){
        return $this->hasMany(OrderPackage::className(),['order_id'=>'id']);
    }
    public function getMethodText(){
        $methods = (new Package())->getMethod();
        if(isset($methods[$this->packageInfo->method])){
            return $methods[$this->packageInfo->method];
        }
        return "undefined";
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