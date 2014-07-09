<?php

namespace backend\controllers;

use Yii;
use backend\models\Stock;
use backend\models\Material;
use backend\models\StockTotal;
use backend\models\search\StockSearch;
use backend\components\BackendController;
use backend\models\Upload;

class StockController extends BackendController {
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
        $searchModel = new StockSearch;
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
        $model = new Stock;
        // collect user input data
        if (isset($_POST['Stock'])) {
            $model->load($_POST);
            if ($model->validate()) {
                $model->save();
                //create a data in stock total
                StockTotal::updateTotal($model->storeroom_id,$model->material_id,$model->actual_quantity);
                Yii::$app->session->setFlash('success', '新建成功！');
                $this->redirect("/stock/list");
            }
        }
        return $this->render('create', array(
            'model' => $model,'isNew'=>true,
        )); 
    }
    public function actionExport(){
        if(isset($_GET['mid']) && $_GET['mid'] != 0){
            $material_id = $_GET['mid'];
            $filename = Material::findOne($material_id)->name;
            $stocks = Stock::find()->where(['material_id'=>$material_id])->orderby(['id'=>SORT_DESC])->all();
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1','出入库标识')
                        ->setCellValue('B1','物料')
                        ->setCellValue('C1','仓库')
                        ->setCellValue('D1','所属人')
                        ->setCellValue('E1','预计入库数量')
                        ->setCellValue('F1','实际入库数量')
                        ->setCellValue('G1','入库时间')
                        ->setCellValue('H1','送货方');
            $i=2;
            foreach($stocks as $v)
            {
                $increase = $v->increase == Stock::IS_NOT_INCREASE ? "出库" :"入库";
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A'.$i, $increase)
                            ->setCellValue('B'.$i, $v->material->name)
                            ->setCellValue('C'.$i, $v->storeroom->name)
                            ->setCellValue('D'.$i, $v->owners->english_name)
                            ->setCellValue('E'.$i, $v->forecast_quantity)
                            ->setCellValue('F'.$i, $v->actual_quantity)
                            ->setCellValue('G'.$i, $v->stock_time)
                            ->setCellValue('H'.$i, $v->delivery);
                            $i++;
            }
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel;charset=utf-8');
            header('Content-Disposition: attachment;filename='.($filename."库存报告.xls").'');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }
    }
    /**
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = new Stock;
        $id = $_GET['id'];
        if($id){
            $model = $this->loadModel($id);
            //update StockTotal
            if (!empty($_POST)) {
                //update StockTotal
                if($model->actual_quantity != $_POST['Stock']['actual_quantity']){
                    if($model->actual_quantity > $_POST['Stock']['actual_quantity']){
                        StockTotal::updateTotal($model->storeroom_id,$model->material_id,($_POST['Stock']['actual_quantity'] - $model->actual_quantity));
                    }else{
                        StockTotal::updateTotal($model->storeroom_id,$model->material_id,($model->actual_quantity - $_POST['Stock']['actual_quantity']));
                    }
                }
                if ($model->load($_POST) && $model->save()) {
                    Yii::$app->session->setFlash('success', '修改成功!');
                    return $this->redirect(Yii::$app->request->getReferrer());
                }
            }
        }
        return $this->render('create',['model'=>$model,'isNew'=>false]);
    }
    public function actionSearch(){
        $model = new Stock(['scenario'=>'search']);
        $dataProvider = [];
        if(isset($_POST['Stock'])){
            $model->load($_POST);
            if($model->validate()){
                $searchModel = new StockSearch;
                $dataProvider = $searchModel->searchByPost($_POST['Stock']['material_id'],$_POST['Stock']['increase']);
            }
        }
        return $this->render("search",['model'=>$model,'dataProvider'=>$dataProvider]);
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Stock::findOne($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    public function actionDestory(){
        $model = new Stock(['scenario'=>'destory']);
        if (isset($_POST['Stock'])) {
            $model->load($_POST);
            if ($model->validate()) {
                $stock = Stock::find()->where(['material_id'=>$_POST['Stock']['material_id']])->orderby(['id'=>SORT_DESC])->one();
                $model->owner_id = $stock->owner_id;
                $model->increase = Stock::IS_NOT_INCREASE;
                $model->actual_quantity = 0 - $_POST['Stock']['actual_quantity'];
                $model->stock_time = date('Y-m-d H:i:s');
                $model->save();
                //create a data in stock total
                StockTotal::updateTotal($model->storeroom_id,$model->material_id,$model->actual_quantity);
                Yii::$app->session->setFlash('success', '成功销毁物料，库存已经相应减少');
                // $this->redirect("/stock/list");
            }
        }
        return $this->render('destory',['model'=>$model]);
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
