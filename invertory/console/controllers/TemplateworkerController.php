<?php
namespace console\controllers;
use Yii;
use gcommon\cms\components\CmsWorkerController;
use gcommon\cms\models\Template;
use gcommon\cms\models\Block;
class TemplateworkerController extends CmsWorkerController{
    protected $_listen_events = array(
        'block:published',
    );
    /**
     * function_description
     *
     *
     * @return
     */
    public function work() {
        if ($this->_current_event['obj_type'] == "block") {
            try {
                $block_id = $this->_current_event['obj_id'];
                // get page dependent this object
                $templateModel = new Template;
                $ids = $templateModel->getAllIdsDependentBlock($block_id);
                // update pages
                foreach ($ids as $id) {
                    $t = Template::findOne($id);
                    $t->firePublished("",$this->_current_event['eid']);
                }
            } catch (Exception $e) {
                Yii::error("Error on update template using block:". $block_id . ". With error: ".$e->getMessage());
                return false;
            }
        }
    }

}