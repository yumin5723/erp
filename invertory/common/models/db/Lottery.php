<?php
/**
 * user: liding
 * date: 14-4-24
 */

namespace common\models\db;

use Yii;
use gcommon\cms\models\Db;
use yii\db\Query;

class Lottery extends Db {
    protected $_db_type = "lottery";
    public $lotteryRecordNum = 5;//设置抽中记录数量
    public $lotteryItemNum = 3; //设置抽奖商品获取数量


    public function getDbType() {
        return $this->_db_type;
    }

    public static function getDb(){
        return \Yii::$app->get("dogdb");
    }


    /**
     * 返回数据
     * @param null $params
     * @return mixed
     * [
     *  lotteryRecord  array 抽奖记录 没有则是空数组
     *  lotteryItem  array 抽奖奖品 没有则是空数组
     * ]
     */
    public function getData($params = null)
    {
        $lotteryRecord =  $this->getLotteryRecord();
        if($lotteryRecord){
            $data['lotteryRecord'] = $lotteryRecord;
        }else{
            $data['lotteryRecord'] = [];
        }
        $lotteryItem = $this->getLotteryItem();
        if($lotteryItem){
            $data['lotteryItem'] = $lotteryItem;
        }else{
            $data['lotteryItem'] = [];
        }

        return $data;
    }


    /**
     * 获取抽奖记录
     * @return array
     * [
     *  user_id 用户ID
     *  user_name 用户昵称
     *  item_name 奖品
     * ]
     */
    private function getLotteryRecord()
    {
        $query = $this->getQuery();
        $results = $query
                ->select(['w.win_user_id AS user_id' , 'i.item_name' , 'm.mem_nickname AS user_name'])
                ->from(['dog_lottery2_winner w' , 'dog_lottery2_items i' , 'dog_member m'])
                ->where([ 'and' , 'w.win_item_id = i.item_id' , 'w.win_user_id = m.uid'])
                ->limit($this->lotteryRecordNum)
                ->orderBy('w.win_id DESC')
                ->all(self::getDb());
        return $results;
    }


    /**
     * 获取抽奖商品
     * @return array
     * [
     *  item_id 奖品ID
     *  item_name 奖品名
     *  item_price 奖品降格
     *  item_cover_udate 图片更新次数
     * ]
     */
    private function getLotteryItem()
    {
        $query = $this->getQuery();
        $results = $query
                ->select(['item_id' , 'item_name' , 'item_price' , 'item_cover_udate'])
                ->from(['dog_lottery2_items'])
                ->where(['item_hide'=>0])
                ->orderBy('item_order DESC')
                ->limit($this->lotteryItemNum)
                ->all(self::getDb());
        return $results;
    }

    private function getQuery()
    {
        $db = new Query();
        return $db;
    }
}
