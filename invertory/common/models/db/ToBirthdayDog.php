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

class ToBirthdayDog extends Db {
    protected $_db_type = "toBirthdayDog";
    public $birthdayDog = 3;

    public function getDbType() {
        return $this->_db_type;
    }

    public static function getDb(){
        return \Yii::$app->get("dogdb");
    }

    /**
     * 返回3条最近过生日的狗狗
     * @param null $param
     * @return array 成功返回数据 没有则返回空数组
     */
    public function getData($param = null){
        $result = $this->getToBirthdayDog();
        if($result){
            return $result;
        }else{
            return [];
        }
    }

    /**
     * 获取最近过生日的狗狗
     * TODO SQL 性能待测试
     * @return array
     * [
     *  dog_id 狗狗ID
     *  dog_name 狗狗名称
     *  head_url 狗狗头像
     * ]
     */
    // 以下是下面方法执行的SQL
    /*SELECT `i`.`dog_id`, `i`.`dog_name`, `h`.`head_id`, `h`.`head_cdate`, `h`.`head_fileext`
        FROM `dog_doginfo` `i`, `pre_common_member` `m`, `dog_head_image` `h`
       WHERE ((i.dog_userid = m.uid)
         AND (i.dog_headid>0)
         AND (m.credits>50)
         AND (i.dog_headid=h.head_id)
         AND (`i`.`dog_status` IN (0, 1, 3, 4)))
         AND (i.dog_birth_time BETWEEN $time-2*24*3600 AND $time+7*24*3600 )
    ORDER BY rand()
       LIMIT 3*/
    private function getToBirthdayDog()
    {
        $time = time();
        $query = $this->getQuery();
        $result = $query
                ->select(['i.dog_id' , 'i.dog_name' , 'h.head_id' ,'h.head_cdate' , 'h.head_fileext'])
                ->from(['dog_doginfo AS i','pre_common_member m' ,'dog_head_image h'])
                ->where([
                        'and' ,
                        'i.dog_userid = m.uid' ,
                        'i.dog_headid>0' ,
                        'm.credits>50' ,
                        'i.dog_headid=h.head_id',
                        ['in','i.dog_status',[0,1,3,4]]
                ])
                ->andWhere("i.dog_birth_time BETWEEN $time-2*24*3600 AND $time+7*24*3600 ")
                ->orderBy('rand()')
                ->limit($this->birthdayDog)
                ->all(self::getDb());
        if($result){
            $model = new NewPhotos();
            foreach($result as $key=>$val){
                $result[$key]['head_url'] = $model->getDogHeadUrl($val['head_id'] ,$val['head_fileext'],$val['head_cdate'],'s');
                unset($result[$key]['head_id']);
                unset($result[$key]['head_fileext']);
                unset($result[$key]['head_cdate']);
            }
        }
        return $result;
    }


    private function getQuery()
    {
        return new Query();
    }

} 