<?php

Yii::import("gcommon.cms.models.ObjecteTemplete");
Yii::import("gcommon.cms.models.Object");
Yii::import("gcommon.cms.components.CmsWorker");
class ObjectWorker extends CmsWorker {
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
        Yii::log("work on event: ".$this->_current_event['eid'], CLogger::LEVEL_INFO);

        if ($this->_current_event['obj_type'] == "template") {
            try {
                $template_id = $this->_current_event['obj_id'];
                // get page dependent this object
                $ids = ObjectTemplete::model()->getAllObjectsIdByTemplateId($template_id);
                // update pages
                foreach ($ids as $id) {
                    $object = Object::model()->findByPk($id);
                    if(!empty($object) && $object->object_status == ConstantDefine::OBJECT_STATUS_PUBLISHED){
                        $object->doPublish();
                    }else{
                        Yii::log("the object is not found or it's status is not published".$id, CLogger::LEVEL_INFO);
                    }
                    Yii::log("success published object: ".$id, CLogger::LEVEL_INFO);
                }
            } catch (Exception $e) {
                Yii::log("Error on update object using template:". $template_id . ". With error: ".$e->getMessage(), CLogger::LEVEL_ERROR);
                return false;
            }
        }
    }


}