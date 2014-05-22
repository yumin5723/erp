<?php
namespace console\controllers;
use Yii;
use gcommon\cms\components\CmsWorkerController;
use gcommon\cms\models\ObjectTemplate;
use gcommon\cms\models\Object;
use gcommon\cms\components\ConstantDefine;
class ObjectworkerController extends CmsWorkerController{
    protected $_listen_events = array(
        'template:published',
    );
    /**
     * function_description
     *
     *
     * @return
     */
    public function work() {
        Yii::Info("work on event: ".$this->_current_event['eid']);

        if ($this->_current_event['obj_type'] == "template") {
            try {
                $template_id = $this->_current_event['obj_id'];
                $objectTemplateModel = new ObjectTemplate;
                // get page dependent this object
                $ids = $objectTemplateModel->getAllObjectsIdByTemplateId($template_id);
                // update pages
                foreach ($ids as $id) {
                    $object = Object::findOne($id);
                    if(!empty($object) && $object->object_status == ConstantDefine::OBJECT_STATUS_PUBLISHED){
                        $object->doPublish();
                    }else{
                        Yii::info("the object is not found or it's status is not published".$id);
                    }
                    Yii::info("success published object: ".$id);
                }
            } catch (Exception $e) {
                Yii::Info("Error on update object using template:". $template_id . ". With error: ".$e->getMessage());
                return false;
            }
        }
    }
}