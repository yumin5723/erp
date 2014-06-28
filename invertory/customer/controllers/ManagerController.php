<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use backend\models\search\ManagerSearch;
use backend\models\LoginForm;
use backend\models\Manager;
use backend\components\BackendController;

class ManagerController extends BackendController{


    public function actionAdmin()
    {
        $searchModel = new ManagerSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionCreate()
    {
        $model = new Manager;
        $model->setScenario('signup');
        if($model->load($_POST) && $model->save()){
            return $this->redirect("/manager/admin");
        }
        return $this->render('create',['model'=>$model]);
    }

    public function actionDelete(){
        $model = new Manager();
        $id = $_GET['id'];
        $model->deleteUser($id);
        return $this->redirect("/manager/admin");
    }

    public function actionUpdate(){
        $model = new Manager;
        $id = $_GET['id'];
        if($id){
            $model = $this->loadModel($id);
            if (!empty($_POST)) {
                if ($model->updateAttrs($_POST['Manager'])) {
                    return $this->redirect(Yii::$app->request->getReferrer());
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
        $model = Manager::findOne(['id'=>$id]);
        if ($model === null) { return false;}
        return $model;
    }
}