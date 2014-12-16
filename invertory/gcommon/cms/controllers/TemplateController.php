<?php
namespace gcommon\cms\controllers;

use Yii;
use gcommon\cms\models\Template;
use gcommon\cms\models\search\TemplateSearch;
use gcommon\cms\models\PageTerm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use gcommon\components\GController;
use gcommon\cms\components\CmsTasks;
use gcommon\cms\components\ConstantDefine;


class TemplateController extends GController {
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
        $searchModel = new TemplateSearch;
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
        $model = new Template;
        $types = ConstantDefine::getTempleteType();
        // collect user input data
        if (isset($_POST['Template'])) {
            $model->load($_POST);
            if ($model->validate()) {
                if ($model->saveRarFile($_FILES)) {
                    $model->admin_id = Yii::$app->user->id;
                    $model->save();
                    (new CmsTasks())->parseTemplete($model->id);
                    Yii::$app->session->setFlash('success', '新建成功！');
                }
            }
        }
        return $this->render('create', array(
            'model' => $model,
        ));
    }
    /**
     * Displays the create page
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        // collect user input data
        if (isset($_POST['Template'])) {
            $model->load($_POST);
            if(!empty($_FILES['Template']['name']['upload'])){
                $model->saveRarFile($_FILES);
            }
            $model->modified_id = Yii::$app->user->id;
            $model->content = $_POST['Template']['content'];
            $model->save(false);
            if(!empty($_FILES['Template']['name']['upload'])){
                (new CmsTasks())->parseTemplete($model->id);
            }
            Yii::$app->session->setFlash('success', '修改成功！');
        }
        return $this->render('update', array(
            'model' => $model
        ));
    }
    public function actionDelete($id){
        GxcHelpers::deleteModel('Templete', $id);  
    }
    /**
     * add batch publish content whose use this templete task
     */
    public function actionBatchtask($id){
        $content_ids = Templete::model()->publishAllContents($id);
        (new CmsTasks())->batchContentPublish($content_ids);
        Yii::$app->session->setFlash('success', '批量发布任务添加成功');
        $this->redirect("admin");
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Template::findOne((int)$id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        
        return $model;
    }
}
