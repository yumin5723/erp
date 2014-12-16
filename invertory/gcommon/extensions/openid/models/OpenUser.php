<?php

class OpenUser extends CActiveRecord {
    /**
     * model
     *
     * @param $className:
     *
     * @return CPUser the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * get model table name
     *
     *
     * @return string the associated database table name
     */
    public function tableName() {
        return 'openuser';
    }


    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return array();
    }

    /**
     * get behaviors
     *
     *
     * @return
     */
    public function behaviors() {
        return array(
            'CTimestampBehavior' => array(
                'class'               => 'zii.behaviors.CTimestampBehavior',
                'createAttribute'     => 'created',
                'updateAttribute'     => 'modified',
                'timestampExpression' => 'date("Y-m-d H:i:s")',
                'setUpdateOnCreate'   => true,
            ),
        );
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function relations() {
        return array();
    }

    /**
     * function_description
     *
     * @param $app_id:
     * @param $uid:
     *
     * @return
     */
    public function createNew($app_id, $uid) {
        $openuser = new self;
        $openuser->app_id = intval($app_id);
        $openuser->uid = intval($uid);
        $openuser->openid = $this->generateNewOpenid();
        $openuser->save(false);
        return $openuser;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function generateNewOpenid() {
        while (true) {
            $openid = GHelper::generateRandomString(32);
            if (self::model()->findByAttributes(array('openid'=>$openid)) == null) {
                return $openid;
            }
        }

    }


}
