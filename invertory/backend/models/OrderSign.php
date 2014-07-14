<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use backend\components\BackendActiveRecord;

class OrderSign extends BackendActiveRecord {
    const ORDER_IS_SIGNED = 0;
    const ORDER_IS_NOT_SIGNED = 1;
    public $order_viewid;
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'order_sign';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            [['order_id','sign_date'],'required'],
            [['image','info','signer'],'safe'],
            ['order_id','required'],
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
                'attributeStamp' => [
                      'class' => 'yii\behaviors\AttributeBehavior',
                      'attributes' => [
                          ActiveRecord::EVENT_BEFORE_INSERT => ['created_uid','modified_uid'],
                          ActiveRecord::EVENT_BEFORE_UPDATE => 'modified_uid',
                      ],
                      'value' => function () {
                          return Yii::$app->user->id;
                      },
                ],
           ]
        );
    }
    public function attributeLabels(){
        return [
            'order_id'=>'订单号',
            'signer'=>'签收人',
            'sign_date'=>'日期',
            'image'=>'签收图片',
            'created'=>'操作时间',
            'created_uid'=>'操作人',
            'order_viewid'=>'订单号',
            'info' => '原因备注',
            'type'=>'签收情况',
        ];
    }
    public function getImage(){
        // return "<img src={$this->image} width='300px' />";
        return "<a class='prettyPhoto[pp_gal]' href={$this->image}><img width='300px' src={$this->image}></a>";
    }
    public function getSignType(){
        // return "<img src={$this->image} width='300px' />";
        if($this->type == OrderSign::ORDER_IS_SIGNED){
            return "正常签收";
        }else{
            return "签收不成功";
        }
    }
}