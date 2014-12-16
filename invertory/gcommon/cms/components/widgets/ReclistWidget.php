<?php
Yii::import("gcommon.cms.components.widgets.CmsWidget");
class ReclistWidget extends CmsWidget {
    /**
     * category id
     *
     */
    public $categoryid = 0;
    public $count = 2;
    public $length = 15;
    /**
     * function_description
     *
     *
     * @return
     */
    public function run() {
        if (empty($this->categoryid)) {
            return "";
        }
        return $this->getRecList();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getRecList() {
        $objects = Object::model()->getRecommendContentByTermId($this->categoryid,$this->count);
        if(empty($objects)){
            return "";
        }
        $html = "";
        foreach($objects as $object){
            $resource = ObjectResource::model()->with('resource')->findByAttributes(array("type"=>"image","object_id"=>$object->object_id));
            if(!empty($resource) && !empty($resource->resource->resource_path)){
                $image = "http://s1.17w78.com/".$resource->resource->resource_path;
            }else{
                $image = "";
            }
            $url = $object->url;
            $title = CmsHelper::cutstr($object->object_title,$this->length);
            $html .= "<li><a target='_blank' href='{$url}'><img width='103' height='77' src='$image'><br>".$title."</a></li>";
        }
        return $html;
    }
} 

