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

class Template extends CmsActiveRecord {

    const STATUS_PARSEING = 0;
    const STATUS_ENABLE = 1;
    const STATUS_PARSE_ERROR = 2;
    public $upload;
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'template';
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
            ['type','required'],
            ['rar_file','safe'],
            ['status','safe'],
            ['content','safe'],
        ];
    }
    public function behaviors()
    {
        return BaseArrayHelper::merge(
            parent::behaviors(),
            [
                'cms_type' => [
                    'class' => 'gcommon\cms\components\CmsTypeBehavior',
                ],
                'cms_event' => [
                    'class' => 'gcommon\cms\components\CmsEventBehavior',
                ],
                'cms_dependence' => [
                    'class' => 'gcommon\cms\components\CmsDepBehavior',
                ],
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
        $tmp_file = $files['Template']['tmp_name']['upload'];
        $real_file = $files['Template']['name']['upload'];
        $file = new UploadFile($tmp_file,$real_file,"templete");
        $uri = $file->get_file_uri();
        if($uri == ""){
            return false;
        }
        $this->rar_file = Yii::$app->params['resource_folder'].$uri;
        return true;
    }
   /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name' => '模板名字',
            'upload' => '上传',
            'rar_file' => '路径',
            'status' => "状态",
            'content' => "内容",
        );
    }
    /**
     * get templete status
     *
     * @return array
     */
    public static function getPageStatus() {
        $templete_status = array(
            "0"=>"解析中",
            "1"=>"可用",
            "2"=>"解析错误",
        );
        return $templete_status;
    }
    /**
     * Convert from value to the String of the Object Status
     *
     * @param type    $value
     */
    public static function convertTempleteStatus() {
        return '
            $status = gcommon\cms\models\Template::getPageStatus();
            if ( isset( $status[$model->status] ) ) {
                return $status[$model->status];
            } else {
                return "undefined";
            }
        ';
    }
    /**
     * function_description
     *
     *
     * @return boolean
     */
    public function parse() {
        try {
            $files = Yii::$app->publisher->parseEntirePage($this->rar_file);
            $this->content=$files['html'];
            $this->status = self::STATUS_ENABLE;
            return $this->save(false);
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::log("Parse templete error for templete: ".$this->id.", with error: ".$e->getMessage(), CLogger::LEVEL_ERROR);
            $this->content = "";
            $this->status = self::STATUS_PARSE_ERROR;
            $this->save(false);
            return true;
        }
    }
    /**
     * get all contents who use this templete
     * @param  intval $templete_id
     * @return array
     */
    public function getAllContentsByTempleteId($templete_id){
        $objects = ObjectTemplete::model()->findAllByAttributes(array("templete_id"=>$templete_id));
        $ret = array();
        foreach($objects as $key=>$object){
            array_push($ret,$object->object_id);
        }
        return $ret;
    }
    /**
     * [publishAllContents description]
     * @return [type] [description]
     */
    public function publishAllContents($templete_id){
        return  $this->getAllContentsByTempleteId($templete_id);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->fireNew();
        } else {
            $this->fireUpdate();
        }
        if(!empty($this->content)){
            $this->UpdateDependentBlockByHtml($this->content);
        }
        return parent::beforeSave($insert);
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function afterSave($insert,$changedAttributes) {
        $this->firePublished();
        return parent::afterSave($insert,$changedAttributes);
    }

    /**
     * get can use page type to save to table page_term
     */
    public function getTempleteTypes(){
        return ConstantDefine::getTempleteType();
    }
}