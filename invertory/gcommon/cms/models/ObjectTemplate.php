<?php
namespace gcommon\cms\models;
use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use gcommon\cms\components\CmsActiveRecord;
use gcommon\cms\components\Publisher;
use gcommon\cms\components\ConstantDefine;
use gcommon\cms\components\UploadFile;
use gcommon\cms\models\Template;
use yii\helpers\BaseArrayHelper;
/*
 * This is the model class for table "{{object_templete}}".
 *
 * The followings are the available columns in table '{{object_templete}}':
 * @property string $object_id
 * @property string $templete_id
 */
class ObjectTemplate extends CmsActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'object_template';
    }
    /**
     * save object templete relation
     */
    public function saveObjectTemplete($object_id,$templete){
        $result = self::model()->findByAttributes(array("object_id"=>$object_id));
        if(empty($result)){
            $model = new self;
            $model->object_id = $object_id;
            $model->templete_id = $templete;
            $model->save(false);
        }else{
            if($result->templete_id != $templete){
                $result->templete_id = $templete;
                $result->save(false);
            }
        }

    }

    /**
     * function_description
     *
     * @param $id:
     *
     * @return
     */
    public function getAllObjectsIdByTemplateId($id) {
        return array_map(function($t){return $t->object_id;},
           self::find()->where(['templete_id'=>$id])->all());
    }
    

}