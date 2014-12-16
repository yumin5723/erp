<?php

Yii::import("gcommon.cms.models.Page");
Yii::import("gcommon.cms.components.CmsWorker");
class PageWorker extends CmsWorker {
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
        if ($this->_current_event['obj_type'] == "block"){
            if(YII_DEBUG){
                Yii::log("received block event :". $this->_current_event['obj_id'], CLogger::LEVEL_INFO);
            }
            try {
                $block_id = $this->_current_event['obj_id'];
                // get page dependent this object
                $ids = Page::model()->getAllIdsDependentBlock($block_id);
                // update pages
                foreach ($ids as $id) {
                    $page = Page::model()->findByPk($id);
                    if(empty($page)){
                        Yii::log("can not find page :".$id."dependent block: ".$block_id,CLogger::LEVEL_ERROR);
                    }else{
                        $page->doPublish();
                    }
                }
            } catch (Exception $e) {
                Yii::log("Error on update page using block:". $block_id . ". With error: ".$e->getMessage(), CLogger::LEVEL_ERROR);
                return false;
            }
        }
    }


}