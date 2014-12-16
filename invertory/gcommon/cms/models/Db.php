<?php
namespace gcommon\cms\models;

use Yii;
use yii\base\Model;

abstract class Db extends Model implements DataSourceInterface
{
    public $object_type = "db";

    abstract public function getDbType();
    abstract public function getData($params = null);


    public function fireUpdate($info="",$parent_event_id=0) {
        $object_id = $this->getDbType();
        $object_type = $this->object_type;
        //@todo: get user id
        $from = 0;
        Yii::$app->cmsEvent->publishEvent($object_id,$object_type,"update",$from,$info,$parent_event_id);
    }
    
}
