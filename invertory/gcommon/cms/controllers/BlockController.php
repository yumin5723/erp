<?php
namespace gcommon\cms\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use gcommon\components\GController;
use gcommon\cms\models\Block;
use gcommon\cms\models\search\BlockSearch;
use gcommon\cms\components\ConstantDefine;
use backend\models\Upload;
class BlockController extends GController {
    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public $enableCsrfValidation;
    public function actionIndex() {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        return $this->render('index');
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
     * Lists all Block models.
     * @return mixed
     */
    public function actionAdmin()
    {
        $searchModel = new BlockSearch;
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
        $model = new Block;
        $types = ConstantDefine::getBlockType();
        if(isset($_POST['choosetype'])){
            $type = $types[$_POST['Block']['type']];
            return $this->render($type,array("block"=>$model,"type"=>$_POST['Block']['type'],"isNew"=>true));
        }
        if (isset($_POST['Block'])) {
            $model->load($_POST);
            if ($model->validate()) {
                if($model->saveBlock(Yii::$app->user->id)){
                    Yii::$app->session->setFlash('success', '新建成功！');
                    $this->redirect("/cms/block/admin");
                }
            }
        }
        return $this->render('new', array(
            'block' => $model,"types"=>$types,"isNew"=>true
        ));
    }
    /**
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $types = ConstantDefine::getBlockType();
        // collect user input data
        if (isset($_POST['Block'])) {
            $model->attributes = $_POST['Block'];
            if($model->updateBlock(Yii::$app->user->id)){
                Yii::$app->session->setFlash('success', '修改成功！');
                $this->redirect("/cms/block/update?id={$id}");
            }
        }
        return $this->render($types[$model->type], array(
            'block' => $model,"type"=>$model->type,'isNew'=>false,
        ));
    }

    /**
     * build block html
     */
    public function actionBuild($id) {
        $block = $this->loadModel($id);
        $block->updateHtml();
        Yii::$app->session->setFlash('success', '获取内容成功');
        $this->redirect(Yii::$app->request->referrer);
        // $this->redirect("/cms/block/admin");
    }

    public function actionDelete($id){
        GxcHelpers::deleteModel('Block', $id);  
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Block::findOne((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
    /**
     * block update history
     */
    public function actionHistory($id){
        $records = BlockBackup::model()->getAllUpdateRecordsById($id);
        $this->render("history",array("records"=>$records));
    }
    /**
     * block update history
     */
    public function actionViewhistory($id){
        $backup = BlockBackup::model()->findByPk($id);
        $this->render("view",array("backup"=>$backup));
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
    /**
     * Lists all Block models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new BlockSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}
