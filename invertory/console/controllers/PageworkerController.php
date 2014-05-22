<?php
namespace console\controllers;
use Yii;
use gcommon\cms\components\CmsWorkerController;
use gcommon\cms\models\Page;
class PageworkerController extends CmsWorkerController{
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
                Yii::Info("received block event :". $this->_current_event['obj_id']);
            }
            try {
                $block_id = $this->_current_event['obj_id'];
                // get page dependent this object
                $pageModel = new Page;
                $ids = $pageModel->getAllIdsDependentBlock($block_id);
                // update pages
                foreach ($ids as $id) {
                    $page = Page::findOne($id);
                    if(empty($page)){
                        Yii::error("can not find page :".$id."dependent block: ".$block_id);
                    }else{
                        $page->doPublish();
                    }
                }
            } catch (Exception $e) {
                Yii::error("Error on update page using block:". $block_id . ". With error: ".$e->getMessage());
                return false;
            }
        }
    }
}