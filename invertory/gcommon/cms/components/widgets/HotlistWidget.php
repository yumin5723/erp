<?php
Yii::import("gcommon.cms.components.widgets.CmsWidget");
class HotlistWidget extends CmsWidget {

    public $count=10;
    public $index = false;
    public $length = 15;
    /**
     * function_description
     *
     *
     * @return
     */
    public function run() {
        return $this->getHotlist();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getHotlist() {
        $objects = Object::model()->getHotObjects($this->count);
        if(empty($objects)){
            return "";
        }
        $html = "";
        if($this->index == "true"){
            foreach($objects as $key =>$object){
                $title = CmsHelper::cutstr($object->object_title,$this->length);
                $ared = ($key <= 0) ? "ared" : "";
                if($key <= 0){
                    $img = "<img src='http://s2.17w78.com/images/news/new_ico--f03d4--.png' align='absmiddle' />";
                }else{
                    $img = "";
                }
                $date = date('m-d',strtotime($object->object_date));
                $url = $object->url;
                $html .= "<li class='clearfix'><a href=$url target='_blank' class={$ared}>".$title.  $img." </a><span>{$date}</span></li>";
            }
        }else{
            foreach($objects as $object){
                $title = CmsHelper::cutstr($object->object_title,$this->length);
                $url = $object->url;
                $html .= "<li><a class='ablue' target='_blank' href=$url>".$title."</a></li>";
            }
        }
        return $html;
    }


}