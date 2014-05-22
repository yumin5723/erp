<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;

class Forum extends ActiveRecord {
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'pre_forum_forum';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
        ];
    }
    public static function getDb(){
        return Yii::$app->get("dogdb");
    }
}