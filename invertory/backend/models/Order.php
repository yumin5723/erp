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
            [['owner_id','recipients','recipients_address','recipients_contact'],'required'],
            [['goods_active','storeroom_id','to_city','info','limitday','status'],'safe'],
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
    public function getCanUseGoodsByOwnerId(){
        $rs = Stock::find()->where(['owner_id'=>$this->owner_id,'storeroom_id'=>$this->storeroom_id])->all();
        $arr = [];
        if($rs){
            foreach($rs as $key=>$v){
                $arr[$v->material['code']]['code'] = $v->material['code'];
                $arr[$v->material['code']]['name'] = $v->material['name'];
                $arr[$v->material['code']]['count'] = $v->stocktotal->total;
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
    public function getCanChoseMethod(){
        $result = ["4小时"=>'4小时','12小时'=>'12小时','24小时'=>'24小时','3天'=>'3天',"5天"=>'5天'];
        return $result;
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
    public function getPrintLink(){
        return '
            return \yii\helpers\Html::a("打印","/order/print?id=$model->id",["target"=>"_blank"]);
        ';
    }
    public function getPackageInfo(){
        return $this->hasOne(Package::className(),['id'=>'package_id'])
                    ->via('orderPackage');
    }
    public function getOrderPackage(){
        return $this->hasOne(OrderPackage::className(),['order_id'=>'id']);
    }
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
    public function getLink(){
        return '
            return \yii\helpers\Html::input("text","selection[]");
        ';
    }
    public function createOrderDetail($postData){
        if(!is_array($postData)){
            return false;
        }
        foreach($postData as $key=>$value){
            $model = new OrderDetail;
            $model->order_id = $this->id;
            $model->goods_code = $value['code'];
            $model->goods_quantity = $value['count'];
            $model->save();
        }
        //Subtract stock
        foreach($postData as $key=>$value){
            $material = Material::find()->where(['code'=>$value['code']])->one();
            $model = new Stock;
            $model->material_id = $material->id;
            $model->storeroom_id = $this->storeroom_id;
            $model->owner_id = $this->owner_id;
            $model->project_id = $material->project_id;
            $model->actual_quantity = 0 - $value['count'];
            $model->stock_time = date('Y-m-d H:i:s');
            $model->created = date('Y-m-d H:i:s');
            $model->increase = Stock::IS_NOT_INCREASE;
            $model->order_id = $this->id;
            $model->save(false);

            //subtract stock total
            StockTotal::updateTotal($material->id,(0 - $value['count']));
        }
        return true;
    }
    public function attributeLabels(){
        return [
            'goods_active'=>'活动',
            'viewid'=>'订单号',
            'storeroom_id'=>'出库仓库',
            'to_city'=>'收货城市',
            'recipients'=>'收货人',
            'recipients_address'=>'收货地址',
            'recipients_contact'=>'收货人联系方式',
            'info'=>'备注',
            'limitday'=>'到货需求',
            'status'=>'订单状态',
            'created'=>'下单时间',
            'created_uid'=>'下单人',
        ];
    }

}