<?php

namespace customer\components;

use Yii;
use yii\web\Controller;

class CustomerController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \customer\components\filters\AccessControl::className(),
            ],
        ];
    }
}