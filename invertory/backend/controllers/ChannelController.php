<?php

namespace backend\controllers;

use Yii;
use backend\models\Channel;
use backend\models\search\ChannelSearch;
use backend\components\BackendController;
use backend\models\Upload;

class ChannelController extends BackendController {
    public $enableCsrfValidation;
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
        $searchModel = new ChannelSearch;
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
        $model = new Channel;
        // collect user input data
        if (isset($_POST['Channel'])) {
            $model->load($_POST);
            if ($model->validate()) {
                $model->save();
                $model->saveOrderNumber($_POST['order_number']);
                Yii::$app->session->setFlash('success', '新建成功！');
                $this->redirect("/channel/list");
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
        $model = new Channel;
        $id = $_GET['id'];
        if($id){
            $model = $this->loadModel($id);
            if (!empty($_POST)) {
                if ($model->load($_POST) && $model->save()) {
                    $model->saveOrderNumber($_POST['order_number']);
                    Yii::$app->session->setFlash('success', '修改成功!');
                    return $this->redirect(Yii::$app->request->getReferrer());
                }
            }
        }
        return $this->render('create',['model'=>$model,'isNew'=>false]);
    }
    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => Channel::findOne($id),
        ]);
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Channel::findOne($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    public function actionUploadfile(){
        $this->enableCsrfValidation = false;
        $num = $_POST['num'];
        // print_r($_FILES);exit;
        if($_FILES){
            $model = new Upload;
            $result = $model->uploadImage($_FILES,false,"picture");
            if($result[0] == true){
                    echo <<<EOF
            <script>parent.stopSend("{$num}","{$result[1]}");</script>
EOF;
            }else{
                echo <<<EOF
            <script>alert("{$result[1]}");</script>
EOF;
            }
        }
    }
}
