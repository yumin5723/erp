<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class LinkAdmin extends ActiveRecord{


    public static function tableName(){
        return 'link_admin';
    }

    public function rules(){
        return [
            ['id','safe'],
            ['name','required'],
        ];
    }

    public function attributeLabels(){
        return [
            'name'=>'场景名称',
        ];
    }
    public function getAllDatas(){
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

    public function updateLinkBlock($id){
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


    public function deleteLinkBlock($id){
        $model = static::findOne($id);
        if($model->delete()){
            $result = FriendLink::deleteAll(['link_type'=>$id]);
            if($result){
                return true;
            }
        }
        return false;
    }
}