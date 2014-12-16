<?php
namespace gcommon\cms\components\widgets;
use gcommon\cms\components\CmsRenderer;
use gcommon\cms\components\widgets\CmsWidget;
use gcommon\cms\models\Block;
class CustomBlockWidget extends CmsWidget {
    public $block_id;

    /**
     * function_description
     *
     *
     * @return
     */
    public function run() {
        // if (!empty($this->block_content)) {
        //     return $this->block_content;
        // }
        if (empty($this->block_id)) {
            return "";
        }
        $cmsRenderer = new CmsRenderer();
        return $cmsRenderer->render("",$this->getBlockContent());
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getBlockContent() {
        $block = Block::findOne($this->block_id);
        if(empty($block)){
            return "";
        }
        return $block->content;
    }
}