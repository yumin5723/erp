<?php
Yii::import("gcommon.cms.models.Templete");
Yii::import("gcommon.cms.components.CmsTasks");
class ParseTempleteWorker extends CConsoleCommand {
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
        $task_queue->addParseTempleteWorker($this);
    }

    /**
     * function_description
     *
     * @param $page_id:
     *
     * @return
     */
    public function work($templete_id) {
        $templete = Templete::model()->findByPk($templete_id);
        if ($templete) {
            try {
                return $templete->parse();
            } catch (Exception $e) {
                Yii::log("Templete page .".$templete_id." error with message: ".$e->getMessage(), CLogger::LEVEL_ERROR);
                return false;
            }
        }
    }


}