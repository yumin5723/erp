<?php

namespace backend\controllers;

use Yii;
use backend\models\Package;
use backend\models\Order;
use backend\components\BackendController;

class PackageController extends BackendController {
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
    public function actionOperate($id){
        $order = Order::findOne($id);
        if ($order === null) throw new CHttpException(404, 'The requested page does not exist.');
        //todo if order status
        $model = new Package;
        // collect user input data
        if (isset($_POST['Package'])) {
            $model->load($_POST);
            if ($model->validate() && $model->save()) {
                $model->saveOrderPackage();
                Yii::$app->session->setFlash('success', '新建成功！');
                $this->redirect("/package/view?id=".$model->id);
            }
        }
        return $this->render('create', array(
            'model' => $model,'isNew'=>true,'order'=>[$id],
        )); 
    }
    public function actionMultiple(){
        if(Yii::$app->request->isPost){
            $orders = $_POST['selection'];
            return $this->render('create', array(
                'model' => new Package,'isNew'=>true,'order'=>$orders
            ));  
        }
    }
    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->loadModel($id),
        ]);
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Package::findOne($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
}
