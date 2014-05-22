<?php
/**
 * user: liding
 * date: 14-4-24
 */

namespace common\models\db;

use Yii;
use gcommon\cms\models\Db;
use yii\db\Query;
use gcommon\cms\models\editor\NewPhotos;

class Mall extends Db{

    protected $_db_type = "mall";
    public $mall1GoodsNum = 3; //团购获取数量
    public $hotGoodsNum = 3; //商城获取数量
    public $pingNum = 12; //点评获取数量


    public function getDbType() {
        return $this->_db_type;
    }

    public static function getDb(){
        return \Yii::$app->get("dogdb");
    }

    /**
     * 获取团购，商城，点评数据
     * @param null $params
     * @return array
     * [
     *  chooseGoods 团购爆款数据
     *  mall1Goods 团购数据
     *  hotGoods 商城排序前3数据
     *  pingGoods 点评数据
     * ]
     */
    public function getData($params = null)
    {
        $data =[];
        $chooseGoods =  $this->getChooseGoods();
        if($chooseGoods){
            $data['chooseGoods'] = $chooseGoods;
        }else{
            $data['chooseGoods'] = [];
        }

        $mall1Goods = $this->getMall1Goods();
        if($mall1Goods){
            $data['mall1Goods'] = $mall1Goods;
        }else{
            $data['mall1Goods'] = [];
        }

        $hotGoods = $this->getHotGoods();
        if($hotGoods){
            $data['hotGoods'] = $hotGoods;
        }else{
            $data['hotGoods'] = [];
        }

        $pingGoods = $this->getPingGoods();
        if($pingGoods){
            $data['pingGoods'] = $pingGoods;
        }else{
            $data['pingGoods'] = [];
        }

        return $data;
    }


    /**
     * 获取团购爆款商品
     * @return array
     * [
     *  id 商品ID
     *  name 商品名称
     *  price 商品销售价格
     *  mprice 商品市场价格
     *  gross_sold 商品销售数量
     *  img_url 商品图片
     * ]
     */
    private function getChooseGoods()
    {
        $time = time();
        $query = $this->getQuery();
        $result = $query
                ->select(['mall_gds_id id' , 'mall_name name' , 'p_price_min price' ,'p_msrp_min mprice','mall_sold sold', 'mall_cover_udate atime'])
                ->from(['dog_mall'])
                ->where(['recommended' => 1,'mall_status' => 1, 'mall_display' => 1])
                ->andWhere("mall_expire > $time")
                ->orderBy('mall_cover_udate DESC')
                ->all(self::getDb());
        if($result){
            $data = $this->_after_select($result);
            $data = $this->getUrl($data,'mall1');
        }
        return $data;
    }


    /**
     * 获取团购商品
     * @return array
     * [
     *  id 商品ID
     *  name 商品名称
     *  price 商品销售价格
     *  mprice 商品市场价格
     *  img_url 商品图片
     * ]
     */
    private function getMall1Goods()
    {
        $time =time();
        $query = $this->getQuery();
        $result = $query
                ->select( "mall_gds_id id , mall_name name , p_price_min price , p_msrp_min mprice ,  mall_cover_udate atime")
                ->from(['dog_mall'])
                ->Where("mall_status > 0  AND mall_expire > $time")
                ->andwhere(['mall_display'=>1 , 'mall_jingbao'=>0 ])
                ->orderBy('mall_cover_udate DESC')
                ->limit($this->mall1GoodsNum)
                ->all(self::getDb());
        if($result){
            $data = $this->getUrl($result,'mall1');
            $data = $this->_after_select($data);
            return $data;
        }
        return $result;
    }


    /**
     * 获取商城最近30天销售量最高的3个商品
     * 获取热销商品
     * @return array
     * [
     *  id 商品ID
     *  name 商品名称
     *  price 商品价格
     *  img_url 商品图片
     * ]
     */
    private function getHotGoods()
    {

        $query = $this->getQuery();
        $result = $query
                ->select(['od.od_gid AS id'  , 'SUM(od.od_qty) AS all_qty'])
                ->from('dog_order2 o')
                ->innerJoin('dog_order2_detail od','o.o_id=od.od_oid')
                ->innerJoin('dog_mall2_stock_snapshot s' , 's.ss_id = od.od_gid')
                ->where('od. od_status = 3 AND s.ss_qty > 0 AND od.od_gid > 0 AND o.o_cdate>UNIX_TIMESTAMP()-30*86400 ')
                ->groupBy('od.od_gid')
                ->orderBy('all_qty DESC')
                ->limit($this->hotGoodsNum)
                ->all(self::getDb());
        if($result){
            foreach($result as $value){
                $ids[] = $value['id'];
            }
            $q = new Query();
            $data = $q
                ->select(['id','name','p_price_min','p_pics'])
                ->from('dog_mall2_item')
                ->where(['id'=>$ids])
                ->all(self::getDb());
            if($data){
                foreach($data as $k=>$val){
                    $pic = explode(',', $val['p_pics']);
                    $data[$k]['img_url'] = $pic[0];
                    unset($data[$k]['p_pics']);
                }
            }
            return $data;
        }
        return $result;
    }


