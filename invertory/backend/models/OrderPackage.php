<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use backend\components\BackendActiveRecord;

class OrderPackage extends BackendActiveRecord {
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'order_package';
    }
}