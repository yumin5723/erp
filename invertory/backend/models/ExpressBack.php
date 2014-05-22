<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use common\models\Express;

class ExpressBack extends Express{
    public static function tableName(){
        return "shipping";
    }
    public function rules(){
        return [
             ['shipping_id','safe'],
             ['cp_id','integer','message'=>'必须为整数！'],
             ['shipping_code','required'],
             ['shipping_name','required'],
             ['shipping_desc','required'],
             ['shipping_status','required'],
             ['address','required'],
             ['ifdefault','required'],
         ];
    }//ALTER TABLE `shipping_config` CHANGE `area_id` `area_id` VARCHAR( 255 ) NOT NULL COMMENT '地区表id'
    //ALTER TABLE `qb_purse` ADD `ifdefault` TINYINT( 1 ) NOT NULL DEFAULT '0'
    public function addExpress($params){
        // $params['ExpressBack']['cp_id'] = Yii::$app->user->id;
        if($params['ExpressBack']['ifdefault']=='1'){
            ExpressBack::updateAll(['ifdefault'=>0],"ifdefault=1 and cp_id={$params['ExpressBack']['cp_id']}");
            if($this->load($params) && $this->save()){
                return true;
            }
        }else{
            if($this->load($params) && $this->save()){
                return true;
            }
        }
        return false;
    }

    public function updateExpress($id){
         $post = static::findOne($id);
        if (!$post) {
            return false;
        }
        if (\Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            // $params['ExpressBack']['cp_id'] = Yii::$app->user->id;
            if($params['ExpressBack']['ifdefault']=='1'){
                ExpressBack::updateAll(['ifdefault'=>0],"ifdefault=1 and cp_id={$params['ExpressBack']['cp_id']}");
            }
            $post->load($params);
            if ($post->save()) {
                return true;
            }
        }
        return false;
     }
     
    public function getAllData(){
        $provider = new ActiveDataProvider([
              'query' => static::find()
                      ->where("shipping_status != 2")
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
    public function attributeLabels(){
        return [
            'shipping_name'=>'配送模板名称',
            'cp_id'=>'商家id',
            'ifdefault'=>'是否为默认模板',
            'shipping_desc'=>'模板描述',
            'shipping_status'=>'配送模板状态',
            'shipping_code'=>'字符代码',
            'address'=>'商家发货地',
        ];
    }

    public function tplStatus(){
        return function ($data){
            if($data->shipping_status == 1){
                return "开启";
            }elseif($data->shipping_status == 2){
                return "删除";
            }else{
                return "关闭";
            }
        };
    }

    public function change($id,$act){
        $model = ExpressBack::findOne($id);
        if($act == 'close'){
            $model->shipping_status = 0;
        }elseif($act == 'start'){
            $model->shipping_status = 1;
        }else{
            $model->shipping_status = 2;
        }
        $model->update();
    }

    public function deleteExpress($id){
        $model = ExpressBack::findOne($id);
        $model->shipping_status = 2;
        $model->update();
    }
    
    public function changeStatus(){
        return function ($data){
            if($data->shipping_status == 1){
                return "<a href='/express/change?id=$data->shipping_id&act=close'>关闭</a>&nbsp;&nbsp;//&nbsp;&nbsp;<a href='/express/change?id=$data->shipping_id&act=del' class='delete'>删除</a>";
            }elseif($data->shipping_status == 0){
                return "<a href='/express/change?id=$data->shipping_id&act=start'>开启</a>&nbsp;&nbsp;//&nbsp;&nbsp;<a href='/express/change?id=$data->shipping_id&act=del' class='delete'>删除</a>";
            }
            // return "<a href='/express/change?act=start'></a><a href='/express/change?act=del'></a>";
        };
    }
}