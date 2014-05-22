<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class Seo extends ActiveRecord{


    public static function tableName(){
        return 'seo_admin';
    }

    public function rules(){
        return [
            ['id','safe'],
            ['tdk_name','required'],
            ['title','required'],
            ['keywords','required'],
            ['description','required'],
            ['scenarios','required'],
        ];
    }
    public function attributelabels(){
         return [
             'tdk_name'=>'场景名称',
             'scenarios'=>'场景描述',
         ];
    }
    public function getAllTdkDatas(){
        $provider = new ActiveDataProvider([
              'query' => static::find()
                      ->orderby('id desc'),
            'sort' => [
                'attributes' => ['id'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
    }

    public function updateTdk($bonus_id){
        $post = static::findOne($bonus_id);
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
}