    /**
     *
     * 获取人气商品
     */
    /**
     * @return array
     * [
     *  id 点评ID
     *  name 点评标题
     *  quality 质量
     *  satisfy 效果
     *  user_id 用户ID
     *  user_name 用户昵称
     *  img_url 商品图片
     *  digest 点评内容
     * ]
     */
    private function getPingGoods()
    {
        $id_list = $this->getPingId();
        if($id_list){
            $query = $this->getQuery();
            $result = $query
                    ->select(['com_quality AS quality',
                            'com_satisfy AS satisfy',
                            'com_user_id AS user_id',
                            'com_digest AS digest',
                            'gds_id AS id',
                            'gds_name AS name' ,
                            'gds_cover_udate AS date'  ,
                            'm.mem_nickname AS user_name'
                    ])
                    ->from(['dog_goods_comment AS c' , 'dog_goods AS g' ,'dog_member as m'])
                    ->where(['and' , 'c.com_gds_id=g.gds_id', 'c.com_user_id = m.uid', 'c.com_status>0' ,['in','c.com_id',$id_list]])
                    ->orderBy('c.com_id DESC')
                    ->limit($this->pingNum)
                    ->all(self::getDb());
            if($result){
                $data = $this->getUrl($result,'pingGoods');
                return $data;
            }

        }
            return false;
    }



    /**
     * 获取推荐的用户评价ID 数据ID在是在点评后台设置
     * @return array|bool
     */
    private function getPingId(){
        $query = $this->getQuery();
        $result = $query
                ->select('set_value')
                ->from(['dog_setting'])
                ->where(['set_id'=>'ping-goods-newest'])
                ->one(self::getDb());
        if($result){
            $id_list = $result['set_value'];
            return explode(',',$id_list);
        }else{
            return false;
        }
    }


    /**
     * 获取每个商品的销售数量 和折扣
     * @param $data
     * @return mixed
     */
    private function _after_select($data)
    {
        foreach($data as $key=>$value){
            if(isset($value['sold'])){
                $data[$key]['gross_sold'] = $this->getGoodsSold($value['sold']);
                unset($data[$key]['sold']); //把原有的数据删除
            }

            if(isset($value['price']) && isset($value['mprice'])){
                if($value['price']== '0.00' || $value['mprice'] == '0.00'){
                    $data[$key]['discount'] = 0;
                }else{
                    $data[$key]['discount'] = round($value['price'] / $value['mprice'] * 10,1);
                }
            }
        }
        return $data;
    }


    /**
     * 获取商品的销售数据
     * @param $param
     * @return int|mixed
     */
    private function getGoodsSold($param)
    {
        //获取商品的销售数据
        if (!empty($param)) {
            $soldGoods = intval($param);
            if ($soldGoods > 0) {
                return $soldGoods;
            }else{
                $soldGoods = unserialize($param);
                if (is_array($soldGoods)) {
                    $sold = 0;
                    foreach ($soldGoods as $val){
                        $sold += $val;
                    }
                    return $sold;
                }
            }
        }else{
            return 0;
        }
    }


    /**
     * 获取图片
     * @param $data
     * @param $type
     */
    private function getUrl($data,$type)
    {
        static $model = null;
        if($model === null){
            $model = new NewPhotos();
        }
        foreach($data as $key=>$val){
            if($type == 'mall1'){
                $data[$key]['img_url'] = $model->getMall1Url($val['id'],$val['atime']);
                unset($data[$key]['atime']);
            }elseif($type == 'mall2'){
                $data[$key]['img_url'] = $model->getMall2Url($val['id'],$val['atime']);
                unset($data[$key]['atime']);
            }elseif($type == 'pingGoods'){
                $data[$key]['img_url'] = $model->getPingGoodsUrl($val['id'],$val['date']);
                unset($data[$key]['date']);
            }
        }
        return $data;
    }


    private function getQuery()
    {
        $db = new Query();
        return $db;
    }
} 