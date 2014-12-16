<?php
namespace gcommon\cms\models;
use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use gcommon\cms\components\CmsActiveRecord;
use yii\helpers\BaseArrayHelper;

class Oterm extends CmsActiveRecord {
    const ERROR_ATTRIBUTE_VALUE = 3001;

    const ERROR_NOT_ALLOW_ATTRIBUTE = 3002;

    const ERROR_NOTHING_TO_MODIFY = 3003;

    const ERROR_UNKNOW = 3004;

    const ERROR_NOT_FOUND = 3005;

    const ERROR_SOME_NOT_FOUND = 3006;

    public $parent_id;
    public $term_id;

    const LIST_PAGE_DISPLAY_COUNT = 30;
    const STATUS_PUBLISHED = 1;
    /**
     * function_description
     *
     *
     * @return
     */
    public static function tableName() {
        return 'oterm';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return [
            [['name','short_name'], 'required',],
            [['description','url','template_id'],'safe'],
        ];
    }
    public function behaviors()
    {
        return BaseArrayHelper::merge(
            parent::behaviors(),
            [
              'nestedSetBehavior' => [
                'class' => 'common\extensions\NestedSetBehavior',
                'leftAttribute'=>'left_id',
                'rightAttribute'=>'right_id',
                'levelAttribute'=>'level',
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
     * [getAllDescendantsByRoot description]
     * @param  [type] $root [description]
     * @return [type]       [description]
     */
    public function getAllDescendantsByRoot($root){
        $category = self::model()->findByPk($root);
        $descendants = $category->descendants()->findAll();
        $ret = array();
        foreach($descendants as $key=>$desc){
            $ret[$key]['id'] = $desc->id;
            $ret[$key]['name'] = $desc->name;
            $ret[$key]['level'] = $desc->level;
        }
        return $ret;
    }

    /**
     * get category name all descendants node id
     * 
     * @param $term_name
     * @return array()
     */
    public function getAllSmallTerms($term_name){
        $category = Category::model()->findByAttributes(array('level'=>2,'name'=>$term_name));
        $category=Category::model()->findByPk($category->id);
        $descendants=$category->descendants()->findAll();
        $allterms = array();
        foreach ($descendants as $value) {
            $allterms[] = $value->id;
        }
        return $allterms;
    }

    /**
     * get category name all children node (id,name)
     * 
     * @param $term_name
     * @return array()
     */
    public function getSecTermId($term_name){
        $category = Category::model()->findByAttributes(array('level'=>2,'name'=>$term_name));
        $category=Category::model()->findByPk($category->id);
        $descendants=$category->children()->findAll();
        $allterms = array();
        foreach ($descendants as $value) {
            $allterms[$value->id] = $value->name;
        }
        return $allterms;
    }

    /**
     * get category id
     * 
     * @param $term_name
     * @return int(id)
     */
    public function getTermId($term_name){
        $category = self::model()->findByAttributes(array('level'=>2,'name'=>$term_name));
        return $category->id;
    }

    /**
     * get category id all descendants node id
     * 
     * @param $term_name
     * @return int(id)
     */
    public function getChildTerm($term_id){
        $category=self::findOne($term_id);
        $descendants=$category->descendants()->all();
        $allterms = array();
        foreach ($descendants as $value) {
            $allterms[] = $value->id;
        }
        return $allterms;
    }
    /**
     * get level 
     * @param  [type] $term_id [description]
     * @return [type]          [description]
     */
    public function getLevelByTermId($term_id){
        $category= self::findOne($term_id);
        if(empty($category)){
            return array();
        }
        $descendants=$category->ancestors()->all();
        $ret = array();
        foreach($descendants as $key =>$desc){
            if($desc->level == 1){
                unset($desc);
            }else{
                $ret[$key]['term_name'] = $desc['name'];
                $ret[$key]['url'] = $desc['url'];
                $ret[$key]['id'] = $desc['id'];
                $ret[$key]['short_name'] = $desc['short_name'];
            }
            
        }
        $self = array("term_name"=>$category->name,"url"=>$category->url,"id"=>$category->id,'short_name'=>$category->short_name);
        array_push($ret, $self);
        return $ret;
    }
    /**
     * get ancestors ids by term id
     * @param  [type] $term_id [description]
     * @return array ids
     */
    public function getAncestorsIdsByTerm($term_id){
        $category=Oterm::model()->findByPk($term_id);
        $descendants=$category->ancestors()->findAll();
        return array_map(function ($a){return $a->id;}, $descendants);
    }

    /**
     * [updateNode description]
     * @return [type] [description]
     */
    public function updateNode($parent,$newNode,$uid){
        if ($parent->id != $newNode) {
            $new = Oterm::model()->findByPk($newNode);
            $this->moveAsFirst($new);
        }
        return true;
    }
    /**
     * get all templetes
     * @return array [description]
     */
    public function getTemplates(){
        $templates = Template::find(['type'=>1])->all();
        $newArray = array();
        foreach($templates as $k => $template){
            $newArray[$template->id] = $template->name;
            // $newArray[$k]['name'] = $templete->name;
        }
        return $newArray;
    }
    /**
     * display the content view 
     * @param  arrar $id  object_id
     * @return string
     */
    public function display($page){
        $template = Template::findOne($this->template_id);
        if(empty($template)){
            return "";
        }
        return Yii::$app->cmsRenderer->render($this,$template->content,array("page"=>$page,"categoryid"=>$this->id));
    }
    /**
     * publish html page 
     * @return boolean
     */
    public function doPublish(){
        $domain = Yii::$app->getModule("cms")->domain;
        $object = new Object;
        $count = $object->getObjectsCountByTermId($this->id);
        $oterm = Oterm::findOne($this->id);
        $offset = self::LIST_PAGE_DISPLAY_COUNT;
        $page = ceil($count/$offset);
        //only build 100 pages
        if($page > 100){
            $page = 100;
        }
        for($i=1;$i<=$page;$i++){
            $content = $this->display($i);
            if($i == 1){
                $path = $oterm->short_name."/index.html";
            }else{
                $path = $oterm->short_name."/".$i.".html";
            }
            $result = Yii::$app->publisher->saveDomainHtml($domain,$path,$content);
            if($result){
                // $this->firePublished();
                // return true;
            }else{
                Yii::log("published object fail for object:".$this->object_id,CLogger::LEVEL_WARNING);
            }
        }
        $this->status = self::STATUS_PUBLISHED;
        $this->saveNode();
        return true;
    }
   /**
     * function_description
     *
     * @param $id:
     *
     * @return
     */
    public function getAllTermIdsByTemplateId($id) {
        return array_map(function($t){return $t->id;},
            $this->find()->where(['template_id'=>intval($id)])->all());
    }

    /**
     * get page title
     */
    public function getTitle(){
        return $this->name."新闻中心_".$this->name."最新资讯_狗民网";
    }
    /**
     * get page keywords
     */
    public function getKeywords(){
        return $this->name."新闻中心,最新".$this->name."信息,".$this->name."最新资讯,最新".$this->name."狗民网";
    }
    /**
     * get page description
     */
    public function getDescription(){
        return "狗民网(www.goumin.com)".$this->name."新闻中心栏目为您提供最新的".$this->name."信息,最新的".$this->name."新闻报道,让您可以及时了解".$this->name."最新资讯。";
    }


     /**
     * create and save new category
     *
     *
     * @return array(boolean result, MError err)
     * result for if new category have created and saved success.
     * err is the error for not created or saved. if saved success, the err is null
     */
    public function setCategory($category_id,$uid){
        if($category_id==0){
            $root = self::findOne(['root'=>$category_id]);
        }else{
            $root = self::findOne($category_id);
        }
        $model = new self;
        $model->name = $this->name;
        $model->short_name = $this->short_name;
        $model->description = $this->description;
        $model->url = $this->url;
        $model->template_id = $this->template_id;
        $model->admin_id = $uid;
        if($model->appendTo($root)){
            return array(true, null);
        }
    }


    public function changeCategory($id,$uid){
        $model = self::findOne($id);
        $model->name = $this->name;
        $model->short_name = $this->short_name;
        $model->description = $this->description;
        $model->url = $this->url;
        $model->template_id = $this->template_id;
        $model->admin_id = $uid;
        if($model->saveNode()){
            return true;
        }return false;
    }


    public function getAllRoots(){
        $model = new self; 
        $roots=$model->roots();
        $results = new ActiveDataProvider([
            'query' => $roots,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $results;
    }


    public function getAllData(){
        $rootId = $_GET['root'];
        $root = self::findOne(["root"=>$rootId]);
        $provider = [];
        if($root){
            $descendants = $root->descendants();
            $provider = new ActiveDataProvider([
                'query' => $descendants,
                'sort' => false,
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
        }
        return $provider;
    }


    public function saveRootNode($params){
        $model = new self;
        $model->name = $params['name'];
        $model->short_name = $params['short_name'];
        $model->saveNode();
    }


    public static function getCatIds(){
        $model = new self;
        $roots = $model->findOne(['root'=>0]);
        $descendants = $roots->descendants()->all();
        $rs = ['1'=>'请选择分类'];
        foreach ($descendants as $key => $value) {
            $rs[$value['id']] = static::str_tree($value['level'],$value['name']);
        }
        return $rs;
    }


    public static function str_tree($level,$name){
        $nav = '|';
        $result = str_repeat("-", $level*pow(2, 3));
        
        return $nav.$result.$name."(".($level-1)."级分类)";
    }


    public function attributelabels(){
        return [
            'name'=>'分类名称',
            'short_name'=>'分类缩写',
            'description'=>'描述',
        ];
    }
    /**
     *  add "|-" for view category
     * 
     */
    public function  cate_tree(){
        return function ($data){
            $nav = '|';
            $result = str_repeat("-", $data->level*pow(2,3));
            return $nav.$result.$data->name."(".($data->level-1)."级分类)";
        };
    }


    public function updateCategory($id){
        $post = static::findOne($id);
        if (!$post) {
            return false;
        }
        if (\Yii::$app->request->isPost) {
            $post->load(Yii::$app->request->post());
            if ($post->saveNode()) {
                return true;
            }
        }
        return false;
    }
    public function getBuildLink(){
        return '
                return \yii\helpers\Html::a("发布","/cms/oterm/publish?id=$model->id",["target"=>"_blank"]);
        ';
    }
    public function getPreviewLink(){
        return '
            return \yii\helpers\Html::a("预览","/cms/oterm/preview?id=$model->id",["target"=>"_blank"]);
        ';
    }
    /**
     * get object term url for object menu display used in fhgame for now
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getTermUrl(){
        $term = Oterm::findOne($this->id);
        if(empty($term)){
            return "";
        }
        return "<a class=agray  href={$term->url}>$term->name</a>";
    }
}