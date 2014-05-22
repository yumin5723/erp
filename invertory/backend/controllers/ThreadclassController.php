<?php

namespace backend\controllers;

use Yii;
use backend\models\Forum;
use backend\models\Threadclass;
use backend\models\search\ForumSearch;
use backend\components\BackendController;

class ThreadclassController extends BackendController {
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
    public function actionAdmin() {
        $searchModel = new ForumSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * Displays the create page
     */
    public function actionAddmore($id) {
        $id = intval($id);
        $model = $this->loadModel($id);
        // collect user input data
        if (isset($_POST['Threadclass'])) {
            $model->load($_POST);
            if ($model->validate()) {
                $model->save();
                Yii::$app->session->setFlash('success', '设置成功！');
                $this->redirect("/threadclass/update/$model->fid");
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
        $id = intval($id);
        $model = new Threadclass;
        // $data = Threadclass::getAllData($id);
        return $this->render('update', array(
            'model' => $model,'id'=>$id
        )); 
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Threadclass::findOne($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
}
