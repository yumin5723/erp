<?php
namespace backend\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use gcommon\cms\components\UploadFile;
use yii\helpers\BaseArrayHelper;

class Apk extends ActiveRecord {
    /**
     * page draft
     */
    public $upload;



    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'apk';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            [['name','version'],'required'],
            [['path','message'],'safe'],
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
   /**
     * save new Rar file when create page
     *
     *
     * @return array(boolean result, MError err)
     * result for if new file have saved success.
     * err is the error for not created or saved. if saved success, the err is null
     */
    public function saveRarFile($files){
        $tmp_file = $files['Apk']['tmp_name']['upload'];
        $real_file = $files['Apk']['name']['upload'];
        $file = new UploadFile($tmp_file,$real_file,"Apk");
        $uri = $file->get_file_uri();
        if($uri == ""){
            return false;
        }
        $this->path = Yii::$app->params['resource_folder'].$uri;
        return true;
    }

}