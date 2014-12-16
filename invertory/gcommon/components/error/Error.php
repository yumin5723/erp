<?php

Yii::import("gcommon.components.error.IError");

class Error implements IError {
    protected $_code = null;

    protected $_message = null;

    function __construct($code, $message=null) {
        $this->_code = intval($code);
        if (!is_null($message)) {
            $this->_message = (string)$message;
        }
    }

    /**
     * get error code
     *
     *
     * @return int
     */
    public function getcode() {
        return $this->_code;
    }

    /**
     * __toString
     *
     *
     * @return string
     */
    public function __toString() {
        if (!empty($this->_message)) {
            return sprintf("Error %d: %s", $this->getcode(), $this->_message);
        } else {
            return sprintf("Unknow error %d", $this->getcode());
        }
    }



}