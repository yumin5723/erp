<?php

class ReadSession extends CApplicationComponent {

    /**
     * session save type: file/db/cache...
     *
     */
    public $session_save_type = "file";

    /**
     * session save path if session_save_type is file
     *
     */
    public $session_path = "/tmp/";

    public $stateKeyPrefix = "";

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
        parent::init();
    }

    /**
     * read session by session_id
     *
     * @param $session_id:
     *
     * @return array SESSION
     */
    public function getSessionById($session_id) {
        $s_str = $this->getSessionString($session_id);
        if ($s_str) {
            return $this->decode_session($s_str);
        }
    }

    /**
     * decode session and keep current session
     *
     *
     * @return array SESSION
     */
    protected function decode_session($session_string){
        session_start();
        $current_session = session_encode();
        foreach ($_SESSION as $key => $value){
            unset($_SESSION[$key]);
        }
        session_decode($session_string);
        $restored_session = $_SESSION;
        foreach ($_SESSION as $key => $value){
            unset($_SESSION[$key]);
        }
        session_decode($current_session);
        return $restored_session;
    }

    /**
     * get session string
     *
     * @param $session_id:
     *
     * @return
     */
    protected function getSessionString($session_id) {
        switch ($this->session_save_type) {
            case 'file':
                return $this->readSessionFile($session_id);
            default:
                return null;
        }

    }

    /**
     * function_description
     *
     * @param $session_id:
     *
     * @return
     */
    protected function readSessionFile($session_id) {
        $filename = $this->session_path."sess_".$session_id;
        if (is_file($filename)) {
            return file_get_contents($filename);
        }
    }






}