<?php
Yii::import("gcommon.cms.components.widgets.CmsWidget");
Yii::import("gcommon.cms.models.Block");
class BlockWidget extends CmsWidget {

    public $blockid = 1;
    /**
     * function_description
     *
     *
     * @return
     */
    public function run() {
        if (empty($this->blockid)) {
            return "";
        }
        return $this->getBlockContent();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getBlockContent() {
        $block = Block::model()->findByPk($this->blockid);
        if(empty($block)){
            return "";
        }
        return $block->content;
    }
}