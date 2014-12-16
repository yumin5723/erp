<?php
namespace gcommon\cms\components\widgets;
use gcommon\cms\models\Object;
use gcommon\cms\components\GxcHelpers;
class RelationlistWidget extends CmsWidget {
    /**
     * category id
     *
     */
    public $count = 5;
    /**
     * function_description
     *
     *
     * @return
     */
    public function run() {
        if (empty($this->id)) {
            return "";
        }
        return $this->getRelationlist();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getRelationlist() {
        $object = new Object;
        $lists = $object->getRelationList($this->id,$this->count);
        $html ='';
        foreach($lists as $list){
            $url = $list->url;
            $object_title = GxcHelpers::cutstr($list->object_title,"30");
            $html .= "<dd><a href='$url' target='_blank' class='a3'>$object_title</a></dd>";
        }
        return $html;
    }
}