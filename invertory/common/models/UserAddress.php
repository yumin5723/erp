<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\MallActiveRecord;
use common\components\SubPages;
use yii\db\Query;

 class UserAddress extends MallActiveRecord{

     public static function tableName(){
         return "user_address_book";
     }

     public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['book_date'],
                ],
            ],
        ];
    }

    public function rules(){
        return [
            ['book_name','required'],
            ['book_uid','required'],
            ['book_name','required'],
            ['book_province','required'],
            ['book_city','required'],
            ['book_area','required'],
            ['book_address','required'],
            ['book_zip','required'],
            ['book_phone','safe'],
            ['book_status','safe'],
        ];
    }



     public function getAllAddressDatas(){
         return static::find()->all();
     }

     public function addAddress($params){
         print_r($params);
         if($this->load($params) && $this->save()){
            return true;
        }else{
            print_r($this->getErrors());exit;
        }
        return false;
     }

     public function updateAddress($id){
        $post = static::findOne($id);
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

    public function getProvinces(){
        $query = new Query;
        $provinces = $query->from("city")->where("pid=0 and id!=1")->all(static::getDb());
        $rs = [];
        if($provinces){
            foreach ($provinces as $value) {
                $rs['0'] = "请选择省市/其他...";
                $rs[$value['id']] = $value['name'];
            }
        }
        return $rs;
    }

    public function getCities($pid){
        $query = new Query;
        $condition = "pid not in(2,3,4,5) and pid=$pid";
        if(in_array($pid,[2,3,4,5])){
            $condition = "id=$pid";
        }
        $cities = $query->from("city")->where($condition)->all(static::getDb());
        $rs = [];
        if($cities){
            foreach ($cities as $key=>$value) {
                $rs[$key]['name'] = $value['name'];
                $rs[$key]['id'] = $value['id'];
            }
        }
        return $rs;
    }

    public function getAreas($pid){
        $query = new Query;
        $areas = $query->from("city")->where("pid in(2,3,4,5) and pid=$pid")->all(static::getDb());
        $rs = [];
        if($areas){
            foreach ($areas as $value) {
                $rs[$value['id']] = $value['name'];
            }
        }
        return $rs;
    }
 }