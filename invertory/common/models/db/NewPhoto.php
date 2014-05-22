<?php
namespace common\models\db;
use Yii;
use gcommon\cms\models\Db;
use gcommon\cms\models\editor\NewPhotos;
class NewPhoto extends Db{
    protected $_dbType = "newphoto";

    public function getDbType(){
        return $this->_dbType;
    }
    /**
     * get bbs focus data
     */
    public function getData($params = null){
        $photo = new NewPhotos;
        $model = NewPhotos::find()->orderby("pht_cdate desc")->all();
        $results = [];
        if($model){
            foreach ($model as $key => $v) {
                $results[$key]['pht_href'] = $photo->getAbmid($v['pht_id'],$v['pht_userid']);
                $results[$key]['pht_id'] = $v['pht_id'];
                $results[$key]['pht_image'] = $v['pht_image'];
                $results[$key]['pht_userid'] = $v['pht_userid'];
            }
        }
        return $results;
    }

}