<?php
/**
 * user: liding
 * date: 14-4-25
 */

namespace common\models\db;

use Yii;
use gcommon\cms\models\Db;
use yii\db\Query;
use gcommon\cms\models\editor\NewPhotos;

class DogFoodRecommend extends Db{

    protected $_db_type = "dogFoodRecommend";
    public $mall1GoodsNum = 4;

    public function getDbType() {
        return $this->_db_type;
    }

    public static function getDb(){
        return \Yii::$app->get("dogdb");
    }

    public function getData($param = null){
        $result = $this->getFoodRecommend();
        return $result;
    }


    /**
     * 获取用户登录根据用户的犬种推荐一些狗粮
     * TODO 因为没有数据分析 所以随便获取几种狗粮
     * TODO 强烈建议不要在ORDER BY 后面 写mysql 函数 但是为了及时上线 以后在优化
     * @return array
     * [
     *  id 商品ID
     *  name 商品名称
     *  price 商品价格
     *  img_url 商品图片
     * ]
     */
    private function getFoodRecommend()
    {
        $time =time();
        $query = $this->getQuery();
        $result = $query
                ->select( "mall_gds_id id , mall_name name , p_price_min price , p_msrp_min mprice ,  mall_cover_udate atime")
                ->from(['dog_mall'])
                ->Where("mall_status > 0  AND mall_expire > $time")
                ->andwhere(['mall_display'=>1 , 'mall_jingbao'=>0 ])
                ->orderBy('rand()')
                ->limit($this->mall1GoodsNum)
                ->all(self::getDb());
        if($result){
            $model = new NewPhotos();
            foreach($result as $key=>$val){
                $result[$key]['img_url'] = $model->getMall1Url($val['id'],$val['atime']);
                unset($result[$key]['atime']);
            }
        }
        return $result;
    }

    private function getQuery()
    {
        return new Query();
    }
} 