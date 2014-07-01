<?php

namespace customer\controllers;

use Yii;
use backend\models\Material;
use backend\models\Stock;
use customer\models\search\MaterialSearch;
use customer\models\search\StockSearch;
use customer\components\CustomerController;
use backend\models\Upload;

class MaterialController extends CustomerController {
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
     * [actionDetail description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionDetail(){
        $searchModel = new StockSearch;
        $dataProvider = $searchModel->searchDetail(Yii::$app->request->getQueryParams());

        return $this->render('detail', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
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
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $material = Material::findOne($id);
        return $this->render('view', [
            'material' => $material,
        ]);
    }
    /**
     * Displays the create page
     */
    // public function actionUpdate($id) {
    //     $model = new Material;
    //     $id = $_GET['id'];
    //     if($id){
    //         $model = $this->loadModel($id);
    //         if (!empty($_POST)) {
    //             if ($model->load($_POST) && $model->save()) {
    //                 Yii::$app->session->setFlash('success', '修改成功!');
    //                 return $this->redirect(Yii::$app->request->getReferrer());
    //             }
    //         }
    //     }
    //     return $this->render('create',['model'=>$model,'isNew'=>false]);
    // }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Material::findOne($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
//     public function actionUploadfile(){
//         $this->enableCsrfValidation = false;
//         $num = $_POST['num'];
//         // print_r($_FILES);exit;
//         if($_FILES){
//             $model = new Upload;
//             $result = $model->uploadImage($_FILES,false,"picture");
//             if($result[0] == true){
//                     echo <<<EOF
//             <script>parent.stopSend("{$num}","{$result[1]}");</script>
// EOF;
//             }else{
//                 echo <<<EOF
//             <script>alert("{$result[1]}");</script>
// EOF;
//             }
//         }
//     }
}
