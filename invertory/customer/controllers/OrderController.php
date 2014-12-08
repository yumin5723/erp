<?php

namespace customer\controllers;

use Yii;
use backend\models\Order;
use backend\models\OrderSign;
use customer\models\Stock;
use customer\models\StockTotal;
use customer\models\OrderPackage;
use backend\models\OrderDetail;
use backend\models\Package;
use backend\models\Owner;
use backend\models\Storeroom;
use backend\models\Material;
use customer\models\search\OrderSearch;
use customer\models\search\OrderStockSearch;
use customer\components\CustomerController;

class OrderController extends CustomerController {
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
     * [actionSearch description]
     * @return [type] [description]
     */
    public function actionSearch(){
        $model = new Order;
        $dataProvider = [];
        if(isset($_POST['orderid'])){
            $searchModel = new OrderSearch;
            $dataProvider = $searchModel->searchByPost($_POST['orderid']);
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
     * Displays the create page
     */
    public function actionRevoke($id) {
        if($id){
            $model = $this->loadModel($id);
            if($model->status == Order::REFUSE_ORDER){
                $model->status = Order::REVOKE_ORDER;
                $db = Order::getDb();
                $transaction = $db->beginTransaction();
                try{
                    // if($model->save()){
                        $model->save();
                        //Recovery inventory
                        //delete stock about this order id the recovery stock total
                        Stock::deleteAll(['order_id'=>$id]);
                        $details = OrderDetail::find()->where(['order_id'=>$id])->all();
                        if(!empty($details)){
                            foreach($details as $detail){
                                $storeroom_id = Order::findOne($id)->storeroom_id;
                                $material_id = Material::find()->where(['code'=>$detail->goods_code])->one()->id;
                                $total = StockTotal::find(['storeroom_id'=>$storeroom_id,'material_id'=>$material_id])->one();
                                if(!empty($total)){
                                    $total->total = $total->total + $detail->goods_quantity;
                                    if(!$total->update()){
                                        throw new \Exception("Error Processing Request", 1);
                                    }
                                }
                            }
                        }
                    // }
                    $transaction->commit();
                }catch (\Exception $e) {
                    $transaction->rollback();
                    throw new \Exception($e->getMessage(), $e->getCode());
                }
                
            }
        }
        return $this->redirect(Yii::$app->request->getReferrer());
    }
    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $order = Order::find()->where(['id'=>$id,'is_del'=>Order::ORDER_IS_NOT_DEL])->one();
        $order_package = OrderPackage::find()->where(['order_id'=>$id])->one();
        $detail = OrderDetail::find()->where(['order_id'=>$id])->all();
        $package = [];
        $sign = OrderSign::findOne($id);
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
    public function actionImport(){
        $model = new Order;
        $error = [];
        $right = false;
        if(isset($_POST['Order']) && $_POST['Order'] != ""){
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel = \PHPExcel_IOFactory::load($_FILES["Order"]["tmp_name"]['file']);
            $datas = $objPHPExcel->getSheet(0)->toArray();
            $ret = [];
            // $datas = [
            //     ['序号','收件人','收件地址','收件人电话','收件城市','活动','发货仓库','物料编码','物料属主','数量','到货需求','备注'],
            //     ['1','','','','','','北京中央库','JIHFSN899011','alisa','20','4小时','must be'],
            //     ['1','wanglei','beijing office','13800138000','beijing','2014 word cup','北京中央库','GSDGSG99990SGA','alisa','5','4小时','must be'],
            //     ['2','lisi','beijing office','13800138000','beijing','2014 word cup','北京中央库','JIHFSN899011','alisa','40','4小时','must be'],
            // ];
            foreach($datas as $key=>$data){
                if($key == 0){
                    continue;
                }else{
                    $ret[$data[0]]['recipients'] = $data[1];
                    $ret[$data[0]]['recipients_address'] = $data[2];  
                    $ret[$data[0]]['recipients_contact'] = $data[3];  
                    $ret[$data[0]]['to_city'] = $data[4];
                    $ret[$data[0]]['goods_active'] = $data[5];
                    $ret[$data[0]]['storeroom_id'] = $data[6];
                    $ret[$data[0]]['goods'][$data[7]] = $data[9];
                    // $ret[$data[0]]['goods']['count'][] = $data[9];
                    $ret[$data[0]]['owner_id'] = $data[8];
                    $ret[$data[0]]['limitday'] = $data[10];
                    $ret[$data[0]]['info'] = $data[11];
                }
            }
            $error = $this->checkOrderRight($ret);
            if(!empty($error)){
                if(!isset($error['owner_error'])){
                    $error['owner_error'] = [];
                }
                if(!isset($error['material_error'])){
                    $error['material_error'] = [];
                }
                if(!isset($error['storeroom_error'])){
                    $error['storeroom_error'] = [];
                }
                if(!isset($error['total_error'])){
                    $error['total_error'] = [];
                }
            }else{
                if($this->createBatchOrder($ret)){
                    $right = true;
                }else{
                    $right = false;
                }
                
            }
        }
        return $this->render('import',['model'=>$model,'error'=>$error,'right'=>$right]);
    }
    /**
     * [checkOrderRight description]
     * @param  [type] $result [description]
     * @return [type]         [description]
     */
    public function checkOrderRight($orderArray){
        $error = [];
        $count = [];
        $num = 0;
        foreach($orderArray as $value){
            $storeroom_id = $value['storeroom_id'];
            
            $storeroom = Storeroom::find()->where(['name'=>trim($value['storeroom_id'])])->one();
            $owner = Owner::find()->where(['english_name'=>$value['owner_id']])->one();
            if(empty($storeroom)){
                $error['storeroom_error'][$value['storeroom_id']] = $value['storeroom_id'];
            }elseif(empty($owner)){
                $error['owner_error'][$value['owner_id']] = $value['owner_id'];
            }else{
                foreach($value['goods'] as $key=>$v){
                    $material = Material::find()->where(['code'=>$key])->one();
                    if(empty($material)){
                        $error['material_error'][$key] = $key; 
                    }else{
                        if(isset($count[$key])){
                            $count[$key] += $v;
                        }else{
                            $count[$key] = $v;
                        }
                    }
                }
                foreach($count as $k=>$v1){
                    $material = Material::find()->where(['code'=>$k])->one();
                    $stock_total = StockTotal::find()->where(['storeroom_id'=>$storeroom->id,'material_id'=>$material->id])->one();
                    if($stock_total->total < $v1){
                        $error['total_error'][$k] = $k;
                    }
                }
            }
        }
        return $error;
    }
    /**
     * [createOrder description]
     * @param  [type] $orderArray [description]
     * @return [type]             [description]
     */
    protected function createBatchOrder($orderArray){
        $db = Owner::getDb();
        $transaction = $db->beginTransaction();
        try {
            foreach($orderArray as $value){
                $storeroom = Storeroom::find()->where(['name'=>trim($value['storeroom_id'])])->one();
                $owner = Owner::find()->where(['english_name'=>$value['owner_id']])->one();
                //create order
                $model = new Order;
                $model->goods_active = $value['goods_active'];
                $model->storeroom_id = $storeroom->id;
                $model->owner_id = $owner->id;
                $model->to_city = $value['to_city'];
                $model->recipients = $value['recipients'];
                $model->recipients_address = $value['recipients_address'];
                $model->recipients_contact = $value['recipients_contact'];
                $model->info = $value['info'];
                $model->limitday = $value['limitday'];
                $model->created = date('Y-m-d H:i:s');
                $model->created_uid = Yii::$app->user->id;
                $model->source = Order::ORDER_SOURCE_CUSTOMER;
                $model->save(false);
                $model->viewid = date('Ymd')."-".$model->id;
                $model->update();

                foreach($value['goods'] as $key=>$v){
                    $detail = new OrderDetail;
                    $detail->order_id = $model->id;
                    $detail->goods_code = $key;
                    $detail->goods_quantity = $v;
                    $detail->save();
                }
                //Subtract stock
                foreach($value['goods'] as $key=>$v){
                    $material = Material::find()->where(['code'=>$key])->one();
                    $stock = new Stock;
                    $stock->material_id = $material->id;
                    $stock->storeroom_id = $model->storeroom_id;
                    $stock->owner_id = $model->owner_id;
                    $stock->project_id = $material->project_id;
                    $stock->actual_quantity = 0 - $v;
                    $stock->stock_time = date('Y-m-d H:i:s');
                    $stock->created = date('Y-m-d H:i:s');
                    $stock->increase = Stock::IS_NOT_INCREASE;
                    $stock->order_id = $model->id;
                    $stock->active = $model->goods_active;
                    $stock->save(false);

                    //subtract stock total
                    // StockTotal::updateTotal($model->storeroom_id,$material->id,(0 - $v));
                    $stockTotal = StockTotal::find()->where(['material_id'=>$material->id,"storeroom_id"=>$model->storeroom_id])->one();
                    $stockTotal->total = $stockTotal->total + (0 - $v);
                    $stockTotal->update();
                }
            }
            //create order detail
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
    public function actionDownload(){
        
    }
}