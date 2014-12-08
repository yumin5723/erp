<?php

namespace backend\controllers;

use Yii;
use backend\models\Package;
use backend\models\search\PackageSearch;
use backend\models\Order;
use backend\models\OrderPackage;
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
    /**
     * Displays the page list
     */
    public function actionList() {
        $searchModel = new PackageSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    /**
     * [actionOperate description]
     * @return [type] [description]
     */
    public function actionOperate(){
        $id = isset($_GET['id']) ? $_GET['id'] : "";
        if($id){
            $order = Order::findOne($id);
            if ($order === null || (Yii::$app->user->identity->storeroom_id != Order::BIGEST_STOREROOM_ID && $order->storeroom_id != Yii::$app->user->identity->storeroom_id)){
                throw new \Exception("Error Processing Request", 404);
            }
        }
        //todo if order status
        $model = new Package;
        // collect user input data
        if (isset($_POST['Package'])) {
            $model->load($_POST);
            if ($model->validate() && $model->save()) {
                $model->saveOrderPackage();
                Yii::$app->session->setFlash('success', '新建成功！');
                $this->redirect("/order/list?OrderSearch[status]=1");
            }
        }
        return $this->render('create', array(
            'model' => $model,'isNew'=>true,'order'=>[$order],
        )); 
    }
    /**
     * [actionOperate description]
     * @return [type] [description]
     */
    public function actionUpdate($id){
        // $order = Order::findOne($id);
        $package = OrderPackage::find()->where(['order_id'=>$id])->one();
        if(!empty($package)){
            $model = $this->loadModel($package->package_id);
        }
        $ret = [];
        foreach($model->orders as $v){
            $ret[] = Order::findOne($v->order_id);
        }
        // collect user input data
        if (isset($_POST['Package'])) {
            $model->load($_POST);
            if ($model->validate() && $model->save()) {
                Yii::$app->session->setFlash('success', '修改成功！');
                $this->redirect("/order/list?OrderSearch[status]=1");
            }
        }
        return $this->render('create', array(
            'model' => $model,'isNew'=>true,'order'=>$ret,
        )); 
    }
    public function actionMultiple(){
        if(Yii::$app->request->isPost){
            $ordersIds = $_POST['selection'];
            $orders = Order::find()->where(['id'=>$ordersIds,'is_del'=>Order::ORDER_IS_NOT_DEL])->all();
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
