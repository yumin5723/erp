<?php
namespace gcommon\cms\components;
use gcommon\components\TaskQueue;

class CmsTasks extends TaskQueue {

    protected $_prefix= 'cms.';

    /**
     * function_description
     *
     *
     * @return
     */
    public function getParsePageKey() {
        return $this->_prefix."parse.page";
    }

    /**
     * add parse page task to queue
     *
     *
     * @return
     */
    public function parsePage($page_id) {
        return $this->addTask($this->getParsePageKey(),$page_id);
    }


    /**
     * get page need to parse
     *
     * @return
     */
    public function addParsePageWorker($worker) {
        $this->register_worker($this->getParsePageKey(),array($worker, 'work'));
    }

        /**
     * function_description
     *
     *
     * @return
     */
    public function getParseTempleteKey() {
        return $this->_prefix."parse.templete";
    }

    /**
     * add parse templete task to queue
     *
     *
     * @return
     */
    public function parseTemplete($templete_id) {
        return $this->addTask($this->getParseTempleteKey(),$templete_id);
    }


    /**
     * get page need to parse
     *
     * @return
     */
    public function addParseTempleteWorker($worker) {
        $this->register_worker($this->getParseTempleteKey(),array($worker, 'work'));
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getBatchContentPublishKey() {
        return $this->_prefix."publish.batchcontent";
    }

    /**
     *
     * @param array content ids 
     * @return
     */
    public function batchContentPublish($content_ids) {
        return $this->addTask($this->getBatchContentPublishKey(),serialize($content_ids));
    }


    /**
     *
     */
    public function addBatchContentPublishWorker($worker) {
        $this->register_worker($this->getBatchContentPublishKey(),array($worker, 'work'));
    }

}