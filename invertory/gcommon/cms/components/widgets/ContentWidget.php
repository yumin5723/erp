<?php
Yii::import("gcommon.cms.components.widgets.CmsWidget");
class ContentWidget extends CmsWidget {
    /**
     * content id
     *
     */
    public $id;

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
        return $this->getContent();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getContent() {
        $c = Object::model()->findByPk($this->id);
        if (empty($c)) {
            throw new CException("Can not find content: ".$this->id);
        }
        return $c->object_content;
    }


}