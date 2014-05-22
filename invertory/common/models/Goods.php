<?php
namespace common\models;

use Yii;
use backend\models\CategoryBack;
use common\components\MallActiveRecord;
use common\components\SubPages;
use common\models\Attributes;
use yii\db\Query;

class Goods extends MallActiveRecord{

    public $limit=5;
    public $sub_pages=6;
    public $duration = 30000;
    public $count = 2;
    /**
     * [tableName description]
     * @return [type] [description]
     */
    public static function tableName()
    {
        return 'goods';
    }
    /**
     * [getView description]
     * @param  [type] $goods_id [description]
     * @return [type]           [description]
     */
    public function getView($goods_id){
        $goodsInfo = [];
        $info = static::find()->where("goods_id=$goods_id")->one();
        $attrs = Attributes::find()->where("goods_id=$goods_id")->all();
        $goodsInfo['info']=$info;
        $goodsInfo['attrs']=$attrs;
        return $goodsInfo;
    }
    /**
     * [getAllOrders description]
     * @param  [type] $goods_id [description]
     * @return [type]           [description]
     */
    public function getAllOrders($goods_id,$page){
        $cache_id = 'getAllOrders'.$goods_id.$page;
        $datas = false;//Yii::$app->cache->get($cache_id);
        if($datas==false){
            $query = static::find()
                ->select('o.*,oi.*')
                ->from(['order o','order_info oi'])
                ->where("o.pay_status=1 and oi.goods_id=$goods_id")
                ->limit($this->limit)
                ->offset(($page-1)*$this->limit);
            $command = $query->createCommand();
            $datas = $command->queryAll();
        }
        return $datas;
    }
    /**
     * [getAllComments description]
     * @param  [type] $goods_id [description]
     * @param  [type] $page     [description]
     * @return [type]           [description]
     */
    public function getAllComments($goods_id,$page){
        $cache_id = 'getAllComments'.$goods_id.$page;
        $datas = false;//Yii::$app->cache->get($cache_id);
        if($datas==false){
            $query = static::find()
                ->select('oi.*,oc.*')
                ->from(['order_info oi','order_comment oc'])
                ->where("oi.order_id=oc.order_id and oi.goods_id=$goods_id")
                ->limit($this->limit)
                ->offset(($page-1)*$this->limit);
            $command = $query->createCommand();
            $datas = $command->queryAll();
            // Yii::$app->cache->set($cache_id,$datas,$this->duration);
        }
        return $datas;
    }
    /**
     * [getAllDatas description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public function getAllDatas($page){
        $cache_id = 'getAllDatas'.$page;
        $datas = false;//Yii::$app->cache->get($cache_id);
        if($datas==false){
            $query = new Query;
            $datas = $query->select('g.*,gs.*')
                  ->from(['goods g','goods_status gs'])
                  ->where("g.goods_id=gs.goods_id and gs.is_on_sale=1 and gs.is_delete=0")
                ->orderBy('g.goods_id desc')
                ->limit($this->limit)
                ->offset(($page-1)*$this->limit)
                ->all(static::getDb());
            // Yii::$app->cache->set($cache_id,$datas,$this->duration);
        }
        return $datas;
    }
    
    public function getCondition($condition){
        $str = '';
        if(!empty($condition['cat_id'])){
            $str .= " and g.cat_id={$condition['cat_id']}";
        }elseif(!empty($condition['brand_id'])){
            $str .= " and g.brand_id={$condition['brand_id']}";
        }
        return $str;
    }
    /**
     * [getSubPages description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public function  getSubPages($page){
        $count = $this->limit;
        $sub_pages = $this->sub_pages;
        $nums = $this->getCount();
        $subPages = new SubPages($count,$nums,$page,$sub_pages,"/index/goods?p=",2);
        $p = $subPages->show_SubPages(2);
        return $p;
    }
    /**
     * [getCount description]
     * @return [type] [description]
     */
    public function getCount(){
        // $count = static::find()->count();
        $count = static::find()->select('g.*,gs.*')
                  ->from(['goods g','goods_status gs'])
                  ->where("g.goods_id=gs.goods_id and gs.is_on_sale=1 and gs.is_delete=0")
                ->count();
        return $count;
    }
    /**
     * [getCatIds description]
     * @return [type] [description]
     */
    public function getCatIds(){
        $model = new CategoryBack;
        $roots = $model->findOne(['root'=>0]);
        $descendants = $roots->descendants()->all();
        $rs = [];
        foreach ($descendants as $key => $value) {
            $rs[$value['level']][$value['id']] = $value['name'];
        }
        return $rs;
    }
    
    public function getCategoryName($id){
        $rs = Category::findOne($id);
        if($rs){
            return $rs->name;
        }
        return false;
    }

    //Brand new release of goods
    public function getNewgoodsByBrand($brand_id){
        $data = static::find()->select('g.*,gs.*')
                  ->from(['goods g','goods_status gs'])
                  ->where("g.goods_id=gs.goods_id and gs.is_on_sale=1 and gs.is_delete=0 and g.brand_id=$brand_id")
                  ->limit(4)
                  ->all();
          return $data;
    }

    public function getBrandAllGoods($brand_id,$page){
        $query = new Query;
        $datas = $query->select('g.*,gs.*')
                ->from(['goods g','goods_status gs'])
                ->where("g.goods_id=gs.goods_id and gs.is_on_sale=1 and gs.is_delete=0 and g.brand_id=$brand_id")
                ->orderBy('g.goods_id desc')
                ->limit($this->limit)
                ->offset(($page-1)*$this->limit)
                ->all(static::getDb());
        return $datas;
    }

    public function getHotCommentGoods($brand_id){
        
    }


    public function getDogIndexDatas(){
        $query = new Query;
        // $datas = $query->select('')
    }

}
