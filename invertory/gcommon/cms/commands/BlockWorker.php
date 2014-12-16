<?php
Yii::import("gcommon.cms.models.ObjectTerm");
Yii::import("gcommon.cms.models.Block");
Yii::import("gcommon.cms.components.CmsWorker");
class BlockWorker extends CmsWorker {
    protected $_listen_events = array(
        'object:published',
        'object:delete',
    );

    /**
     * function_description
     *
     *
     * @return
     */
    public function work() {
        if ($this->_current_event['obj_type'] == "object") {
            try {
                $object_id = $this->_current_event['obj_id'];
                $category_ids =  ObjectTerm::model()->getAncestorsIdsByObject($object_id);
                // $ids = array();
                // foreach ($category_ids as $cid) {
                //     $ids += Block::model()->getAllIdsDependentCategory($cid);

                // }
                // $ids = array_unique($ids);
                // foreach ($ids as $id) {
                //     $block = Block::model()->findByPk($id);
                //     if ($block) {
                //         $block->updateHtml();
                //     }
                // }
                $this->updateBlockHtml($category_ids);
                $termCache = $this->_current_event['info'];
                if(is_array($termCache)){
                    $to_delete = array_diff($termCache, $category_ids);
                    $this->updateBlockHtml($to_delete);
                }
                

            } catch (Exception $e) {
                Yii::log("Error on update block with content update: :". $object_id . ". With error: ".$e->getMessage(), CLogger::LEVEL_ERROR);
                return false;
            }
        }
    }
    public function updateBlockHtml($category_ids){
        $ids = array();
        foreach ($category_ids as $cid) {
            $obj =  Block::model()->getAllIdsDependentCategory($cid);
            $ids = array_merge($ids,$obj);
        }
        $blockIds = array_unique($ids);
        foreach($blockIds as $blockid){
            $block = Block::model()->findByPk($blockid);
            Yii::log("the block will updated: ".$blockid, CLogger::LEVEL_INFO);
            if ($block) {
                $block->updateHtml();
            }
        }
    }

}