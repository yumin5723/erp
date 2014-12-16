<?php
Yii::import("gcommon.cms.models.Object");
Yii::import("gcommon.cms.components.CmsTasks");
Yii::import("gcommon.cms.components.ConstantDefine");
class BatchContentPublishWorker extends CConsoleCommand {
    /**
     * function_description
     *
     * @param $args:
     *
     * @return
     */
    public function run($args) {
        Yii::getLogger()->autoFlush = 1;
        Yii::getLogger()->autoDump = true;
        $task_queue = new CmsTasks();
        $task_queue->addBatchContentPublishWorker($this);
    }

    /**
     * function_description
     *
     * @param $content_ids serilized
     *
     * @return
     */
    public function work($content_ids) {
        $content_ids = unserialize($content_ids);
        if (!empty($content_ids) && is_array($content_ids)) {
            foreach($content_ids as $content_id){
                if(!is_numeric($content_id)){
                    continue;
                }
                try {
                    $content = Object::model()->findByPk($content_id);
                    if($content && $content->object_status == ConstantDefine::OBJECT_STATUS_PUBLISHED){
                        $content->doPublish();
                    }
                } catch (Exception $e) {
                     Yii::log("Templete page .".$content_id." error with message: ".$e->getMessage(), CLogger::LEVEL_ERROR);
                    return false;
                }
            }
            
        }
    }


}