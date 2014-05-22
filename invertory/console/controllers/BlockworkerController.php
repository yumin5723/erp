<?php
namespace console\controllers;
use Yii;
use gcommon\cms\components\CmsWorkerController;
use gcommon\cms\models\ObjectTerm;
use gcommon\cms\models\Block;
class BlockworkerController extends CmsWorkerController{
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
                $objectTermMapper = new ObjectTerm;
                $category_ids =  $objectTermMapper->getAncestorsIdsByObject($object_id);
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
        	$blockModel = new Block;
            $obj =  $blockModel->getAllIdsDependentCategory($cid);
            $ids = array_merge($ids,$obj);
        }
        $blockIds = array_unique($ids);
        foreach($blockIds as $blockid){
            $block = Block::findOne($blockid);
            Yii::Info("the block will updated: ".$blockid);
            if ($block) {
                $block->updateHtml();
            }
        }
    }
}