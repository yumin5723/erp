<?php
namespace gcommon\cms\components;
use Yii;
class UploadFile {
    // const ADMIN_STATIC_FOLDER = 'page';
    protected $_adminFolder;
    protected $_origin = null;
    protected $_origin_file_name = null;
    protected $_saved = false;
    protected $_base_path;
    protected $_uri = null;
    protected $_extension = null;

    /**
     * function_description
     *
     * @param $origin_file:
     *
     * @return
     */
    public function __construct($origin_file,$real_file,$adminFolder) {
        $this->_adminFolder = $adminFolder;
        $this->_origin = $origin_file;
        $this->_base_path = Yii::$app->params['resource_folder'];
        $position = strrpos($real_file,".");
        
        $this->_origin_file_name = substr($real_file,0,$position);
        $extend = pathinfo($real_file);
        $extend = strtolower($extend["extension"]);
        $this->_extension = $extend;
        
        $this->_save_file();
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function get_file_uri() {
        if (!$this->_saved) {
            $this->_save_file();
        }

        return $this->_uri;
    }

    /**
     * function_description
     *
     * @param $file_path:
     *
     * @return
     */
    protected function check_dir($full_path) {
        $dir_path = dirname($full_path);
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777, true);
        }
        return true;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function _save_file() {
        $file_path = $this->generate_filename();
        $full_path = $this->_base_path.'/'.$file_path;
        $this->check_dir($full_path);
        //mv file
        copy($this->_origin, $full_path);
        $this->_uri = $file_path;
        return $this->_uri;
    }
    /**
     * function_description
     *
     *
     * @return
     */
    protected function generate_filename() {
        return $this->_adminFolder.'/'.date('Y').'/'.date('m').'/'.date('d').'/'.$this->_origin_file_name."_".date("YmdHis").'.'."$this->_extension";
    }

}