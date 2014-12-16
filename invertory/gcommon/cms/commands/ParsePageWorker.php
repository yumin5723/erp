<?php
Yii::import("gcommon.cms.models.Page");
Yii::import("gcommon.cms.components.CmsTasks");
class ParsePageWorker extends CConsoleCommand {
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
        $task_queue->addParsePageWorker($this);
    }

    /**
     * function_description
     *
     * @param $page_id:
     *
     * @return
     */
    public function work($page_id) {
        $page = Page::model()->findByPk($page_id);
        if ($page) {
            try {
                return $page->parse();
            } catch (Exception $e) {
                 Yii::log("Templete page .".$templete_id." error with message: ".$e->getMessage(), CLogger::LEVEL_ERROR);
                return false;
            }
        }
    }


}