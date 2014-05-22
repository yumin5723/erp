<?php
namespace common\models\db;
use Yii;
use yii\db\Query;
use gcommon\cms\models\Db;
class Freask extends Db{
    protected $_dbType = "freask";
    public function getDbType(){
        return $this->_dbType;
    }
    /**
     * [getData description]
     * @return [type] [description]
     */
    public function getData($params = null){
        $query = new Query;
        $results = $query
            ->select('qst_id,qst_subject')
            ->from('dog_ask_question')
            ->where('status=1')
            ->orderBy('qst_views desc')
            ->limit(2)
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