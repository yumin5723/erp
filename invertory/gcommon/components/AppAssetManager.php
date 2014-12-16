<?php

class AppAssetManager extends CApplicationComponent {
    public $asset_dir = "assets";

    protected $_assetsUrl;

    protected $_assetsPath;

    /**
     * @return string the base URL that contains all published asset files of gii.
     */
    public function getUrl()
    {
        if($this->_assetsUrl===null) {
            if (strpos($this->asset_dir, "/") === 0) {
                $this->_assetsPath = $this->asset_dir;
            } else {
                $this->_assetsPath = Yii::getPathOfAlias("application.".$this->asset_dir);
            }
            $this->_assetsUrl=Yii::app()->getAssetManager()->publish($this->_assetsPath);
        }
        return $this->_assetsUrl;
    }

}