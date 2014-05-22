<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use common\models\Bonus;

class BonusBack extends Bonus{


    public static function tableName(){
        return 'bonus_setting';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['bonus_date', 'bonus_date'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'bonus_date',
                ],
            ],
        ];
    }
    public function rules(){
        return [
            ['bonus_id','safe'],
            ['cp_id','required'],
            ['bonus_name','required'],
            ['start_date','required'],
            ['end_date','required'],
            ['bonus_threshold','required'],
            ['bonus_value','required'],
        ];
    }

    public function getAllData(){
        $str1 = $str2 = $str3 ='';
        if(!empty($_GET['BonusBack']['bonus_id'])){
            $str1 = " bonus_id='{$_GET['BonusBack']['bonus_id']}'";
        }
        if(!empty($_GET['BonusBack']['cp_id'])){
            $str2 = " cp_id='{$_GET['BonusBack']['cp_id']}'";
        }
        if(!empty($_GET['BonusBack']['bonus_name'])){
            $str3 = " bonus_name like '%{$_GET['BonusBack']['bonus_name']}%'";
        }
        $provider = new ActiveDataProvider([
              'query' => static::find()
                      ->andWhere($str1)
                      ->andWhere($str2)
                      ->andWhere($str3)
                      ->orderby('bonus_id desc'),
            'sort' => [
                'attributes' => ['bonus_id'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
    }

    public function attributelabels(){
         return [
             'bonus_id'=>'ID',
             'cp_id'=>'商家id',
             'bonus_name'=>'红包名称',
             'start_date'=>'开始时间',
             'end_date'=>'结束时间',
             'bonus_threshold'=>'使用条件',
             'bonus_value'=>'优惠金额',
             'bonus_date'=>'添加时间',
         ];
     }

     public function addBonus($params){
         if(isset($params['BonusBack']['start_date']) && isset($params['BonusBack']['end_date'])){
             $params['BonusBack']['start_date'] = strtotime($params['BonusBack']['start_date']);
             $params['BonusBack']['end_date'] = strtotime($params['BonusBack']['end_date']);
         }
         // $params['BonusBack']['cp_id'] = Yii::$app->user->id;
         if($this->load($params) && $this->save()){
            return true;
        }
        return false;
     }

     public function updateBonus($bonus_id){
         $post = static::findOne($bonus_id);
        if (!$post) {
            return false;
        }
        if (\Yii::$app->request->isPost) {
            if(isset($_POST['BonusBack']['start_date']) && isset($_POST['BonusBack']['end_date'])){
                 $_POST['BonusBack']['start_date'] = strtotime($_POST['BonusBack']['start_date']);
                 $_POST['BonusBack']['end_date'] = strtotime($_POST['BonusBack']['end_date']);
             }
             // $params['BonusBack']['cp_id'] = Yii::$app->user->id;
            $post->load($_POST);
            if ($post->save()) {
                return true;
            }
        }
        return false;
     }

     public function deleteBonus($id){
         $model = static::findOne($id);
         $model->delete();
         if($model->delete()){
            return true;
        }
        return false;
     }
}