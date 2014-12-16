<?php

namespace backend\controllers;

use Yii;
use backend\models\Order;
use backend\models\City;
use backend\models\OrderSign;
use backend\models\OrderPackage;
use backend\models\OrderDetail;
use backend\models\Package;
use backend\models\Upload;
use backend\models\search\OrderSearch;
use backend\models\search\OrderStockSearch;
use backend\components\BackendController;

class OrderController extends BackendController {
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
        $searchModel = new OrderSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(),Yii::$app->user->identity->storeroom_id);

        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'status'=>$_GET['OrderSearch']['status'],
        ]);
    }
    /**
     * [actionSearch description]
     * @return [type] [description]
     */
    public function actionSearch(){
        $model = new Order;
        $dataProvider = [];
        if(isset($_POST['orderid'])){
            $searchModel = new OrderSearch;
            $dataProvider = $searchModel->searchByPost($_POST['orderid'],Yii::$app->user->identity->storeroom_id);
        }
        return $this->render("search",['model'=>$model,'dataProvider'=>$dataProvider]);
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
     * [actionConfirm description]
     * @return [type] [description]
     */
    public function actionConfirm(){
        $ordersIds = $_POST['selection'];
        $orders = Order::find()->where(['id'=>$ordersIds,'is_del'=>Order::ORDER_IS_NOT_DEL])->all();
        foreach($orders as $order){
            if($order->status != Order::NEW_ORDER){
                continue;
            }else{
                $order->status = Order::CONFIRM_ORDER;
                $order->save(false);
            }
        }
        return $this->redirect('/order/list?OrderSearch[status]=4');
    }
    /**
     * [actionShipping description]
     * @return [type] [description]
     */
    public function actionShipping(){
        $ordersIds = $_POST['selection'];
        $orders = Order::find()->where(['id'=>$ordersIds,'is_del'=>Order::ORDER_IS_NOT_DEL])->all();
        foreach($orders as $order){
            if($order->status != Order::PACKAGE_ORDER){
                continue;
            }else{
                $order->status = Order::SHIPPING_ORDER;
                $order->save(false);
            }
        }
        return $this->redirect('/order/list?OrderSearch[status]=2');
    }
    /**
     * [actionSign description]
     * @return [type] [description]
     */
    public function actionSign(){
        $ordersIds = $_POST['selection'];
        $orders = Order::find()->where(['id'=>$ordersIds,'is_del'=>Order::ORDER_IS_NOT_DEL])->all();
        foreach($orders as $order){
            if($order->status != Order::SHIPPING_ORDER){
                continue;
            }else{
                $order->status = Order::SIGN_ORDER;
                $order->save(false);
            }
        }
        return $this->redirect('/order/list?OrderSearch[status]=3');
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
                $db = Order::getDb();
                $transaction = $db->beginTransaction();
                try{
                    $model->to_province = City::findOne($_POST['Order']['to_province'])->name;
                    $model->to_city = City::findOne($_POST['Order']['to_city'])->name;
                    $model->source = Order::ORDER_SOURCE_CUSTOMER;
                    $model->save();
                    $model->viewid = date('Ymd')."-".$model->id;
                    $model->update();
                    //create order detail 
                    $model->createOrderDetail($_POST['OrderDetail']);
                    $transaction->commit();
                    $this->redirect("/order/list?OrderSearch[status]=0");
                }catch (\Exception $e) {
                    $transaction->rollback();
                    throw new \Exception($e->getMessage(), $e->getCode());
                }
                
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
        $order = Order::find()->where(['id'=>$id,'is_del'=>Order::ORDER_IS_NOT_DEL])->one();
        if(Yii::$app->user->identity->storeroom_id != Order::BIGEST_STOREROOM_ID){
            if($order->storeroom_id != Yii::$app->user->identity->storeroom_id){
                throw new \Exception("Error Processing Request", 404);
            }
        }
        $order_package = OrderPackage::find()->where(['order_id'=>$id])->one();
        $detail = OrderDetail::find()->where(['order_id'=>$id])->all();
        $sign = OrderSign::findOne($id);
        $package = [];
        if(!empty($order_package)){
            $package = Package::find()->where(['id'=>$order_package->package_id])->one();
        }
        return $this->render('view', [
            'order' => $order,
            'package' => $package,
            'detail' =>$detail,
            'sign' => $sign,
        ]);
    }
    public function actionChange(){
        if(isset($_POST['orderid'])){
            $order = Order::findOne($_POST['orderid']);
            if(!empty($order)){
                $order->status = $_POST['status'];
                $order->save(false);

                //create queue to send email
                if($order->status == Order::SIGN_ORDER){
                    //Yii::$app->gqueue->createJob('send_email','gcommon\components\gqueue\workers\SendEmail',["type"=>Order::SIGN_ORDER,'id'=>$order->id]);
                }
                // if($_POST['status'] == Order::CONFIRM_ORDER){
                //     //send mail to customer
                //     Yii::$app->mail->compose('confirm',['order'=>$order])
                //          ->setFrom('liuwanglei2001@163.com')
                //          ->setTo('liuwanglei@goumin.com')
                //          ->setSubject("订单确认通知")
                //          ->send();
                // }
                // if($_POST['status'] == Order::SIGN_ORDER){
                //     Yii::$app->mail->compose('sign',['order'=>$order])
                //          ->setFrom('liuwanglei2001@163.com')
                //          ->setTo('liuwanglei@goumin.com')
                //          ->setSubject("订单签收通知")
                //          ->send();
                // }
                $this->redirect('/order/view?id='.$order->id);
            }
        }
    }
    /**
     * [actionPrint description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionPrint($id){
        $order = Order::find()->where(['id'=>$id,'is_del'=>Order::ORDER_IS_NOT_DEL])->one();
        $detail = OrderDetail::find()->where(['order_id'=>$id])->all();
        return $this->renderPartial('print', [
            'order' => $order,
            'detail' =>$detail,
        ]);
    }
    /**
     * [actionPrint description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionMarksign($id){
        $order = $this->loadModel($id);
        if($order->status != Order::SHIPPING_ORDER){
            throw new CHttpException(404, '数据错误，请检查一下订单是否是发货状态，不是发货状态的订单不能标记为签收');
        }
        if(Yii::$app->user->identity->storeroom_id != Order::BIGEST_STOREROOM_ID){
            if($order->storeroom_id != Yii::$app->user->identity->storeroom_id){
                throw new \Exception("Error Processing Request", 404);
            }
        }
        $model = new OrderSign;
        $model->order_viewid = $order->viewid;
        if(Yii::$app->request->isPost){
            $model->load($_POST);
            $model->sign_date = $_POST['sign_date-ordersign-sign_date'];
            if($model->validate()){
                if($model->save()){
                    $order->status = Order::SIGN_ORDER;
                    $order->save(false);
                    //create queue to send email
                    //Yii::$app->gqueue->createJob('send_email','gcommon\components\gqueue\workers\SendEmail',["type"=>Order::SIGN_ORDER,'id'=>$order->id]);
                }
                return $this->redirect("/order/list?OrderSearch[status]=3");
            }
        }
        return $this->render('marksign',['id'=>$id,'order'=>$order,'model'=>$model]);
    }
    public function actionMarkunsign($id){
        $order = $this->loadModel($id);
        if($order->status != Order::SHIPPING_ORDER){
            throw new CHttpException(404, '数据错误，请检查一下订单是否是发货状态，不是发货状态的订单不能标记为签收');
        }
        if(Yii::$app->user->identity->storeroom_id != Order::BIGEST_STOREROOM_ID){
            if($order->storeroom_id != Yii::$app->user->identity->storeroom_id){
                throw new \Exception("Error Processing Request", 404);
            }
        }
        $model = new OrderSign;
        $model->order_viewid = $order->viewid;
        if(Yii::$app->request->isPost){
            $model->load($_POST);
            $model->sign_date = $_POST['sign_date-ordersign-sign_date'];
            if($model->validate()){
                $model->type = OrderSign::ORDER_IS_NOT_SIGNED;
                if($model->save()){
                    $order->status = Order::UNSIGN_ORDER;
                    $order->save(false);
                }
                return $this->redirect("/order/list?OrderSearch[status]=7");
            }
        }
        return $this->render('markunsign',['id'=>$id,'order'=>$order,'model'=>$model]);
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $order = Order::find()->where(['id'=>$id,'is_del'=>Order::ORDER_IS_NOT_DEL])->one();
        if ($order === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $order;
    }
    public function actionGetgoods(){
        $this->enableCsrfValidation = false;
        $result = Order::getCanUseGoodsByOwnerId($_POST['owner_id'],$_POST['storeroom_id']);
        echo json_encode($result);
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
    public function actionCity(){
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $param1 = null;
                $param2 = null;
                if (!empty($_POST['depdrop_params'])) {
                    $params = $_POST['depdrop_params'];
                    $param1 = $params[0]; // get the value of input-type-1
                    $param2 = $params[1]; // get the value of input-type-2
                }
     
                // $out = self::getSubCatList1($cat_id, $param1, $param2); 
                // the getSubCatList1 function will query the database based on the
                // cat_id, param1, param2 and return an array like below:
                $out = City::getCityByPid($cat_id);
                // var_dump($out);exit;
                // var_dump($out);exit;
                // $out = [
                //        ['id'=>'20', 'name'=>'a'],
                //        ['id'=>'21', 'name'=>'b'],
                //        ['id'=>'22', 'name'=>'c'], 
                //        ['id'=>'23', 'name'=>'d'],
                // ];
                
                
                // $selected = self::getDefaultSubCat($cat_id);
                // the getDefaultSubCat function will query the database
                // and return the default sub cat for the cat_id
                echo json_encode(['output'=>$out, 'selected'=>$out[0]['id']]);
                return;
            }
        }
        echo json_encode(['output'=>'', 'selected'=>'']);
    }
}
