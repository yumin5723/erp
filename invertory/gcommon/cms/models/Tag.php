<?php
namespace gcommon\cms\models;
use Yii;
use gcommon\cms\components\CmsActiveRecord;
use gcommon\cms\components\ConstantDefine;
use yii\db\Query;
/**
 * This is the model class for table "{{tag}}".
 *
 * The followings are the available columns in table '{{tag}}':
 * @property string $id
 * @property string $name
 * @property integer $frequency
 * @property string $slug
 */
class Tag extends CmsActiveRecord
{
    const LIST_PAGE_DISPLAY_COUNT = 30;
	/**
     *
     *
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            // ['total_number_meta', 'total_number_resource', 'object_slug','safe'],
            [['name'],'required'],
            [['url','title','keywords','frequency','description'],'safe'],
        ];
    }

	public static function tableName(){
		return 'tag';
	}
    /**
     * display the content view 
     * @param  arrar $id  object_id
     * @return string
     */
    public function display($page){
        $template = Template::find()->where(['type'=>ConstantDefine::TEMPLETE_TAG_TYPE])->one();
        if(empty($template)){
            return "";
        }
        return Yii::$app->cmsRenderer->render($this,$template->content,array("page"=>$page,"tag_id"=>$this->id));
    }
    /**
     * publish html page 
     * @return boolean
     */
    public function doPublish(){
        $domain = Yii::$app->getModule("cms")->domain;
        $count = $this->getObjectsCountByTagId($this->id);
        $offset = self::LIST_PAGE_DISPLAY_COUNT;
        $page = ceil($count/$offset);
        //only build 100 pages
        if($page > 100){
            $page = 100;
        }
        if($page != 0 ){
            for($i=1;$i<=$page;$i++){
                $content = $this->display($i);
                $path = "tag/".$this->id."_".$i.".html";
                $result = Yii::$app->publisher->saveDomainHtml($domain,$path,$content);
                if($result){
                    // $this->firePublished();
                    // return true;
                }else{
                    Yii::log("published object fail for object:".$this->object_id,CLogger::LEVEL_WARNING);
                }
            }
        }
        if($this->url == ""){
            $this->url = "tag/".$this->id."_1.html";
        }
        $this->save(false);
        return true;
    }
    /**
     * get objects count by term_id
     * @return [type] [description]
     */
    public function getObjectsCountByTagId($tag_id){
        $results = TagRelationships::find()->leftJoin('object o','tag_relationships.object_id = o.object_id')->andWhere(['o.object_status'=>ConstantDefine::OBJECT_STATUS_PUBLISHED,'tag_id'=>$tag_id])->all();
        $ids = array();
        foreach($results as $result){
            $ids[] = $result->object_id;
        }
        return count($ids);
    }
    public function fetchObjectsByTagId($tag_id,$count,$page){

        $results = TagRelationships::find()->leftJoin('object o','tag_relationships.object_id = o.object_id')->andWhere(['o.object_status'=>ConstantDefine::OBJECT_STATUS_PUBLISHED,'tag_id'=>$tag_id])->all();
        $ids = array();
        foreach($results as $result){
            $ids[] = $result->object_id;
        }
        return Object::find()
                ->with('objecttags')
                ->where("object_status=2")
                ->andFilterWhere(['in', 'object_id', $ids])
                ->orderBy("object_id DESC")
                ->limit($count)
                ->offset(($page - 1) * $count)
                ->all();
    }
    /**
     * get page title
     */
    public function getTitle(){
        if($this->title == ""){
            return $this->name."新闻中心_".$this->name."最新资讯_狗民网";
        }else{
            return $this->title;
        }
    }
    /**
     * get page keywords
     */
    public function getKeywords(){
        if($this->keywords == ""){
            return $this->name."新闻中心,最新".$this->name."信息,".$this->name."最新资讯,最新".$this->name."狗民网";
        }else{
            return $this->keywords;
        }
    }
    /**
     * get page description
     */
    public function getDescription(){
        if($this->description == ""){
            return "狗民网(www.goumin.com)".$this->name."新闻中心栏目为您提供最新的".$this->name."信息,最新的".$this->name."新闻报道,让您可以及时了解".$this->name."最新资讯。";
        }else{
            return $this->description;
        }
    }
    public function getOperationLink(){
      return '
           return \yii\helpers\Html::a("发布","/cms/tag/publish?id=$model->id");
      ';
    }
    /**
     * get all tag ids  
     *
     *
     * @return
     */
    public function getAllTagIds() {
        $query = new Query;
        $rows = $query->select('id')
                     ->from(self::tableName())
                     ->all(static::getDb());
        return array_map(function($a){return $a['id'];},$rows);
    }
}