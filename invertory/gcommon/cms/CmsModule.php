<?php

namespace gcommon\cms;

class CmsModule extends \yii\base\Module
{
    public $controllerNamespace = 'gcommon\cms\controllers';
    public $domain;
    public function init()
    {
        parent::init();
        // $this->setAliases(['@cms'=>'@gcommon/cms']);
        // custom initialization code goes here
    }
	// /**
	//  * @return string the base URL that contains all published asset files of gii.
	//  */
	// public function getAssetsUrl()
	// {
	// 	if($this->_assetsUrl===null)
	// 		$this->_assetsUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('gcommon.assets'));
	// 		$this->_assetsUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('cms.assets'));
	// 	return $this->_assetsUrl;
	// }

	// /**
	//  * @param string $value the base URL that contains all published asset files of gii.
	//  */
	// public function setAssetsUrl($value)
	// {
	// 	$this->_assetsUrl=$value;
	// }
	// 

}
