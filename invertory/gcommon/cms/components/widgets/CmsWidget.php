<?php
namespace gcommon\cms\components\widgets;
use Yii;
use yii\base\Widget;
class CmsWidget extends Widget {
    /**
     * for store block content
     *
     */
    public $block_content;

    /**
     * function_description
     *
     *
     * @return
     */
    public function getDependentCategoryIds() {
        return array();
    }

}