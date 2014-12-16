<?php
Yii::import("gcommon.cms.models.Oterm");
Yii::import("gcommon.cms.components.CmsWorker");
class OtermWorker extends CmsWorker {
    protected $_listen_events = array(
        'template:published',
        'object:published',
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
                // get oterms which dependence this template 
                $ids = Oterm::model()->getAllTermIdsByTemplateId($template_id);
                // update oterm pages
                foreach ($ids as $id) {
                    $this->updateOtermData($id);
                }
            } catch (Exception $e) {
                Yii::log("Error on update object using template:". $template_id . ". With error: ".$e->getMessage(), CLogger::LEVEL_ERROR);
                return false;
            }
        }
        if ($this->_current_event['obj_type'] == "object") {
            try {
                $object_id = $this->_current_event['obj_id'];
                // get include this object terms
                $category_ids =  ObjectTerm::model()->getAncestorsIdsByObject($object_id);
                // update pages
                foreach ($category_ids as $category_id) {
                    $this->updateOtermData($category_id);
                }
                //update delete term object data
                $termCache = $this->_current_event['info'];
                if(is_array($termCache)){
                    $to_delete = array_diff($termCache, $category_ids);
                    foreach($to_delete as $term_id){
                        $this->updateOtermData($term_id);
                    }
                }

            } catch (Exception $e) {
                Yii::log("Error on update object using template:". $object_id . ". With error: ".$e->getMessage(), CLogger::LEVEL_ERROR);
                return false;
            }
        }
    }
    public function updateOtermData($term_id){
        $oterm = Oterm::model()->findByPk($term_id);
        if(!empty($oterm) && $oterm->template_id !=0 && $oterm->status == Oterm::STATUS_PUBLISHED){
            $oterm->doPublish();
        }else{
            Yii::log("the oterm is not found or it's status is not published".$term_id, CLogger::LEVEL_INFO);
        }
        Yii::log("success published term: ".$term_id, CLogger::LEVEL_INFO);
    }

}