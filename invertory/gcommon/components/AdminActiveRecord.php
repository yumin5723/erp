<?php

class AdminActiveRecord extends CActiveRecord {
    protected $connectionID = "adminDb";

    public static $db;

    public function getDbConnection() {
		if(self::$db!=null)
			return self::$db;
		else if((self::$db=Yii::app()->getComponent($this->connectionID)) instanceof CDbConnection)
            return self::$db;
		else
			throw new CException(Yii::t('yii','AdminActiveRecord connectionID "{id}" is invalid. Please make sure it refers to the ID of a CDbConnection application component.',
				array('{id}'=>$this->connectionID)));
    }

}