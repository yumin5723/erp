<?php
namespace common\models;

use Yii;
use common\components\MallActiveRecord;

class Attributes extends MallActiveRecord{

    protected $pk = 'attr_id';

    /**
     * [tableName description]
     * @return string [description]
     */
    public static function tableName(){
        return 'attribute';
    }


	/**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = static::find(['attr_id'=>$id]);
        if ($model === null) { return false;}
        return $model;
    }
}