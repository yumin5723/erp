<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use common\models\Category;
use yii\data\ActiveDataProvider;
use common\models\Goods;
use backend\models\GoodStatus;
use backend\models\AttributesBack;
use backend\models\BrandBack;
use backend\models\ExpressBack;
use backend\models\CategoryBack;


class GoodsBack extends Goods
{
    // public $is_real=1;
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['add_time'],
                    // ActiveRecord::EVENT_BEFORE_UPDATE => 'last_update',
                ],
            ],
            // 'relations'=>'backend\models\GoodsBack',
        ];
    }
    public function rules(){
        return [
            ['goods_id','safe'],
            ['cp_id','integer','message'=>'必须为整数！'],
            ['goods_name','required'],
            ['brand_id','integer','message'=>'必须为整数！'],
            ['cat_id','integer','message'=>'必须为整数！'],
            ['type_id','integer','message'=>'必须为整数！'],
            ['goods_brief','required'],
            ['goods_desc','safe'],
            ['goods_img','safe'],
            ['goods_thumb','safe'],
            ['is_real','required'],
            ['shipping_type','required'],
            ['shipping_type_parameter','safe'],
            ['integral','required'],
            ['give_integral','required'],
            ['depot','required'],
        ];
    }
    // public function relations(){
    //     return [
    //         'goods_status'=>'GoodStatus',
    //     ];
    // }
    
    /**
     * [addGoods description]
     * @param [type] $params [description]
     */
    public function addGoods($params){
        if($params['GoodsBack']['shipping_type']==1){
            $params['GoodsBack']['shipping_type_parameter']=0;
        }
        // $params['GoodsBack']['cp_id'] = Yii::$app->user->id;
        if($this->load($params) && $this->save()){
            return true;
        }
        return false;
    }
    /**
     * [updateGoods description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function updateGoods($id){
        $post = static::findOne($id);
        if (!$post) {
            return false;
        }
        if (\Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            // $params['GoodsBack']['cp_id'] = Yii::$app->user->id;
            if($params['GoodsBack']['shipping_type']==1){
                $params['GoodsBack']['shipping_type_parameter']=0;
            }
            $post->load($params);
            if ($post->save()) {
                return true;
            }
        }
        return false;
    }


    public function deleteGoods($goods_id){
        $model = GoodStatus::findOne(['goods_id'=>$goods_id]);
        if($model){
            $model->is_delete = 1;
            $model->update();
            if($model->update()){
                return true;
            }
        }
        return false;
    }


    public function getAllData(){
        $str = $cstr = $dstr = $job = '';
        if(!empty($_GET['Goods']['goods_id'])){
            $str = " goods_id='{$_GET['Goods']['goods_id']}'";
        }
        if(!empty($_GET['Goods']['goods_name'])){
            $cstr = " goods_name like '%{$_GET['Goods']['goods_name']}%'";
        }
        if(!empty($_GET['Goods']['cat_id'])){
            $dstr = " cat_id='{$_GET['Goods']['cat_id']}'";
        }
        $query = GoodsBack::find()
                      ->andWhere($str)
                      ->andWhere($cstr)
                      ->andWhere($dstr)
                      ->orderby('goods_id desc');
        // print_r($query);exit;
        $provider = new ActiveDataProvider([
              'query' => $query,
            'sort' => [
                'attributes' => ['goods_id','cp_id'],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $provider;
    }

    public function search($params){
        $query = GoodsBack::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['goods_id' => $this->goods_id])
              ->andFilterWhere(['cat_id' => $this->cat_id]);
        $query->andFilterWhere(['like', 'goods_name', $this->goods_name]);

        return $dataProvider;
    }

    public function getOnSale(){
        $qs = static::find()
                ->select('g.*,gs.*')
                ->from(['goods g','goods_status gs'])
                ->where("g.goods_id=gs.goods_id and gs")
                ->orderby("g.goods_id desc");
        $provider = new ActiveDataProvider([
              'query' => $qs,
            'sort' => [
                'attributes' => ['g.goods_id'],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $provider;
    }


    public function getAllDatass(){
        $qs = static::find()
                ->select('g.*,gs.*')
                ->from(['goods g','goods_status gs'])
                ->where("g.goods_id=gs.goods_id")
                ->orderby("g.goods_id desc");
        // $qs = static::find()->with('goods_status')->all();
        $provider = new ActiveDataProvider([
              'query' => $qs,
            'sort' => [
                'attributes' => ['g.goods_id'],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $provider;
    }


    public function getBrands(){
        $model = new BrandBack;
        return $model->getBrands();
    }


    public function isSale(){
        $model = GoodStatus::find(['goods_id'=>$goods_id]);
        if($model){
            $model->is_on_sale = 0;
            $model->update();
            if($model->update()){
                return true;
            }
        }
        return false;
    }


    public function showOne($goods_id){
        $query = static::find()->where("goods_id=$goods_id");
        $rs=[];
        foreach ($query->batch() as $users) {
            $rs = $users[0];
        }
        return $rs;
    }
    
    /**
     * [getCategoryName description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getCatName($id){
        $rs = Category::findOne($id);
        if($rs){
            return $rs['name'];
        }
        return false;
    }


    public function getBrand($brand_id){
        $rs = BrandBack::findOne($brand_id);
        if($rs){
            return $rs['brand_name'];
        }
        return false;
    }
    /**
     * [getAttributeData description]
     * @param  [type] $goods_id [description]
     * @return [type]           [description]
     */
    public function getAttributeData($goods_id){
        $datas = Attributes::find()->where(['goods_id'=>$goods_id])->all();
        return $datas;
    }
    /**
     * [createAttribute description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function createAttribute($data){
        $model = new Attributes;
        if($model->load($_POST) && $model->save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * [alterAttribute description]
     * @param  [type] $id   [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function alterAttribute($id,$data){
        $model = new Attributes;
        $model = $model->loadModel($id);
        if (!empty($data)) {
            if ($model->updateAttrs($data['Attributes'])) {
                return true;
            }
        }
        return false;
    }
    /**
     * [delAttribute description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delAttribute($id){
        $model = Attributes::find($id);
        $model->delete();
        if($model->delete()){
            return true;
        }    
        return false;
    }
    /**
     * [getPosttype description]
     * @return [type] [description]
     */
    public function getPosttype(){
        if($this->shipping_type == 1){
            return "包邮";
        }elseif($this->shipping_type == 2){
            return "不包邮";
        }elseif($this->shipping_type == 3){
            return "满金额包";
        }elseif($this->shipping_type == 4){
            return "满数量包";
        }
    }
    /**
     * [getStatus description]
     * @return [type] [description]
     */
    public function getStatus(){
        if($this->goods_status == 1){
            return "显示";
        }else{
            return "隐藏";
        }
    }
    public function attributeLabels(){
        return [
            'goods_id'=>'商品ID',
            'cp_id'=>'商家',
            'goods_name'=>'商品名称',
            'cat_id'=>'商品分类',
            'brand_id'=>'商品品牌',
            'type_id' => '商品类型',
            'goods_brief'=>'商品介绍',
            'goods_desc'=>'商品详细介绍',
            'goods_img'=>'大图',
            'goods_thumb'=>'小图',
            'is_real'=>'是否实物',
            'shipping_type'=>'邮费类型',
            'shipping_type_parameter'=>'邮费参数',
            'integral'=>'使用积分',
            'give_integral'=>'赠送积分',
            'add_time'=>'添加时间',
            'last_update'=>'修改时间',
            'depot'=>'仓库',
        ];
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = static::findOne(['goods_id'=>$id]);
        if ($model === null) { return false;}
        return $model;
    }


    public function getExpressTpl(){
        $cp_id = Yii::$app->user->id;
        $result = ExpressBack::find()->where("cp_id=$cp_id and shipping_status=1")->orderby('ifdefault desc')->all();
        $data = '';
        if($result){
            foreach ($result as $k => $v) {
                $data[$v['shipping_id']] = $v['shipping_name'];
            }
        }
        return $data;
    }


    public function getExpressName(){
        return function($data){
            $rs = ExpressBack::findOne($data->shipping_type_parameter);
            if($rs){
                return $rs->shipping_name;
            }
            return false;
        };
    }


    public function getActivity(){
        return [
            '1'=>'city',
            '2'=>'mall',
            '3'=>'shop',
        ];
    }


    public function getExpType(){
        return function ($data){
            if($data->shipping_type == 1){
                return "包邮";
            }elseif($data->shipping_type == 2){
                return "不包邮";
            }elseif($data->shipping_type == 3){
                return "满金额包";
            }else{
                return "满数量包";
            }
        };
    }


    public function getBrandName(){
        return function ($data){
            $rs = BrandBack::findOne($data->brand_id);
            if($rs){
                return $rs->brand_name;
            }
            return false;
        };
    }


    public function getGoodImg($goods_img){
            if($goods_img){
                return "<img src='".Yii::$app->params['targetDomain'].$goods_img."'/>";
            }return false;
    }


    public  function showImg(){
        return function ($data){
            if($data->goods_img){
                return "<img width='100px' src='".Yii::$app->params['targetDomain'].$data->goods_img."'/>";
            }return false;
        };
    }
}