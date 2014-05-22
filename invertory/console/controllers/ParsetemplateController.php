<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use gcommon\cms\components\CmsTasks;
use gcommon\cms\models\Template;
class ParsetemplateController extends Controller{

	public function actionDo(){
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
    public function work($template_id) {
        $template = Template::findOne($template_id);
        if ($template) {
            try {
                return $template->parse();
            } catch (Exception $e) {
                Yii::log("Templete page .".$templete_id." error with message: ".$e->getMessage(), CLogger::LEVEL_ERROR);
                return false;
            }
        }
    }

}