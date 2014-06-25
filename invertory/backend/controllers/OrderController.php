<?php

namespace backend\controllers;

use Yii;
use backend\models\Order;
use backend\models\search\OrderSearch;
use backend\models\search\OrderStockSearch;
use backend\components\BackendController;

class OrderController extends BackendController {
    public $enableCsrfValidation = true;
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
        $searchModel = new OrderSearch;
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
        $model = new Order;
        // collect user input data
        if (isset($_POST['Order'])) {
            $model->load($_POST);
                $results = $model->getCanUseGoodsByOwnerId();
                return $this->render('create', [
                    'results' => $results,
                    'model'=>$model,
                    'ischange'=>true,
                    'owner_id'=>$_POST['Order']['owner_id'],
                    'storeroom_id'=>$_POST['Order']['storeroom_id'],
                ]);
        }
        return $this->render('create', array(
            'model' => $model,'isNew'=>true,'ischange'=>false,
        )); 
    }
    /**
     * Displays the create page
     */
    public function actionCheck() {
        $model = new Order;
        // collect user input data
        if(isset($_POST['confirm_end'])){
            $model->load($_POST);
            if ($model->validate()) {
                $model->save();
                //create order detail 
                $model->createOrderDetail($_POST['OrderDetail']);
                $this->redirect("/order/list?OrderSearch[status]=0");
            }
        }
        if (isset($_POST['selection'])) {
            foreach($_POST['selection'] as $key=>$value){
                if($value['count'] == 0){
                    unset($_POST['selection'][$key]);
                }
            }
            return $this->render('checkaddress', array(
                'model' => $model,'isNew'=>true,'data'=>$_POST['selection'],'owner_id'=>$_POST['Order']['owner_id'],
                    'storeroom_id'=>$_POST['Order']['storeroom_id'],
            )); 
        }
        // var_dump($_POST);exit;
    }
    /**
     * [actionCreateorder description]
     * @return [type] [description]
     */
    public function actionCreateorder(){

    }
    /**
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = new Order;
        $id = $_GET['id'];
        if($id){
            $model = $this->loadModel($id);
            if (!empty($_POST)) {
                if ($model->load($_POST) && $model->save()) {
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
            'model' => Order::findOne($id),
        ]);
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Order::findOne($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    public function actionGetgoods(){
        $this->enableCsrfValidation = false;
        $result = Order::getCanUseGoodsByOwnerId($_POST['owner_id'],$_POST['storeroom_id']);
        echo json_encode($result);
    }
}
