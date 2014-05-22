<?php
namespace common\models\db;
use Yii;
use yii\db\Query;
use gcommon\cms\models\Db;
class Health extends Db{
    protected $_dbType = "health";
    public function getDbType(){
        return $this->_dbType;
    }
    /*select t1.id,t1.name,t2.73_img from dog_health_case t1, dog_health t2 where t1.doctotid=t2.user_id
    order by t1.addtime desc limit 10*/
    /**
     * [getData description]
     * @return [type] [description]
     */
    public function getData($params = null){
        $query = new Query;
        $results = $query
            ->select('t1.id,t1.name,t2.73_img as img')
            ->from(['dog_health_case t1','dog_health t2'])
            ->where('t1.doctorid=t2.user_id')
            ->orderBy('t1.addtime desc')
            ->limit(15)
            ->all(self::getDb());
        return $results;
    }

    /**
     * [getDb description]
     * @return [type] [description]
     */
    public static function getDb(){
        return \Yii::$app->get("dogdb");
    }

}