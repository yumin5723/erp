<?php
namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use backend\models\ExpressBack;
use common\models\City;
use yii\db\Query;
class ExpressArea extends ExpressBack{

    // public $id;
    // public $pid;
    // public $name;
    // public $area_id;


    public static function tableName(){
        return "shipping_config";
    }

    public function rules(){
        return [
            ['area_id','required'],
            ['shipping_id','required'],
            ['first_weight','required'],
            ['first_weight_price','required'],
            ['continued_weight','required'],
            ['continued_weight_price','required'],
            ['no_first_weight','required'],
            ['basic_fee','required'],
            ['no_shipping_price','required'],

        ];
    }

    public function getAllConfigData($shipping_id){
        $provider = new ActiveDataProvider([
              'query' => static::find()
                      ->where(["shipping_id"=>$shipping_id])
                     ->orderby('shipping_id desc'),
            'sort' => [
                'attributes' => ['shipping_id'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
    }

    public function getAllCity(){
        $query = new Query;
        $rs = $query->select("id,name,pid")->from('city')->all(static::getDb());
        $area = [];
        $result = [];
        foreach($rs as $k=>$v){
            if ($v["pid"] == 0) {
                $area[$k]['id'] = $v["id"];
                $area[$k]['name'] = $v["name"];
                if($v['id']<2){
                    $result[$v['id']]["city"][$v["id"]] = $v['name'];
                }
            }
        }
        foreach ($area as $key => $value) {
            foreach ($rs as $k => $v) {
                $result[$value["id"]]["id"] = $value["id"];
                $result[$value["id"]]["name"] = $value["name"];
                if ($value["id"] == $v["pid"]) {
                    $result[$value["id"]]["city"][$v["id"]] = $v["name"];
                }
            }
        }
        return $result;
    }


    public function attributeLabels(){
        return [
            'shipping_id'=>'运费模板Id',
            'area_id'=>'地区',
            'ifdefault'=>'是否为默认模板',
            'first_weight'=>'首重',
            'first_weight_price'=>'首重价格',
            'continued_weight'=>'续重',
            'continued_weight_price'=>'续重价格',
            'no_first_weight'=>'是否免首重',
            'basic_fee'=>'快递最低费用',
            'no_shipping_price'=>'免邮额度',
        ];
    }


    public function addExpArea($params){
        $result = $this->checkCityId($params['ExpressArea']['shipping_id'],$params['ExpressArea']['area_id']);
        if(!$result){
            if($this->load($params) && $this->save()){
                return false;
            }
        }
        return $result;
    }


    public function updateExpDetail($config_id){
        $post = static::findOne($config_id);
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


    public function deleteExpDetail($config_id){
         $model = static::findOne($config_id);
         $model->delete();
         if($model->delete()){
            return true;
        }
        return false;
     }


    public function showCity($area_id){
        $city = '';
        $datas = City::find()->where("id in ($area_id)")->all();
        foreach ($datas as $key => $value) {
            $city .=$value['name'].','; 
        }
        return trim($city,',');
    }


     /**
      * [checkCityId description]
      * @param  [type] $shipping_id [description]
      * @param  [type] $cities      [description]
      * @return [type]              [description]
      */
    public function checkCityId($shipping_id,$cities){
        $area_id = '';
        $datas = static::find()->select('area_id')->where("shipping_id=$shipping_id")->all();
        if($datas){
            foreach ($datas as $key => $value) {
                $area_id .=$value['area_id'].',';
            }
            $area_id = trim($area_id,',');
            $areas = explode(',', $area_id);
            $city = explode(',', $cities);
            $mnus = array_intersect($areas,$city);
            if(!empty($mnus)){
                $cityId = implode($mnus,',');
                $city = $this->showCity($cityId);
                return $city;
            }
        }
        return false;
    }


    public function getCityName(){
        return function ($data){
         $city = '';
         $datas = City::find()->where("id in ($data->area_id)")->all();
         foreach ($datas as $key => $value) {
             $city .=$value['name'].','; 
         }
         return trim($city,',');
        };
    }

    
    /**
     * 返回快递免邮状态
     * @return callable
     */
    public function noFirstWeightStatus()
    {
        return function($data){
          if($data->no_first_weight === 1){
              return '是';
          }else{
              return '否';
          }
        };
    }
}