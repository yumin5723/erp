<?php
Yii::import("gcommon.cms.components.widgets.CmsWidget");
Yii::import("gcommon.cms.models.Object");
class NewslistWidget extends CmsWidget {
    /**
     * category id
     *
     */
    public $categoryid = 0;
    public $count = 5;
    public $hasdate = true;
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
        return $this->getNewslist();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getNewslist() {
        $objects = Object::model()->fetchObjectsByTermId($this->categoryid,$this->count,0);
        if(empty($objects)){
            return "";
        }
        $html = "";
        foreach($objects as $obj){
            $date = date('m-d',strtotime($obj->object_date));
            $url = $obj->url;
            $title = CmsHelper::cutstr($obj->object_title,$this->length);
            if($this->hasdate == "false"){
                $html .= "<li class='clearfix'><a href='{$url}' target='_blank'>$title</a></li>";
            }else{
                $html .= "<li class='clearfix'><a href='{$url}' target='_blank'>$title</a><span>$date</span></li>";
            }
        }
        return $html;
    }


}