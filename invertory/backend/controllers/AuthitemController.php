<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
class AuthitemController extends Controller{
     public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['manage','index','assign','assignments'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(){
        return $this->render("index");
    }
    
}