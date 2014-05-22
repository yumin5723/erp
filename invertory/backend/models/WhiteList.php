<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class WhiteList extends ActiveRecord{


    public static function tableName(){
        return 'white_list';
    }

    public function rules(){
        return [
            ['id','safe'],
            ['keyword','required'],
        ];
    }

    public function attributeLabels(){
        return [
            'keyword'=>'关键词'
        ];
    }

    public function getAllDatas(){
        $provider = new ActiveDataProvider([
            'query' => static::find(),
            'sort' => [
                'attributes' => ['id'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
    }

    public function addKeyword($params){
        $model = new BbsAd;
        if($model->load($params) && $model->save()){
            return true;
        }
        return false;
    }

    
    public function updateKeyword($id){
        $post = static::findOne($id);
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

    public function deleteKeyword($id){
        $model = static::findOne($id);
        $model->delete();
        if($model->delete()){
            return true;
        }
        return false;
    }
}