<?php
namespace gcommon\cms\models;
use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use gcommon\cms\components\CmsActiveRecord;
use gcommon\cms\components\Publisher;
use gcommon\cms\components\ConstantDefine;
use gcommon\cms\components\UploadFile;
use yii\helpers\BaseArrayHelper;

class DynamicTemplate extends CmsActiveRecord {
    const INDEX_ID = 68;
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'dynamic_template';
    }

    public function behaviors()
    {
        return BaseArrayHelper::merge(
            parent::behaviors(),
            [
                'timestamp' => [
                    'class' => 'yii\behaviors\TimestampBehavior',
                    'attributes' => [
                        ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'modified'],
                        ActiveRecord::EVENT_BEFORE_UPDATE => 'modified',
                    ],
                    'value' => function (){ return date("Y-m-d H:i:s");}
                ],
           ]
        );
    }
    public static function buildTemplate($name,$content){
        $template = self::findOne(self::INDEX_ID);
        if($template !== null){
            $template->content = $content;
            if($template->save()){
                return true;
            }
            return flase;
        }
        
        $model = new self;
        $model->name = $name;
        $model->content = $content;
        if($model->save()){
            return true;
        }
        return false;
    }
    public static function getCanuseTemplateByName($name){
        $template = self::find()->where(['name'=>$name])->orderby('id DESC')->one();
        if($template !== null){
            return $template->content;
        }
        return "";
    }

}