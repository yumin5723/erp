<?php

namespace backend\controllers;

use Yii;
use backend\models\Apk;
use backend\models\search\ApkSearch;
use backend\components\BackendController;

class ApkController extends BackendController {
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
        $searchModel = new ApkSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * Displays the create page
     */
    public function actionCreate() {
        $model = new Apk;
        // collect user input data
        if (isset($_POST['Apk'])) {
            $model->load($_POST);
            if ($model->validate()) {
                if ($model->saveRarFile($_FILES)) {
                    $model->save();
                    Yii::$app->session->setFlash('success', '新建成功！');
                    $this->redirect("/apk/create");
                }
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
        if (isset($_POST['Apk'])) {
            $model->load($_POST);
            if(!empty($_FILES['Apk']['name']['upload'])){
                $model->saveRarFile($_FILES);
            }
            $model->save(false);
            Yii::$app->session->setFlash('success', '修改成功！');
            $this->redirect("/apk/admin");
        }
        return $this->render('create', array(
            'model' => $model
        ));
    }
    /**
     * push rar file
     */
    public function actionPublish($id) {
        $page = $this->loadModel($id);
        if($page->doPublish()){
            Yii::$app->session->setFlash('success', '发布成功！');
        }else{
            Yii::$app->session->setFlash('success', '发布失败请重新尝试!');
        }
        $this->redirect("/cms/page/admin");
    }
    /**
     * preview page for admin user
     */
    public function actionPreview($id){
        $page = $this->loadModel($id);
        echo $page->display();
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Apk::findOne($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
}
