<?php

namespace backend\controllers;

use Yii;
use backend\models\Storeroom;
use backend\models\search\StoreroomSearch;
use backend\components\BackendController;

class StoreroomController extends BackendController {
    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $this->render('index');
    }
    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) echo $error['message'];
            else $this->render('error', $error);
        }
    }
    /**
     * Displays the page list
     */
    public function actionList() {
        $searchModel = new StoreroomSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * Displays the create page
     */
    public function actionCreate() {
        $model = new Storeroom;
        // collect user input data
        if (isset($_POST['Storeroom'])) {
            $model->load($_POST);
            if ($model->validate()) {
                $model->save();
                Yii::$app->session->setFlash('success', '新建成功！');
                $this->redirect("/storeroom/list");
            }
        }
        return $this->render('create', array(
            'model' => $model,'isNew'=>true,
        )); 
    }
    /**
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        if (isset($_POST['Storeroom'])) {
            $model->load($_POST);
            $model->save(false);
            Yii::$app->session->setFlash('success', '修改成功！');
        }
        return $this->render('create', array(
            'model' => $model
        ));
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Storeroom::findOne($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
}
