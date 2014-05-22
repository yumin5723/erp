<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\Goods;
use yii\data\ActiveDataProvider;

class GoodStatus extends Goods{

    public $value='{value}';


    public static function tableName(){
        return 'goods_status';
    }


    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['update_date'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update_date',
                ],
            ],
        ];
    }


    public function rules(){
        return [
            ['goods_id','required'],
            ['is_on_sale','integer'],
            ['is_best','required'],
            ['is_new','required'],
            ['is_hot','required'],
            ['is_promote','required'],
            ['activity_id','required'],
        ];
    }

    
    public function attributelabels(){
        return [
            'is_on_sale'=>'是否开始销售',
            'is_best'=>'是否是精品',
            'is_new'=>'是否是新品',
            'is_hot'=>'是否是热卖',
            'is_promote'=>'是否有优惠',
            'activity_id'=>'活动',
        ];
    }


    public function getAllStatus(){
        $provider = new ActiveDataProvider([
              'query' => static::find()
                      ->orderby('goods_id desc'),
            'sort' => [
                'attributes' => ['goods_id'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
    }


    public function checkAllStatus($act){
        if (\Yii::$app->request->isPost) {
            $ids = Yii::$app->request->post();
            $num =0;
            foreach ($ids['selection'] as $key => $v) {
                if($act=='up'){
                    $num += GoodStatus::updateAll(['is_on_sale' => 1], "goods_status_id = $v");
                }elseif($act=='down'){
                    $num += GoodStatus::updateAll(['is_on_sale' => 0], "goods_status_id = $v");
                }else{
                    $num += GoodStatus::updateAll(['is_delete' => 1], "goods_status_id = $v");
                }
            }
            //$v = explode(',',$ids['selection']);
            //GoodStatus::updateAll(['is_on_sale' => 1], "goods_id in ($v)");
            return $num;
        }
    }


    public function deleteGoods($id){
        $model = GoodStatus::findOne($id);
        if($model){
            $model->is_delete = 1;
            $model->update();
            if($model->update()){
                return true;
            }
        }
        return false;
    }


    public function addStatus($id){
        $model = GoodStatus::findOne(['goods_id'=>$id]);
        if($model){
            if (\Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->post());
                if ($model->save()) {
                    return true;
                }
            }
        }else{
            $this->load(Yii::$app->request->post());
            if($this->save()){
                return true;
            }
        }
        return false;
    }

    public function isHot(){
        return function ($data){
            if($data->is_hot == 1){
                return "是";
            }else{
                return "否";
            }
        };
    }
}