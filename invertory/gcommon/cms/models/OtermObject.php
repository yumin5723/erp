<?php
Yii::import("gcommon.cms.models.Object");
/**
 * This is the model class for table "game".
 *
 * The followings are the available columns in table 'game':
 * @property integer $id
 * @property string $name
 * @property string $tags
 * @property string $from
 * @property string $desc
 * @property string $operations_guide
 * @property string $how_begin
 * @property string $target
 * @property string $image
 * @property string $tag_image
 * @property string $url
 * @property string $created_uid
 * @property string $modified_uid
 * @property string $created
 * @property string $modified
 */
class OtermObject extends Object
{
    /**
     * save a otermpage type for object 
     * @param  [type] $term_id     [description]
     * @param  [type] $template_id [description]
     * @return [type]              [description]
     */
    public function getOtermPageId($term_id,$template_id){
        $object = new self;
        $object->object_title = "oterm for term_id: ".$term_id;
        $object->object_content = "this is a otermpage type subject for build oterm page list";
        $object->object_type = "otermpage";
        if($object->save(false)){
            ObjectTemplate::model()->saveObjectTemplete($object->id,$template_id);
            return $object->id;
        }
    }
    /**
     * display the content view 
     * @param  arrar $id  object_id
     * @return string
     */
    public function display($term_id,$template_id,$page){
        $object = Object::model()->findByPk($template_id);
        if(!empty($object)){
            $object_template = ObjectTemplete::model()->findByAttributes(array("object_id"=>$object->id));
            if(!empty($object_template)){
                $template = Templete::model()->findByPk($object_template->templete_id);
                if(empty($template)){
                    return "";
                }
                return Yii::app()->cmsRenderer->render($object,$template->content,array("page"=>$page,"categoryid"=>$term_id));
            }else{
                return "";
            }
        }
        return "";
    }
    /**
     * publish html page 
     * @return boolean
     */
    public function doPublish(){
        $domain = Yii::app()->getModule("cms")->domain;
        $url = "/list/".$this->id."_1.html";
        $count = Object::model()->getObjectsCountByTermId($this->id);
        $offset = self::LIST_PAGE_DISPLAY_COUNT;
        $page = ceil($count/$offset);
        //only build 100 pages
        if($page > 100){
            $page = 100;
        }
        for($i=1;$i<=$page;$i++){
            $content = $this->display($i);
            $path = "list/".$this->id."_".$i.".html";
            $result = Yii::app()->publisher->saveDomainHtml($domain,$path,$content);
            if($result){
                // $this->firePublished();
                // return true;
            }else{
                Yii::log("published object fail for object:".$this->object_id,CLogger::LEVEL_WARNING);
            }
        }
        $this->saveAttributes(array("url"=>$url));
        return true;
    }
}