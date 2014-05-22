<?php
namespace backend\models;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\BaseArrayHelper;
use yii\data\ActiveDataProvider;

class Threadclass extends ActiveRecord {
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'pre_forum_threadclass';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            [['title','keywords','description'],'safe'],
        ];
    }
    public static function getDb(){
        return Yii::$app->get("dogdb");
    }
    /**
     * [getTypesByFid description]
     * @param  int $fid [description]
     * @return [type]      [description]
     */
    public static function getAllData($fid){
        $query = self::find()->where("fid=$fid");

        $provider = new ActiveDataProvider([
              'query' => $query,
              'pagination' => [
                    'pageSize' => 30,
                ],
        ]);
        return $provider;
    }
}