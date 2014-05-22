<?php
namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use common\models\Ad;

class AdBack extends Ad{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['label_time','label_update_time'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'label_update_time',
                ],
            ],
        ];
    }
    public function rules(){
         return [
             ['label_id','safe'],
             ['label_type','required'],
             ['label_name','required'],
             ['label_order','required'],
             ['label_order','integer','message'=>'必须为整数！'],
             ['label_status','required'],
         ];
     }

     public function attributelabels(){
         return [
             'label_id'=>'标签id',
             'label_type'=>'广告标签类型',
             'label_name'=>'广告标签名称',
             'label_order'=>'排序顺序',
             'label_status'=>'广告标签状态',
             'label_time'=>'添加时间',
             'label_update_time'=>'修改时间',
         ];
     }
     public function addAdLabel($params){
         $model = new AdBack;
         if($model->load($params) && $model->save()){
            return true;
        }
        return false;
     }
     public function updateAdLabel($label_id){
         $post = static::findOne($label_id);
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

     public function deleteAdLabel($label_id){
         $model = static::findOne($label_id);
         $model->delete();
         $result = AdDetail::deleteAll(['label_id'=>$label_id]);
         if($model->delete() && $result){
            return true;
        }
        return false;
     }

     public function getAllData(){
         $provider = new ActiveDataProvider([
              'query' => static::find()
                     ->orderby('label_id desc'),
            'sort' => [
                'attributes' => ['label_id'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
     }
     public function getStatus(){
         return function($data){
             if($data->label_status == 0){
                return "<a href='/ad/check?id=$data->label_id&act=show'>显示</a>";
            }else{
                return "<a href='/ad/check?id=$data->label_id&act=hide'>隐藏</a>";
            }
         };
    }

    public function showStatus(){
        return function ($data){
            if($data->label_status == 1){
                return "显示";
            }
            return "隐藏";
        };
    }

    public function getChangeStatus($label_id,$act){
        $model = static::findOne($label_id);
        if($act == 'show'){
            $model->label_status = 1;
        }else{
            $model->label_status = 0;
        }
        $model->update();
    }
    public function getAdType(){
        return function($data){
            if($data->label_type == 1){
                return "汪星人";
            }elseif($data->label_type ==2){
                return "喵星人";
            }else{
                return "品牌";
            }
        };
    }
     
}

