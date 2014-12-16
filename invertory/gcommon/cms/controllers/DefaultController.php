<?php
namespace gcommon\cms\controllers;

use yii\web\Controller;
use gcommon\cms\goumindata\Health;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $model = new Health;
        $result = $model->getDepartmentInfo();
    }

	/**
     * function_description
     *
     *
     * @return
     */
    public function filters() {

        return array(
            'accessControl'
        );
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     *
     * @return array access control rules
     */
    public function accessRules() {

        return array(
            array(
                'allow',
                'actions' => array(
                    'index'
                ) ,
                'users' => array(
                    '@'
                ) ,
            ) ,
            array(
                'deny', // deny all users
                'users' => array(
                    '*'
                ) ,
            ) ,
        );
    }
}
