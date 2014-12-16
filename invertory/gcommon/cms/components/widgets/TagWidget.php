<?php
namespace gcommon\cms\components\widgets;
use gcommon\cms\models\Object;
class TagWidget extends CmsWidget {
    /**
     * function_description
     *
     *
     * @return
     */
    public function run() {
        return $this->getContentMenu();
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function getContentMenu() {
        $object = Object::findOne($this->id);
        if(empty($object)){
            return "";
        }
        $html = "";
        foreach($object->objecttags as $tag){
            $url = $tag->url;
            $name = $tag->name;
            $position = strpos($url,"http:");
            if($position === false){
                $html .= "<a href='/{$url}'>$name</a>";
            }else{
                $html .= "<a href='{$url}'>$name</a>";
            }
        }
        return $html;
    }


}