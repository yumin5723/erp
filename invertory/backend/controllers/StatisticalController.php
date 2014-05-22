<?php
namespace backend\controllers;

use Yii;
use backend\models\Statistical;
use backend\models\StatOperation;
use backend\models\StatElectricity;
use backend\components\BackendController;

class StatisticalController extends BackendController{
    public function actionOperations(){
        $model = new StatOperation;
        $data = $model->getAllOperationData();
        return $this->render("index_o",['data'=>$data]);
    }

    public function actionElectricity(){
        $model = new StatElectricity;
        $data = $model->getAllElectricityData();
        return $this->render("index_e",['data'=>$data]);
    }
}
