<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\MallActiveRecord;
use common\components\SubPages;

 class Brand extends MallActiveRecord{
     public $limit=8;
    public $sub_pages=6;
    public $duration = 30000;

     public static function tableName(){
         return "brand";
     }
     /**
      * [getAllBrands description]
      * @param  [type]  $page      [description]
      * @param  boolean $condition [description]
      * @return [type]             [description]
      */
     public function getAllBrands($page,$condition=false){
        $str = "";
        if(!empty($condition)){
            if(!empty($condition['keyword'])){
                $str = "brand_name like like '%{$condition['keyword']}%'";
            }
            if(!empty($condition['word'])){
                $str = "brand_word='{$condition['word']}'";
            }
        }
        $query = static::find()
                ->andWhere(["is_show"=>!0,"is_delete"=>!1])
                // ->andWhere("show_nav=1 and is_delete=0")
                ->andWhere($str)
                ->limit($this->limit)
                ->offset(($page-1)*$this->limit);
        $command = $query->createCommand();
        $datas = $command->queryAll();
        return $datas;
     }

     public function findBrand($brand_id){
         return static::find($brand_id);
     }

     public function getBrandInfo($brand_id){
         return static::findOne($brand_id);
     }

     public function getBrandGoods($brand_id,$page){
         $cache_id = 'getBrandGoods'.$page.$brand_id;
        $datas = false;//Yii::$app->cache->get($cache_id);
        if($datas==false){
            $query = static::find()
                ->select('g.*,gs.*')
                  ->from(['goods g','goods_status gs'])
                  ->where("g.goods_id=gs.goods_id and gs.is_on_sale=1 and gs.is_delete=0 and brand_id=$brand_id")
                ->orderBy('g.goods_id desc')
                ->limit($this->limit)
                ->offset(($page-1)*$this->limit);
            $command = $query->createCommand();
            $datas = $command->queryAll();
            Yii::$app->cache->set($cache_id,$datas,$this->duration);
        }
        return $datas;
     }

     public function getBrandName($brand_id){
         $rs = Brand::find($brand_id);
         return $rs['brand_name'];
     }

     public function  getSubPages($page){
        $count = $this->limit;
        $sub_pages = $this->sub_pages;
        $nums = $this->getCount();
        $subPages = new SubPages($count,$nums,$page,$sub_pages,"/index/brands?p=",2);
        $p = $subPages->show_SubPages(2);
        return [$nums,$p];
    }


    public function getCount(){
        $count = static::find()
                ->where("is_show=1 and is_delete=0")
                ->count();
        return $count;
    }

    public function getBrandsByCatid($catid){
        $datas = static::find()->where("cat_id=$catid")->all();
        return $datas;
    }
     
 }