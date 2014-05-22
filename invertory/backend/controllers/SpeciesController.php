<?php
namespace backend\controllers;

use Yii;
use backend\models\Species;
use backend\components\BackendController;

class SpeciesController extends BackendController{

    public function actionAdmin(){
        $model = new Species;
        $model->getAllSpeciesTitle();
        return $this->render('admin',['model'=>$model]);
    }


    public function actionCreate()
    {
        $model = new Species;
        if($model->load($_POST)){
            if($model->validate() && $model->save()){
                return $this->redirect("/species/admin");
            }
        }
        return $this->render('create',['model'=>$model]);
    }
    

    public function actionDelete(){
        $model = new Species();
        $id = $_GET['id'];
        $model->deleteSpe($id);
        return $this->redirect("/species/admin");
    }


    public function actionUpdate(){
        $model = new Species;
        $id = $_GET['id'];
        if($id){
            $model = $this->loadModel($id);
            if (!empty($_POST)) {
                if ($model->updateAttrs($_POST['Species'])) {
                    return $this->redirect("/species/admin");
                }
            }
        }
        return $this->render('create',['model'=>$model]);
    }

    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Species::findOne($id);
        if ($model === null) { return false;}
        return $model;
    }
}
