<?php
namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use backend\models\Ad;

class AdDetail extends AdBack{
    public static function tableName(){
        return "label_detail";
    }
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['detail_time','detail_update_time'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'detail_update_time',
                ],
            ],
        ];
    }
    public function rules(){
         return [
             ['label_id','safe'],
             ['label_id','required'],
             ['detail_picture','required'],
             ['detail_thumb','required'],
             ['detail_value','required'],
             ['detail_count','required'],
             ['detail_count','integer','message'=>'必须为整数！'],
             ['detail_order','required'],
             ['detail_order','integer','message'=>'必须为整数！'],
             ['detail_status','required'],
         ];
     }

     public function attributelabels(){
         return [
             'label_detail_id'=>'标签id',
             'detail_picture'=>'图片',
             'detail_thumb'=>'品牌小图',
             'detail_value'=>'商品id 或者品牌id',
             'detail_count'=>'点击人气',
             'detail_order'=>'排序顺序',
             'detail_status'=>'状态',
             'detail_time'=>'添加时间',
             'detail_update_time'=>'修改时间',
         ];
     }

     public function getAllData(){
         $provider = new ActiveDataProvider([
              'query' => static::find()
                     ->orderby('detail_count desc'),
            'sort' => [
                'attributes' => ['detail_count'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
     }

     public function addAdDetail($params){
         $model = new AdDetail;
         if($model->load($params) && $model->save()){
            return true;
        }else{
            print_r($model->getErrors());exit;
        }
        return false;

     }
     public function updateAdDetail($detail_id){
         $post = static::findOne($detail_id);
        if (!$post) {
            return false;
        }
        if (\Yii::$app->request->isPost) {
            $post->load(Yii::$app->request->post());
            if ($post->save()) {
                return true;
            }
        }
        return false;
     }

     public function deleteAdDetail($detail_id){
         $model = static::findOne($detail_id);
         $model->delete();
         if($model->delete()){
            return true;
        }
        return false;
     }

     public function showAdDetail($label_id){
         $provider = new ActiveDataProvider([
              'query' => static::find()
                      ->where(['label_id'=>$label_id])
                         ->orderby('label_detail_id desc'),
            'sort' => [
                'attributes' => ['label_detail_id'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
     }

     public function showImg(){
         return function ($data){
             if($data->detail_picture){
                 return "<img src='".Yii::$app->params['targetDomain'].$data->detail_picture."' width='50px' />";
             }
         };
     }
}

