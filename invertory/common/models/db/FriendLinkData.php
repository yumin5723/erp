<?php
namespace common\models\db;
use Yii;
use yii\db\Query;
use gcommon\cms\models\Db;
use backend\models\FriendLink;
class FriendLinkData extends Db{

    protected $_dbType = "FriendLink";

    public function getDbType(){
        return $this->_dbType;
    }
    /**
     * [getData description]
     * @return [type] [description]
     */
    public function getData($params = null){
        $data = FriendLink::find()->where(['link_type'=>$params])->all();
        $rs = [];
        if($data){
            foreach ($data as $key => $value) {
                $rs[$key]['link_text'] = $value['link_text'];
                $rs[$key]['link_url'] = $value['link_url'];
            }
        }
        return $rs;
    }

}