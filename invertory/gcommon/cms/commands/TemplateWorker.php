<?php
Yii::import("gcommon.cms.models.Templete");
Yii::import("gcommon.cms.models.Block");
Yii::import("gcommon.cms.components.CmsWorker");
class TemplateWorker extends CmsWorker {
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
                $ids = Templete::model()->getAllIdsDependentBlock($block_id);
                // update pages
                foreach ($ids as $id) {
                    $t = Templete::model()->findByPk($id);
                    $t->firePublished("",$this->_current_event['eid']);
                }
            } catch (Exception $e) {
                Yii::log("Error on update template using block:". $block_id . ". With error: ".$e->getMessage(), CLogger::LEVEL_ERROR);
                return false;
            }
        }
    }


}