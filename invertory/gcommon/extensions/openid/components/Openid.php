<?php


class Openid extends CApplicationComponent {

    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
        if (Yii::getPathOfAlias('openid') === false) {
            Yii::setPathOfAlias('openid', realpath(dirname(__FILE__)."/.."));
        }
        Yii::import("openid.models.OpenUser");

        parent::init();
    }

    /**
     * get user openid
     * if openuser not exists, create new
     *
     * @param $app_id:
     * @param $user_id:
     *
     * @return
     */
    public function getUserOpenidForApp($app_id, $user_id) {
        // find from db
        $openuser = OpenUser::model()->findByPk(array('app_id'=>$app_id,'uid'=>$user_id));

        if (empty($openuser)) {
            $openuser = OpenUser::model()->createNew($app_id, $user_id);
        }

        return $openuser->openid;

    }



}
