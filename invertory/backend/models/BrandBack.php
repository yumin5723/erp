<?php
namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\Brand;

class BrandBack extends Brand{

     public function rules(){
         return [
             ['brand_id','safe'],
             ['cat_id','integer','message'=>'必须为整数！'],
             ['cat_id','required'],
             // ['brand_name','string','length'=>[4,32]],
             // ['brand_name','string','max'=>32],
             ['brand_name','required'],
             ['brand_logo','required'],
             ['brand_thumbs','required'],
             ['brand_desc','required'],
             ['brand_url','required'],
             ['brand_word','required'],
             ['brand_word','match','pattern'=>'/^[A-Z]{1}$/','message'=>'必须为品牌的大写首字母'],
             ['show_nav','required'],
             ['is_show','required'],
         ];
     }


     public function attributelabels(){
         return [
             'brand_id'=>'品牌ID',
             'cat_id'=>'分类ID',
             'brand_name'=>'名称',
             'brand_logo'=>'LOGO',
             'brand_thumbs'=>'小图',
             'brand_desc'=>'介绍',
             'brand_url'=>'链接',
             'brand_word'=>'首字母',
             'show_nav'=>'是否显示在导航栏',
             'is_show'=>'是否显示',
         ];
     }


     public function addBrand($params){
         if($this->load($params) && $this->save()){
            return true;
        }
        return false;
     }


     public function updateBrand($id){
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


     public function deleteBrand($id){
         $model = static::findOne($id);
         $model->is_delete = 1;
         $model->update();
         if($model->update()){
            return true;
        }
        return false;
     }

     public function getAllData(){
         $str1 = $str2 = $str3 = $str4 = $str5 = $str6='';
        if(!empty($_GET['Brand']['brand_id'])){
            $str1 = " brand_id='{$_GET['Brand']['brand_id']}'";
        }
        if(!empty($_GET['Brand']['brand_name'])){
            $str2 = " brand_name like '%{$_GET['Brand']['brand_name']}%'";
        }
         $provider = new ActiveDataProvider([
              'query' => static::find()
                      ->andWhere("is_delete=0")
                      ->andWhere($str1)
                      ->andWhere($str2)
                     ->orderby('brand_id desc'),
            'sort' => [
                'attributes' => ['brand_id'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
     }

     public function getBrands(){
         $rs = static::find()->where("is_delete=0")->all();
         $arr = [];
         if($rs){
             foreach($rs as $key=>$v){
                 $arr[$v['brand_id']]=$v['brand_name'];
             }

         }
         return $arr;
     }

     public function logoUrl(){
         return function ($data){
             if($data->brand_logo){
                 return "<img width='100px' src='".Yii::$app->params['targetDomain'].$data->brand_logo."' />";
             }
             return false;
         };
     }
     
 }