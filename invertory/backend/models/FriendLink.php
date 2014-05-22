<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class FriendLink extends ActiveRecord{


    public static function tableName(){
        return 'link_list';
    }

    public function rules(){
        return [
            ['link_id','safe'],
            ['link_text','required'],
            ['link_url','required'],
            ['link_type','required'],
        ];
    }

    public function attributelabels(){
        return [
            'link_text'=>'链接文字',
            'link_url'=>'链接地址url',
        ];
    }

    public function getAllLinkDatas($link_type){
        $provider = new ActiveDataProvider([
            'query' => static::find()->where(['link_type'=>$link_type])
                      ->orderby('link_id desc'),
            'sort' => [
                'attributes' => ['link_id'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
    }

    public function addLinks($params){
        $model = new FriendLink;
        if($model->load($params) && $model->save()){
            return true;
        }
        return false;
    }

    
    public function updateFriendLink($link_id){
        $post = static::findOne($link_id);
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

    public function deleteFriendLink($link_id){
        $model = static::findOne($link_id);
        $model->delete();
        if($model->delete()){
            return true;
        }
        return false;
    }
}