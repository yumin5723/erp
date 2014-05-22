<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;

class Project extends ActiveRecord {
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'project';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            ['name','required'],
        ];
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
    public function updateAttrs($attributes){
        $attrs = array();
        if (!empty($attributes['name']) && $attributes['name'] != $this->name) {
            $attrs[] = 'name';
            $this->name = $attributes['name'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }
    public function getSelect(){
        return 'new yii\web\JsExpression("function( event, ui ) {
        $("#user-company").val(ui.item.id); }")';



        //  return '
        //     $status = gcommon\cms\models\Page::getPageStatus();
        //     if ( isset( $status[$model->status] ) ) {
        //         return $status[$model->status];
        //     } else {
        //         return "undefined";
        //     }
        // ';
    }
}