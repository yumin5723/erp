<?php
Yii::import("gcommon.cms.components.widgets.CmsWidget");
class TopblockWidget extends CmsWidget {
    /**
     * category id
     *
     */
    public $categoryid = 0;
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
        return $this->getTopBlock();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getTopBlock() {
        $object = Object::model()->getTopContentByTermId($this->categoryid);
        if(empty($object)){
            return "";
        }
        $slug = CmsHelper::cutstr($object->object_slug,50);
        $html = "<h4><a href='{$object->url}' class='ared'>".$object->object_title."</a></h4> <p>".$slug."
        <a href='{$object->url}' class='ablue'>[详细]</a></p>";
        return $html;
    }
}