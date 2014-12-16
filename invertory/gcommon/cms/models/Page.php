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
use gcommon\cms\models\DynamicTemplate;

class Page extends CmsActiveRecord {
    /**
     * page draft
     */
    const STATUS_PARSEING = 0;
    const STATUS_DRAFT = 1;
    const STATUS_NEED_PUBLISH = 2;
    const STATUS_PUBLISHING = 3;
    const STATUS_PUBLISHED = 4;
    const STATUS_PARSE_ERROR = 5;
    const IS_TEMPLATE = 1;
    const NAME_INDEX = "index";
    public $pagetype;

    const ERROR_ATTRIBUTE_VALUE = 3001;
    public $upload;



    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'page';
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
            ['domain','required'],
            ['path','required'],
            ['is_template','required'],
            ['title','required'],
            ['keywords','required'],
            ['description','required'],
            // ['upload', 'file','allowEmpty'=>false,'types'=>'rar','maxSize'=>1024 * 1024 * 4,'tooLarge'=>'The file was larger than 4MB. Please upload a smaller file.'],
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
    public function attributelabels(){
        return [
            // 'name'=>'商品',
        ];
    }
     /**
     * get can use domain
     *
     * @return array
     */
    public function getCanUseDomain() {
        $domains = Yii::$app->publisher->domains;
        $ret = array();
        foreach($domains as $key=>$domain){
            $ret[$key] = $key;
        }
        return $ret;
    }
    /**
     * Convert from value to the String of the Object Status
     *
     * @param type    $value
     */
    public static function convertPageStatus() {
        return '
            $status = gcommon\cms\models\Page::getPageStatus();
            if ( isset( $status[$model->status] ) ) {
                return $status[$model->status];
            } else {
                return "undefined";
            }
        ';
    }
    public function getPreviewLink(){
        return '
            return \yii\helpers\Html::a("预览","/cms/page/preview?id=$model->id",["target"=>"_blank"]);
        ';
    }

    /**
     * Convert from value to the String of the Object Status
     *
     * @param type    $value
     */
    public static function convertLable( $value ) {
        if ($value == self::STATUS_DRAFT) {
            return "发布";
        }
        if($value == self::STATUS_PUBLISHED){
            return "重新发布";
        }
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
        $tmp_file = $files['Page']['tmp_name']['upload'];
        $real_file = $files['Page']['name']['upload'];
        $file = new UploadFile($tmp_file,$real_file,"page");
        $uri = $file->get_file_uri();
        if($uri == ""){
            return false;
        }
        $this->status = self::STATUS_PARSEING;
        $this->rar_file = Yii::$app->params['resource_folder'].$uri;
        return true;
    }
    /**
     * get page status
     *
     * @return array
     */
    public static function getPageStatus() {
        $page_status = array(
            "0"=>"解析中",
            "1"=>"草稿",
            "2"=>"可用",
            "3"=>"正在发布",
            "4"=>"已经发布",
            "5"=>"解析错误",
        );
        return $page_status;
    }
    public function getOperationLink(){
      return '
          if($model->status == 1 || $model->status == 2){
             return \yii\helpers\Html::a("发布","/cms/page/publish?id=$model->id");
          }elseif($model->status == 4){
             return \yii\helpers\Html::a("重新发布","/cms/page/publish?id=$model->id");
          }else{
             return "";
          }
      ';
    }

    // *
    //  * function_description
    //  *
    //  *
    //  * @return boolean
     
    public function parse() {
        try {
            $files = pathinfo($this->rar_file);
            $ext = $files['extension'];
            if($ext != "rar"){
                return true;
            }
            $files = Yii::$app->publisher->parseEntirePage($this->rar_file);
            $this->content=$files['html'];
            $this->status = self::STATUS_DRAFT;
            return $this->save(false);
        } catch (Exception $e) {
            Yii::log("Parse page error for page: ".$this->id.", with error: ".$e->getMessage(), CLogger::LEVEL_ERROR);
            $this->content = "";
            $this->status = self::STATUS_PARSE_ERROR;
            $this->save(false);
            return true;
        }
    }
    /**
     * display the page view 
     * @param  arrar $id  page id
     * @return string
     */
    public function display(){
        return Yii::$app->cmsRenderer->render($this,$this->content,array());
    }
    /**
     * publish html page 
     * @return boolean
     */
    public function doPublish(){
        // update block dependent
        $this->UpdateDependentBlockByHtml($this->content);
        if($this->is_template == self::IS_TEMPLATE){
            $result = $this->buildTemplate(self::NAME_INDEX,$this->display());
        }else{
            $result = Yii::$app->publisher->saveDomainHtml($this->domain,$this->path,$this->display());
        }
        if($result){
            $this->status = self::STATUS_PUBLISHED;
            $this->save(false);
            return true;
        }
        return false;
    }
    /**
     * build a template if property is_template is 1
     *
     * 
     */
    public function buildTemplate($name,$content){
        return DynamicTemplate::buildTemplate($name,$content);
    }
    /**
     * get can use page type to save to table page_term
     */
    public function getPageTypes(){
        return ConstantDefine::getPageType();
    }
    /**
     * get page title
     */
    public function getTitle(){
        return $this->title;
    }
    /**
     * get page keywords
     */
    public function getKeywords(){
        return $this->keywords;
    }
    /**
     * get page description
     */
    public function getDescription(){
        return $this->description;
    }

    public function getResult($type){
        $pages = PageTerm::model()->findAllByAttributes(array('type'=>$type));
        if (empty($pages)) {
            return null;
        }
        $pages_ids = array();
        foreach ($pages as $value) {
            $pages_ids[] = $value->page_id;
        }
        $criteria = new CDbCriteria;
        $criteria->addInCondition('id',$pages_ids);
        $result = self::model()->findAll($criteria);
        return $result;
    }

    public function getSpecialPages($type){
        $result = $this->getResult($type);
        if (empty($result)) {
            return array();
        }
        $ret = array();
        foreach ($result as $key => $value) {
            $ret[$key]['name'] = $value->name;
            $ret[$key]['url'] = "/cms/special/block/page_id/".$value->id;
            $ret[$key]['id'] = $value->id;
        }
        return $ret;
    }

    public function getSubjectPages($type){
        $result = $this->getResult($type);
        if (empty($result)) {
            return array();
        }
        $ret = array();
        foreach ($result as $key => $value) {
            $ret[$key]['name'] = $value->name;
            $ret[$key]['url'] = "/cms/subject/block/page_id/".$value->id;
            $ret[$key]['id'] = $value->id;
        }
        return $ret;
    }

}