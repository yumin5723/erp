<?php
/**
 * user: liding
 * date: 14-4-25
 */

namespace common\models\db;

use Yii;
use gcommon\cms\models\Db;
use yii\db\Query;

class DogCount extends Db{
    protected $_db_type = "dogCount";
    public function getDbType() {
        return $this->_db_type;
    }

    public static function getDb(){
        return \Yii::$app->get("dogdb");
    }

    /**
     * 返回狗狗统计
     * @param null $params
     * @return array
     * [
     *  dogNum 狗狗总数量
     *  todayNum 今天新增狗狗数量
     *  yesterdayDauNum 昨日新增狗狗数量
     * ]
     */
    public function getData($params = null)
    {
        $data =[];
        $data['dogNum'] = $this->getDogNum();
        $data['todayNum'] = $this->getTodayDogNum();
        $data['yesterdayDauNum'] = $this->getYesterdayDogNum();
        return $data;
    }


    /**
     * 获取昨日新增狗狗数量
     * @return int
     */
    private function getYesterdayDogNum()
    {
        $yesterday  = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
        $today  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
        $query = $this->getQuery();
        $result = $query
                ->where("dog_auth = 0 AND dog_cdate >= $yesterday AND dog_cdate < $today")
                ->from(['dog_doginfo'])
                ->count('dog_id',self::getDb());
        return $result;
    }


    /**
     * 获取今天新增狗狗数量
     * @return int
     */
    private function getTodayDogNum()
    {
        $today  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
        $query = $this->getQuery();
        $result = $query
                ->where("dog_auth = 0 AND dog_cdate >= $today")
                ->from(['dog_doginfo'])
                ->count('dog_id',self::getDb());
        return $result;
    }

    /**
     * 获取狗狗总数
     * @return int
     */
    private function getDogNum()
    {
        $query = $this->getQuery();
        $dog_total = $query->from('dog_doginfo')->count('dog_id',self::getDb());
        $dog_auth_num = $query
                ->where("dog_auth = 0 AND dog_species=90")
                ->from(['dog_doginfo'])
                ->count('dog_id',self::getDb());
        return $dog_total - $dog_auth_num;
    }

    private function getQuery(){
        return new Query();
    }
} 