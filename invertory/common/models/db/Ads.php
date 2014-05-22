<?php
namespace common\models\db;
use Yii;
use gcommon\cms\models\Db;
use backend\models\BbsAd;
class Ads extends Db{
    protected $_dbType = "ads";

    public function getDbType(){
        return $this->_dbType;
    }
    /**
     * get bbs focus data
     */
    public function getData($params = null){
        return BbsAd::find()->select(['ad_image','ad_url'])->limit(3)->all();
    }
}