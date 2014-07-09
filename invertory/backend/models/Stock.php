<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use backend\components\BackendActiveRecord;

class Stock extends BackendActiveRecord {
    const IS_NOT_INCREASE = 1;
    public $upload;
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'stock';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            [['material_id','storeroom_id','project_id','owner_id'],'required'],
            [['forecast_quantity','actual_quantity','stock_time','delivery'],'safe'],
            [['material_id'],'required','on'=>'search'],
            [['material_id','actual_quantity','destory_reason'],'required','on'=>'destory'],
            [['actual_quantity'],'checkQuantity','on'=>'destory'],
        ];
    }
    /**
    * Validates the can use destory num.
    * This method serves as the inline validation for password.
    */
    public function checkQuantity()
    {
        if (!$this->hasErrors()) {
            $destory = $this->actual_quantity;
            $quantity = StockTotal::find()->where(['material_id'=>$this->material_id,'storeroom_id'=>$this->storeroom_id])->one();
            if ($destory > $quantity->total) {
                $this->addError('actual_quantity', '您要销毁的数量超过当前的库存数量，当前您最多可销毁'.$quantity->total."件");
            }
        }
    }
    public function scenarios()
    {
        return [
            'destory' => ['material_id','actual_quantity','project_id','storeroom_id','destory_reason'],
            'default' => ['material_id','storeroom_id','project_id','owner_id','forecast_quantity','actual_quantity','stock_time','delivery'],
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
     * [getCanUseProjects description]
     * @return [type] [description]
     */
    public function getCanUseProjects(){
        $rs = Project::find()->all();
        $arr = [];
        if($rs){
            foreach($rs as $key=>$v){
                $arr[$v['id']]=$v['name'];
            }

        }
        return $arr;
    }
    /**
     * [getCanUseProjects description]
     * @return [type] [description]
     */
    public function getCanUseMaterial(){
        $rs = Material::find()->all();
        $arr = [];
        if($rs){
            foreach($rs as $key=>$v){
                $arr[$v['id']]=$v['code']."  ".$v['name'];
            }

        }
        return $arr;
    }
    /**
     * [getCanUseProjects description]
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
    public function getStockStatus(){
        $arr = ['0'=>'入库','1'=>'出库'];
        return $arr;
    }
    public function getProjects(){
        return $this->hasOne(Project::className(),['id'=>'project_id']);
    }
    public function getStoreroom(){
        return $this->hasOne(Storeroom::className(),['id'=>'storeroom_id']);
    }
    public function getMaterial(){
        return $this->hasOne(Material::className(),['id'=>'material_id']);
    }
    public function getOwners(){
        return $this->hasOne(Owner::className(),['id'=>'owner_id']);
    }
    public function getStocktotal(){
        return $this->hasOne(StockTotal::className(),['storeroom_id'=>'storeroom_id']);
    }
    public function getLink(){
        return '
            if($model->increase == 1){
                if($model->destory_reason != ""){
                    return "<span title=$model->destory_reason>销毁</span>";
                }else{
                    return "出库  ".\yii\helpers\Html::a("查看明细","/order/view?id=$model->order_id");
                }
            }else{
                return "入库";
            }
            
        ';
    }
    public function attributeLabels(){
        return [
            'material_id'=>'物料',
            'storeroom_id'=>'入库仓库',
            'project_id'=>'所属项目',
            'forecast_quantity'=>'预计入库数量',
            'actual_quantity'=>'实际出入库数量',
            'owner_id'=>'所属人',
            'stock_time'=>'出入库时间',
            'delivery'=>'送货方',
            'increase'=>'出入库标记',
            'order_id'=>'订单号',
            'created'=>'添加时间',
            'created_uid'=>'创建人',
            'destory'=>'销毁数量',
            'destory_reason'=>'销毁原因',
        ];
    }
    public function getExportLink(){
        return ['0'=>'/stock/export?mid='.$this->material_id];
    }

}