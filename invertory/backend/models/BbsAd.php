<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class BbsAd extends ActiveRecord{


    public static function tableName(){
        return 'bbs_ad';
    }

    public function rules(){
        return [
            ['ad_id','safe'],
            ['ad_image','required'],
            ['ad_url','required'],
            ['ad_order','required'],
            ['type','safe'],
        ];
    }

    public function attributelabels(){
         return [
             'ad_image'=>'广告图片',
             'ad_url'=>'图片地址url',
             'ad_order'=>'排序倒序',
         ];
    }

    public function getAllAdDatas($type=false){
        if(!$type){
            $type = 0;
        }
        $provider = new ActiveDataProvider([
            'query' => static::find()->where(['type'=>$type]),
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
    }

    public function getCount(){
        return static::find()->count();
    }

    
    public function updateAd($ad_id){
        $post = static::findOne($ad_id);
        if (!$post) {
            return false;
        }
        if (\Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            $post->load($params);
            if ($post->save($params)) {
                return true;
            }
        }
        return false;
    }


    public function showImage(){
        return function ($data){
            if($data->ad_image){
                return "<img width='100px' src='".Yii::$app->params['targetDomain'].$data->ad_image."' />";
            }
            return false;
        };
    }
}